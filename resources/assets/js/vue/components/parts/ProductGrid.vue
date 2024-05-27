<template>
  <div typeof="schema:Product" class="product" :itemprop="'itemListElement'">
    <div class="product-image">
      <div class="product_badge" v-if="data.offer_percentage > 0">
        <span>-{{ data.offer_percentage }}%</span>
      </div>
      <div v-if="data.shop_verified.if_verified" class="product_badge_flagship">
        <span>
          <img :src="`${baseurl}/${data.shop_verified.verified_banner}`" alt="Flagship">
        </span>
      </div>
      <router-link :to="{ name: 'product', params: { slug: data.slug } }">
        <img :src="`${baseurl}/${data.default_image}`" alt="">
      </router-link>
    </div>
    <div class="product-details">
      <div v-if="parseInt(data.price_after_offer.replace(/,/g, '')) === parseInt(data.price.replace(/,/g, ''))" class="offer_gap"></div>
      <router-link :to="{ name: 'product', params: { slug: data.slug } }">
        <p property="schema:name" class="elipsis_title">{{ data.title }}</p>
      </router-link>
      <div class="product-title">
        <div typeof="schema:Offer" class="price">
          <ul>
            <li>
              <div property="schema:price" class="now-price">BDT {{ data.price_after_offer }}</div>
            </li>
            <li>
              <div class="old-price">
                <del v-if="parseInt(data.price_after_offer.replace(/,/g, '')) < parseInt(data.price.replace(/,/g, ''))">BDT {{ data.price }}</del>
              </div>
            </li>
          </ul>
        </div>
        <div v-if="data.product_type == 'variable' || data.product_type == 'service'" class="text-center variable_details">
          <div class="row">
            <ul class="hover_icon_group icon_group">
              <li><i class="fa fa-heart" @click="addToWishlist(data.id)" aria-hidden="true"></i></li>
              <li><i class="fa fa-eye" :data-modal="data.id" data-toggle="modal" :data-target="'#quickViewModal' + data.id" aria-hidden="true"></i></li>
              <li><i class="fa fa-retweet" @click="addToCompare(data.id)" aria-hidden="true"></i></li>
            </ul>
            <div class="col-12">
              <router-link :to="{ name: 'product', params: { slug: data.slug } }" :class="'add_to_cart disabledbtn' + data.id">
                <i class="fa fa-info-circle"></i>{{ $t('Details') }}
              </router-link>
            </div>
          </div>
        </div>
        <div v-else class="row p-0">
          <span v-if="data.in_stock > 0 && data.qty > 0" style="width: 90%; margin: 0 auto;">
            <div class="row all-efecive-btns">
              <ul class="hover_icon_group icon_group">
                <li><i class="fa fa-heart" @click="addToWishlist(data.id)" aria-hidden="true"></i></li>
                <li><i class="fa fa-eye" :data-modal="data.id" data-toggle="modal" :data-target="'#quickViewModal' + data.id" aria-hidden="true"></i></li>
                <li><i class="fa fa-retweet" @click="addToCompare(data.id)" aria-hidden="true"></i></li>
              </ul>
              <div class="col-6 pr-1 add_to_cart_area">
                <a :class="'add_to_cart disabledbtn' + data.id" @click.prevent="addToCart(data.id)">
                  {{ $t('Add To Cart') }}
                </a>
              </div>
              <div class="col-6 pl-1 buy_now_area">
                <a :class="'buy_now buynowdisabledbtn' + data.id" @click.prevent="buyNow(data.id)">
                  {{ $t('Buy Now') }}
                </a>
              </div>
            </div>
          </span>
          <span v-else>
            <div class="row">
              <ul class="hover_icon_group icon_group">
                <li><i class="fa fa-heart" @click="addToWishlist(data.id)" aria-hidden="true"></i></li>
                <li><i class="fa fa-eye" :data-modal="data.id" data-toggle="modal" :data-target="'#quickViewModal' + data.id" aria-hidden="true"></i></li>
                <li><i class="fa fa-retweet" @click="addToCompare(data.id)" aria-hidden="true"></i></li>
              </ul>
            </div>
            <div class="col-md-12 text-center">
              <a class="out_of_stock">{{ $t('Out Of Stock') }}</a>
            </div>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      baseurl: '',
      thumbnailUrl: '',
      calculated_in_stock: true,
      calculated_price: '',
      discount_Percentage: '',
      AllRating: ''
    };
  },
  props: ['data'],
  computed: {
    schemaPrice() {
      return `BDT ${this.data.price_after_offer}`;
    },
    schemaOldPrice() {
      return `BDT ${this.data.price}`;
    }
  },
  methods: {
    // imageLoadError(event) {
    //   event.target.src = '/images/notfound.png';
    // },
    addToCompare(product_id) {
      const session_key = localStorage.getItem('session_key');
      const token = localStorage.getItem('token');
      const axiosConfig = {
        headers: {
          'Content-Type': 'application/json;charset=UTF-8',
          'Access-Control-Allow-Origin': '*',
          Authorization: `Bearer ${token}`
        }
      };
      this.product_id = product_id;
      axios.post(`${this.baseurl}/api/v1/add-to-compare`, { product_id, session_key }, axiosConfig)
        .then((response) => {
          if (response.data.status === 1) {
            this.checkCompare = 1;
            $('.alreadycompared').css({ color: '#c7c7c7' });
            this.$store.dispatch('loadedCompares');
            swal({
              title: 'Successfully added to your compare list.',
              icon: 'success',
              timer: 1000
            });
          } else {
            swal('Sorry', response.data.message, 'error');
          }
        });
    },
    addToWishlist(product_id) {
      const session_key = localStorage.getItem('session_key');
      const token = localStorage.getItem('token');
      const axiosConfig = {
        headers: {
          'Content-Type': 'application/json;charset=UTF-8',
          'Access-Control-Allow-Origin': '*',
          Authorization: `Bearer ${token}`
        }
      };
      this.product_id = product_id;
      axios.post(`${this.baseurl}/api/v1/add-to-wishlist`, { product_id, session_key }, axiosConfig)
        .then((response) => {
          if (response.data.status === 1) {
            this.$store.dispatch('loadedWishlist');
            swal({
              title: 'Successfully added to your wishlist.',
              icon: 'success',
              timer: 1000
            });
          } else {
            swal('Sorry', response.data.message, 'error');
          }
        });
    },
    addToCart(product_id) {
      $(`.disabledbtn${product_id}`).attr('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
      const session_key = localStorage.getItem('session_key');
      const token = localStorage.getItem('token');
      const axiosConfig = {
        headers: {
          'Content-Type': 'application/json;charset=UTF-8',
          'Access-Control-Allow-Origin': '*',
          Authorization: `Bearer ${token}`
        }
      };
      const lang = localStorage.getItem('lang');
      axios.post(`${this.baseurl}/api/v1/add-to-cart`, { product_id, qty: 1, session_key }, axiosConfig)
        .then((response) => {
          if (response.data.status === 1) {
            this.$store.dispatch('loadedCart');
            $('.back_to_cart').trigger('click');
            swal({
              title: 'Product added to cart Successfully.',
              icon: 'success',
              timer: 1000
            }).then(() => {
              $(`.disabledbtn${product_id}`).attr('disabled', false).html(lang === 'bn' ? 'যুক্ত করুন' : 'Add To Cart');
            });
          } else {
            swal('Oops', response.data.message, 'error');
            $(`.disabledbtn${product_id}`).attr('disabled', false).html(lang === 'bn' ? 'যুক্ত করুন' : 'Add To Cart');
          }
        });
    },
    buyNow(product_id) {
      $(`.buynowdisabledbtn${product_id}`).attr('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
      const session_key = localStorage.getItem('session_key');
      const token = localStorage.getItem('token');
      const axiosConfig = {
        headers: {
          'Content-Type': 'application/json;charset=UTF-8',
          'Access-Control-Allow-Origin': '*',
          Authorization: `Bearer ${token}`
        }
      };
      const lang = localStorage.getItem('lang');
      axios.post(`${this.baseurl}/api/v1/add-to-cart`, { product_id, qty: 1, session_key }, axiosConfig)
        .then((response) => {
          if (response.data.status === 1) {
            this.$store.dispatch('loadedCart');
            $('.back_to_cart').trigger('click');
            swal({
              title: 'Product added to cart Successfully.',
              icon: 'success',
              timer: 1000
            }).then(() => {
              $(`.buynowdisabledbtn${product_id}`).attr('disabled', false).html(lang === 'bn' ? 'এখন কিনুন' : 'Buy Now');
              $('.show_checkout_section').trigger('click');
              $('.left_cart_icon').trigger('click');
            });
          } else {
            swal('Oops', response.data.message, 'error');
            $(`.buynowdisabledbtn${product_id}`).attr('disabled', false).html(lang === 'bn' ? 'এখন কিনুন' : 'Buy Now');
          }
        });
    },
    variableAddToCart(product_id) {
      $(`.disabledbtn${product_id}`).attr('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
      const token = localStorage.getItem('token');
      const session_key = localStorage.getItem('session_key');
      const axiosConfig = {
        headers: {
          'Content-Type': 'application/json;charset=UTF-8',
          'Access-Control-Allow-Origin': '*',
          Authorization: `Bearer ${token}`
        }
      };
      const formData = new FormData(this.$refs.variable_form);
      const data = { session_key };
      for (const [key, val] of formData.entries()) {
        Object.assign(data, { [key]: val });
      }
      axios.post(`${this.baseurl}/api/v1/variable-add-to-cart`, data, axiosConfig)
        .then((response) => {
          if (response.data.status === '1') {
            this.$store.dispatch('loadedCart');
            $('.back_to_cart').trigger('click');
            swal({
              title: 'Product added to cart Successfully.',
              icon: 'success',
              timer: 1000
            }).then(() => {
              $(`.disabledbtn${product_id}`).attr('disabled', false).html('<i class="fa fa-shopping-basket"></i> Add To Cart');
            });
          } else {
            $(`.disabledbtn${product_id}`).attr('disabled', false).html('<i class="fa fa-shopping-basket"></i> Add To Cart');
            swal('Oops', response.data.message, 'error');
          }
        })
        .catch(() => {
          $(`.disabledbtn${product_id}`).attr('disabled', false).html('<i class="fa fa-shopping-basket"></i> Add To Cart');
          swal('Oops', 'Something went wrong. Please try again later!', 'error');
        });
    },
    digitaladdToCart(product_id) {
      $(`.disabledbtn${product_id}`).attr('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
      const user = this.$store.getters.getLoadedUser.user;
      const session_key = localStorage.getItem('session_key');
      const token = localStorage.getItem('token');
      const axiosConfig = {
        headers: {
          'Content-Type': 'application/json;charset=UTF-8',
          'Access-Control-Allow-Origin': '*',
          Authorization: `Bearer ${token}`
        }
      };
      const qty = $('.digital_product_qty').attr('data-qty');
      const phone_number = $('.phone_number').val();
      if (phone_number === -1 || phone_number.length === 11) {
        axios.post(`${this.baseurl}/api/v1/digital-add-to-cart`, { shipping_option: '', phone_number, session_key, product_id, qty }, axiosConfig)
          .then((response) => {
            if (response.data.status === '1') {
              this.$store.dispatch('loadedCart');
              $('.back_to_cart').trigger('click');
              swal({
                title: 'Product added to cart Successfully.',
                icon: 'success',
                timer: 1000
              }).then(() => {
                $(`.disabledbtn${product_id}`).attr('disabled', false).html('Add To Cart');
              });
            } else {
              $(`.disabledbtn${product_id}`).attr('disabled', false).html('Add To Cart');
              swal('Oops', response.data.message, 'error');
            }
          });
      } else {
        $(`.disabledbtn${product_id}`).attr('disabled', false).html('Add To Cart');
        swal('Oops', 'Please type a valid mobile number.', 'error');
      }
    },
    calculated_functions(data) {
      const start = new Date(data.special_price_start);
      const end = new Date(data.special_price_end);
      const diffTime = Math.abs(start - end);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      if (data.special_price_type === 1) {
        if (diffTime > 0) {
          this.calculated_price = data.special_price;
          const discount = data.price - data.special_price;
          this.discount_Percentage = parseFloat((discount / data.price) * 100).toFixed(2);
        } else {
          this.calculated_price = data.price;
        }
      }
      if (data.special_price_type === 2) {
        if (diffTime > 0) {
          const discount = (data.special_price / 100) * data.price;
          const price = data.price - parseFloat(discount).toFixed(2);
          this.calculated_price = parseFloat(price).toFixed(2);
          this.discount_Percentage = parseFloat(data.special_price).toFixed(0);
        } else {
          this.calculated_price = data.price;
        }
      }
      // axios.get(`${this.baseurl}/api/v1/get-all-rating/${data.id}`).then((response) => {
      //   this.AllRating = response.data;
      // });
    }
  },
  mounted() {
    this.baseurl = this.$baseUrl;
    this.thumbnailUrl = this.$thumbnailUrl;
    this.calculated_functions(this.$props.data);
  }
};
</script>
