jQuery(document).ready(function($) {
    $("input[name=quantity]").blur(function(event) {
        single_product_price_change()
    });;
    $("input[name=quantity]").keyup(function(event) {
        single_product_price_change()
    });; 
    $("input[name=quantity]").change(function(event) {
        single_product_price_change()
    });; 

    $("#type").change(function(event) {
     setTimeout(function() {
      single_product_price_change()

     }, 50);
        
    });;

    function single_product_price_change() {
        var quantity = $("input[name=quantity]").val();
        if (dealer_volume_discount.type == 'percentage_discount') {
            var discount = (dealer_volume_discount.value / 100) * wc_price_calculator_params.product_price;
        } else if (dealer_volume_discount.type == 'price_discount') {
            var discount = dealer_volume_discount.value;
        }
        var finaltot = ((wc_price_calculator_params.product_price * quantity) - (discount * quantity));
        console.log(finaltot)
        $('.total_price').html(accounting.formatMoney(finaltot, {
            symbol: wc_price_calculator_params.woocommerce_currency_symbol,
            decimal: wc_price_calculator_params.woocommerce_price_decimal_sep,
            thousand: wc_price_calculator_params.woocommerce_price_thousand_sep,
            precision: wc_price_calculator_params.woocommerce_price_num_decimals,
            format: "%s%v"
        }));
    }
    single_product_price_change();
});

