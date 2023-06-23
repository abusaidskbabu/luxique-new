<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Models\OrderAutoRenewal;
use App\Models\User;
use App\Models\OrderDetails;
use App\Models\Order;
use App\Models\Product;
use App\Models\Admins;
use Carbon\Carbon;
use DB;
use Auth;

use Illuminate\Http\Request;

class OrderAutoRenewalController extends Controller
{
    public function orderAutoRenewalSync(){
        $renewableOrders = OrderAutoRenewal::whereDate('next_order_date', '=', date('Y-m-d'))->where('status',1)->get();
        $results = [];
        foreach($renewableOrders as $order){
            $results[] = $this->_placeOrder($order->order_id);
        }

        foreach($results as $result){
           if( $result['status'] == 1){
                $order_id = $result['order_id'];
                $renewalOrder = OrderAutoRenewal::where('order_id', $order_id)->first();
                $nextDate = date('Y-m-d H:i:s', strtotime($renewalOrder->next_order_date . ' +'.$renewalOrder->renewal_cycle.' day'));
                $order_renewal = OrderAutoRenewal::where('order_id', $order_id)->first();
				$order_renewal->next_order_date = $nextDate;
				$order_renewal->save();
				$reponseOrderId[] =  $order_id;
           }
		}

		return redirect()->route('admin.index')->with('success', 'Auto renewal service worker has been run successfully! Please check Auto renewal dashboard for further details.');

    }

    private function _placeOrder($order_id){
            $order = Order::find($order_id);
            $user = User::find($order->user_id);
            $orderDetails = OrderDetails::where('order_id',$order_id)->get();
            $user_id = $user->id;

            $packaging_cost = 0;
            $security_charge = 0;
            $discount_amount = 0;
            $sub_total = 0;

			$orderData['discount_amount'] = $discount_amount;
			$orderData['paid_amount'] = 0;
			$orderData['order_from'] = 'web-auto-renewal';
			$orderData['coupon_code'] = null;
			$orderData['coupon_amount'] = 0;
			$orderData['voucher_code'] = null;
			$orderData['voucher_amount'] = 0;
			$orderData['payment_method'] = $order->payment_method;
			$orderData['status'] = 1;
			$orderData['user_id'] = $user_id;
			$orderData['address_id'] = $order->address_id;
			$orderData['payment_id'] = uniqid();
			$orderData['ip_address'] = request()->ip();
			$orderData['note']    = 'This order is auto generated by system.';

			$orderData['total_packaging_cost']  = $packaging_cost;
			$orderData['total_security_charge'] = $security_charge;

			$new_order_id = DB::table('orders')->insertGetId($orderData);

			$totalShippingCost = 0;
			$grocery_total_price = 0;
			$shipping_cost_grocery = 0;

           

			//Insert Order details
			foreach($orderDetails as $single_item){
				$product = Product::find($single_item->product_id);

                $product_price = \Helper::price_after_offer($single_item->product_id);

				$detailsData['user_id'] = $user_id;
				$detailsData['order_id'] = $new_order_id;
				$detailsData['product_id'] = $single_item->product_id;
				
				$detailsData['product_sku'] = $single_item->product_sku;

				$detailsData['product_qty'] = $single_item->product_qty;

				$detailsData['price'] = $product_price;

				$detailsData['is_promotion'] = $single_item->is_promotion;
				$detailsData['loyalty_point'] = $single_item->loyalty_point;

                $detailsData['shipping_method'] = $single_item->shipping_method;
				$detailsData['shipping_cost'] = $single_item->shipping_cost;
				$detailsData['product_options'] = $single_item->product_options;

				$detailsData['seller_id'] = $single_item->seller_id;
				$orderData['payment_method'] = $single_item->payment_method;

				$detailsData['packaging_cost'] = $product->packaging_cost  ?? 0;
				$detailsData['security_charge'] = $product->security_charge  ?? 0;

					
				if($orderDetailsId = DB::table('order_details')->insertGetId($detailsData)){
					if($order->payment_method == 'cash_on_delivery'){
						\Helper::update_product_quantity($single_item->product_id, $single_item->qty, $single_item->product_options,'subtraction');
					}

					//Set Seller Commission
					\Helper::setSellerBalance($orderDetailsId);

				}else{
					$data['status'] = 0;
                    $data['order_id'] = $order_id;
					$data['message'] = 'Something went wrong.';
					return $data;
				}


                if($product->is_grocery == 'grocery' ){
					$grocery_total_price += $product->price * $single_item->qty;
				}

               
               
                if($product->product_type == 'simple' || $product->product_type == 'digital' || $product->product_type == 'service'){

					$price = \Helper::price_after_offer($single_item->product_id);
					$qty = \Helper::simple_product_stock($single_item->product_id, $single_item->product_qty);

					if($qty == 1){
						$sub_total += $price*$single_item->product_qty;
						$packaging_cost += $product->packaging_cost*$single_item->product_qty;
						$security_charge += $product->security_charge*$single_item->product_qty;

					}else{
						$data['status'] = 0;
                        $data['order_id'] = $order_id;
						$data['message'] = 'Product out of stock.';
						return $data;
					}
				}

                

				if($product->product_type == 'variable'){
					if ($single_item->product_options) {
						$availableOptions = \Helper::variable_product_stock($single_item->product_id, $single_item->qty, json_decode($single_item->product_options));
						if($availableOptions){
							$price = \Helper::price_after_offer($single_item->product_id) + $availableOptions['totalAdditional'];
							$sub_total += $price*$single_item->qty;

                            $packaging_cost += $product->packaging_cost*$product->qty;
						    $security_charge += $product->security_charge*$single_item->qty;
						}
					}
				}
			}

           

			if($grocery_total_price > 0){
				$settingAmountForGrocery = \Helper::getsettings('shipping_validation_amount') ?? 500;
				$minimumShippingAmount = \Helper::getsettings('shipping_minimum_amount') ?? 30;
				$maximumShippingAmount = \Helper::getsettings('shipping_maximum_amount') ?? 50;
	
				if($grocery_total_price >= $settingAmountForGrocery ){
					$shipping_cost_grocery = $minimumShippingAmount;
				}else{
					$shipping_cost_grocery = $maximumShippingAmount;
				}
			}


			$totalShippingCost = $order->shipping_cost - $order->grocery_shipping_cost;

			$paid_amount = $sub_total+$totalShippingCost+$shipping_cost_grocery+$packaging_cost+$security_charge;
			
			DB::table('orders')->where('id',$new_order_id)->update([
				'shipping_cost' => $totalShippingCost + $shipping_cost_grocery,
				'grocery_shipping_cost' => $shipping_cost_grocery ,
				'paid_amount'	=> $paid_amount,
				'parent_order_id' => $order_id
			]);

			
			//Send Email
			$order = Order::find($new_order_id);
			
			if(request()->server('HTTP_HOST') != '127.0.0.1:8000'){
                if($email = $user->email){
                    \Helper::sendEmail($email,'Order Placed',$order,'invoice');
                }
            }
			

			//Send Push notifications
				//Customer
	            $message = [
					'order_id' =>  'KB'.date('y',strtotime(Carbon::now())).$new_order_id,
					'type' =>'order',
					'message' =>'Order placed successfully!',
	            ];

				//Seller
	            $sellers = DB::table('order_details')->select('seller_id')->where('order_id', $new_order_id)->get();
	            $sellers_id_for_order = array();

				foreach ($sellers as $row) {
					$sellers_id_for_order[$row->seller_id] = $row->seller_id; // Get unique seller by id.
				}
				
				foreach ($sellers_id_for_order as $key => $val) {
					$seller = Admins::find($val);
					\Helper::sendPushNotification($val,2,'Order Placed','Order placed successfully!',json_encode($message));
					\Helper::sendSms($seller->phone,'একটি নতুন অর্ডার সফলভাবে স্থাপন করা হয়েছে! অর্ডার আইডি হল  #'.'KB'.date('y',strtotime(Carbon::now())).$order_id);
				}
				
				// Customer
				\Helper::sendSms($user->phone,'অর্ডার সফলভাবে স্থাপন করা হয়েছে! আপনার অর্ডারের জন্য আপনাকে ধন্যবাদ. আপনার অর্ডার আইডি #'.'KB'.date('y',strtotime(Carbon::now())).$order_id);


                $data['status'] = 1;
                $data['order_id'] = $order_id;
                $data['message'] = 'Auto renewal order has been placed successfully!';
                return $data;

    }


