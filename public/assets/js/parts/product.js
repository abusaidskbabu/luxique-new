$(document).on('click', '.color_image', function() {
    //e.stopImmediatePropagation();
    $show_img = $(this).attr('src');
    $color_selectd = $(this).data('title');
    $('.selectedColor').text($color_selectd);
    $(".selectedColor").attr('data-selectedColor', $color_selectd);
    $('#big-img').attr("src", $show_img);
    $('#show-img').attr("src", $show_img);
    $('.zoom-show').attr("src", $show_img);
    $('#big-img').css({ 'background': '#fff' });
    $('[id=\'big-img\']').css({ 'background': '#fff' });
    $('[id=\'big-img\']').attr("src", $show_img);
    $(this).closest('.option_parent_group').find('.color_image').css({ 'border': '0' });
    $(this).css({ 'border': '3px solid #2ca8e3' });
});


$(document).on('click', '.option_selector_every', function(){
    var product_id = $(this).attr('data-productid');
    
    let additionalPrice = Number($(this).data('price'));
    let additionalQty = Number($(this).data('variable-qty'));
    let additionalSku = $(this).data('variable-sku');
    let selectedTitle = $(this).data('title');
    let parentElement = $(this).closest('.option_parent_group');
    parentElement.find('.variant_input'+product_id).val(selectedTitle);
    parentElement.find('.variant_input'+product_id).attr('data-additional-price', additionalPrice);
    parentElement.find('.variant_input'+product_id).attr('data-additional-qty', additionalQty);
    parentElement.find('.variant_input'+product_id).attr('data-additional-sku', additionalSku);
    parentElement.find('.selectedValue'+product_id).html(selectedTitle);
    parentElement.find('.option_selector'+product_id).removeClass('option_selected');
    $(this).addClass('option_selected');
    changePrice(product_id);
    // alert(selectedTitle);
});


$(document).on('change', '.variant_dropdwon_every', function() {
    var product_id = $(this).attr('data-productid');
   // alert('product_id = '+product_id);
    let additionalPrice = Number($(this).find('option:selected').data('dropdownprice'));
    let additionalQty = Number($(this).find('option:selected').data('variable-qty'));
    let additionalSku = $(this).find('option:selected').data('variable-sku');
    let selectedTitle = $(this).find('option:selected').val();
    let parentElement = $(this).closest('.option_parent_group');
    parentElement.find('.variant_input'+product_id).val(selectedTitle);
    parentElement.find('.variant_input'+product_id).attr('data-additional-price', additionalPrice);
    parentElement.find('.variant_input'+product_id).attr('data-additional-qty', additionalQty);
    parentElement.find('.variant_input'+product_id).attr('data-additional-sku', additionalSku);
    parentElement.find('.selectedValue').html(selectedTitle);
    changePrice(product_id);
   
});

function changePrice(product_id) {
    //alert(product_id);
    let originalPrice = Number($('.calculated_price').data('calculated-price'));
    let additionalPrice = 0;
    $('.variant_input'+product_id).each(function(key, val) {
        additionalPrice += Number($(this).attr('data-additional-price'));
    });
    let calculatedPrice = originalPrice + additionalPrice;
    let isSubmitAble = true;
    console.log('calculatedPrice-1 = '+calculatedPrice);
    if(calculatedPrice < 0){
        isSubmitAble = false;
    }
    $('.price_text').text(calculatedPrice);
    $('.qty').attr('data-qty', 1);
    $('.qty').text(1);
    $('.qtyInput').val(1);
    let skuHtml = '';

    $('.variant_input'+product_id).each(function() {
        let currentQty = $(this).attr('data-additional-qty');
        skuHtml = skuHtml.concat(" ", $(this).attr('data-additional-sku'));
        jQuery('.variable_sku').val(skuHtml);
        console.log('currentQty = '+currentQty);
        if (currentQty == -1) {
            isSubmitAble = false;
        }
    });

    if (isSubmitAble) {
        $('.single_page_sku').html(skuHtml);

        $('.disabledbtn'+product_id).removeClass('disabled');
        $('.disabledbtn'+product_id).removeAttr('disabled');
        $('.disabledbtn'+product_id).attr('type', 'submit');
    }else{
        $('.disabledbtn'+product_id).addClass('disabled');
        $('.disabledbtn'+product_id).attr('disabled',true);
        $('.disabledbtn'+product_id).attr('type', 'button');
    }
}