	public function orderAutoRenewal(Request $request){
		$data = [];
		$order_id = $request->order_id;
		$user_id = Order::find($order_id)->user_id;
		$renewal_cycle = $request->renewal_cycle;


		if(Order::find($order_id)->where('user_id',$user_id)->exists()){

			if(OrderAutoRenewal::where('order_id',$order_id)->where('user_id',$user_id)->exists()){
				$OrderAutoRenewal = OrderAutoRenewal::where('order_id',$order_id)->first();
				$OrderAutoRenewal->renewal_cycle = $request->renewal_cycle;
				$OrderAutoRenewal->status = $request->status;
				$OrderAutoRenewal->next_order_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' +'.$request->renewal_cycle.' day'));
				$update = $OrderAutoRenewal->save();

			}else{
				$OrderAutoRenewal = new OrderAutoRenewal();
				$OrderAutoRenewal->order_id = $request->order_id;
				$OrderAutoRenewal->renewal_cycle = $request->renewal_cycle;
				$OrderAutoRenewal->next_order_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' +'.$request->renewal_cycle.' day'));
				$OrderAutoRenewal->user_id = $user_id;
				$OrderAutoRenewal->status = $request->status;
				$update = $OrderAutoRenewal->save();
			}

	
			if($update){
				$data['status'] = 1;
				$data['message'] = 'Thank you for activate order auto renewal service!';
				return response()->json($data, 200);	
			}else{
				$data['status'] = 0;
				$data['message'] = 'Something went wrong! Please try again later.';
				return response()->json($data, 200);	
			}
		}else{
			$data['status'] = 0;
			$data['message'] = 'Order not found!';
			return response()->json($data, 200);	
		}

		
	}


}