$(document).on('click', '.change_location', function() {
    $('.pickup_location').removeAttr('readonly');
    $('.pickup_location').css({ 'border': '1px solid #cfcfcf;', 'background': '#fbfbfb' });
    return false;
});
$(".pickup_location").keypress(function(event) {
    event.stopImmediatePropagation();
    if (event.key === "Enter") {
        $('.pickup_location').attr('readonly', 'true');
        $('.pickup_location').css({ 'border': '0', 'background': 'rgb(247 247 247)' });
    }
});
$(document).on('mouseleave', '.delivery', function() {
    $('.pickup_location').attr('readonly', 'true');
    $('.pickup_location').css({ 'border': '0', 'background': 'rgb(247 247 247)' });
    return false;
});






// single products page  
/*
$(document).on('click', '.variable_product_single_page .option_selector', function(){
    let additionalPrice = Number($(this).data('price'));
    let additionalQty = Number($(this).data('variable-qty'));
    let additionalSku = $(this).data('variable-sku');
    let selectedTitle = $(this).data('title');
    let parentElement = $(this).closest('.variable_product_single_page .option_parent_group');
    parentElement.find('.variable_product_single_page .variant_input').val(selectedTitle);
    parentElement.find('.variable_product_single_page .variant_input').attr('data-additional-price', additionalPrice);
    parentElement.find('.variable_product_single_page .variant_input').attr('data-additional-qty', additionalQty);
    parentElement.find('.variable_product_single_page .variant_input').attr('data-additional-sku', additionalSku);
    parentElement.find('.variable_product_single_page .selectedValue').html(selectedTitle);
    parentElement.find('.variable_product_single_page .option_selector').removeClass('option_selected');
    $(this).addClass('option_selected');
    changePrice();
});


$(document).on('change', '.variable_product_single_page .variant_dropdwon', function() {
    let additionalPrice = Number($(this).find('option:selected').data('dropdownprice'));
    let additionalQty = Number($(this).find('option:selected').data('variable-qty'));
    let additionalSku = $(this).find('option:selected').data('variable-sku');
    let selectedTitle = $(this).find('option:selected').val();
    let parentElement = $(this).closest('.option_parent_group');
    parentElement.find('.variable_product_single_page .variant_input').val(selectedTitle);
    parentElement.find('.variable_product_single_page .variant_input').attr('data-additional-price', additionalPrice);
    parentElement.find('.variable_product_single_page .variant_input').attr('data-additional-qty', additionalQty);
    parentElement.find('.variable_product_single_page .variant_input').attr('data-additional-sku', additionalSku);
    parentElement.find('.variable_product_single_page .selectedValue').html(selectedTitle);
    changePrice();
});

function changePrice() {
    let originalPrice = Number($('.variable_product_single_page .calculated_price').data('calculated-price'));
    let additionalPrice = 0;
    $('.variable_product_single_page .variant_input').each(function(key, val) {
        additionalPrice += Number($(this).attr('data-additional-price'));
    });
    let calculatedPrice = originalPrice + additionalPrice;
    let isSubmitAble = true;
    console.log('calculatedPrice-1 = '+calculatedPrice);

    if(calculatedPrice < 0){
        isSubmitAble = false;
    }
    $('.variable_product_single_page .price_text').text(calculatedPrice);
    $('.variable_product_single_page .qty').attr('data-qty', 1);
    $('.variable_product_single_page .qty').text(1);
    $('.variable_product_single_page .qtyInput').val(1);
    let skuHtml = '';

    $('.variable_product_single_page .variant_input').each(function() {
        let currentQty = $(this).attr('data-additional-qty');
        skuHtml = skuHtml.concat(" ", $(this).attr('data-additional-sku'));
        jQuery('.variable_product_single_page .variable_sku').val(skuHtml);
		console.log('currentQty = '+currentQty);
        if (currentQty == -1) {
            isSubmitAble = false;
        }
    });


    console.log('isSubmitAble last = '+isSubmitAble);

    if (isSubmitAble || isSubmitAble == true) {
        $('.single_page_sku').html(skuHtml);
        $('#variable_addtocart_button').removeClass('disabled');
        $('#variable_addtocart_button').removeAttr('disabled');
        $('#variable_addtocart_button').attr('type', 'submit');
        $('#variable_addtocart_button').prop('disabled', false);
		// alert('success');
    }else{
        $('#variable_addtocart_button').addClass('disabled');
        $('#variable_addtocart_button').attr('disabled',true);
        $('#variable_addtocart_button').attr('type', 'button');
    }
}
*/