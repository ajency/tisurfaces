(function($){
	"use strict";

	function get_cart() {
		if (window.wc_add_to_cart_params != undefined) {
			$.post({
				url: wc_add_to_cart_params.ajax_url,
				dataType: 'JSON',
				data: {action: 'woomenucart_ajax'},
				success: function(data, textStatus, XMLHttpRequest) {
					$('.uncode-cart-dropdown').html(data.cart);
					if (data != '') {
						if ($('.uncode-cart .badge, .mobile-shopping-cart .badge').length) {
							if (data.articles > 0) {
								$('.uncode-cart .badge, .mobile-shopping-cart .badge').html(data.articles);
								$('.uncode-cart .badge, .mobile-shopping-cart .badge').show();
							} else {
								$('.uncode-cart .badge, .mobile-shopping-cart .badge').hide();
							}
						} else $('.uncode-cart .cart-icon-container').append('<span class="badge">'+data.articles+'</span>'); //$('.uncode-cart .badge').html(data.articles);
					}
				}
			});
		}
	}

	function change_images(event, variation) {
		var getSeletcedImgSrc = variation.image.src;
		$('a.woocommerce-main-image').attr('href',getSeletcedImgSrc).attr('data-title',name).attr('title',name).children('img').attr('src',getSeletcedImgSrc);
		if (variation.image_link !== '') {
			var get_href = $('a[href="'+variation.image_link+'"]'),
			image_variable = $('img', get_href),
			getLightbox = UNCODE.lightboxArray[get_href.data('lbox')];
			get_href.data('options',"thumbnail: '"+variation.image_src+"'");
			if (image_variable.hasClass('async-done')) {
				image_variable.attr('data-path',variation.uncode_image_path);
				image_variable.removeClass('async-done').addClass('adaptive-async');
				UNCODE.adaptive();
			}
			if (getLightbox != undefined) getLightbox.refresh();
		}
		var getSeletcedVariationName = variation.sku;
		var checkThumbLength = $('.woocommerce-images .thumbnails > a').length;
		for(var i=0;i<checkThumbLength;i++){
			var getDataTitle = $('.woocommerce-images .thumbnails > a:eq('+i+')').attr('title');
			if(getSeletcedVariationName == getDataTitle){
				$('.woocommerce-images .thumbnails > a').removeClass('selected-vatiation');
				$('.woocommerce-images .thumbnails > a:eq('+i+')').addClass('selected-vatiation');
			}
		}
		$('div.quantity .total-price-wrap').html('<label>Total Price</label>'+$('#price_calculator .calculated-price > td:eq("1")').html());
	}

	// function change_images(event, variation) {

	// 	if (variation.image_link !== '') {
	// 		var get_href = $('a[href="'+variation.image_link+'"]'),
	// 		image_variable = $('img', get_href),
	// 		getLightbox = UNCODE.lightboxArray[get_href.data('lbox')];
	// 		get_href.data('options',"thumbnail: '"+variation.image_src+"'");
	// 		if (image_variable.hasClass('async-done')) {
	// 			image_variable.attr('data-path',variation.uncode_image_path);
	// 			image_variable.removeClass('async-done').addClass('adaptive-async');
	// 			UNCODE.adaptive();
	// 		}
	// 		if (getLightbox != undefined) getLightbox.refresh();
	// 	}
	// }

	$(document).ready(function() {
		if ($(window).width() > 959) {
			$('#menu-main-menu .mega-menu .mega-menu-inner > li').each(function(){
				var getMegaMenuLi = $(this).children('.drop-menu').children('li').length;
				for(var i=0;i<getMegaMenuLi;i++){
					var getMenuImgSRC = $(this).children('.drop-menu').children('li:eq('+i+')').children('a').attr('title');
					if(getMenuImgSRC.match('.jpg') || getMenuImgSRC.match('.png') || getMenuImgSRC.match('.gif') || getMenuImgSRC.match('.jpeg')){
						$(this).children('.drop-menu').children('li:eq('+i+')').children('a').prepend('<img src="'+getMenuImgSRC+'" alt="'+$(this).children('.drop-menu').children('li:eq('+i+')').children('a').text()+'" />');
					}
				}
			});
		}
		var getThumbLength = $('.woocommerce-images .thumbnails > a').length;
		if(getThumbLength<=1){
			$('.woocommerce-images .thumbnails').addClass('column-1').removeClass('column-3');
		}
		else if(getThumbLength>=1 && getThumbLength<=2){
			$('.woocommerce-images .thumbnails').addClass('column-2').removeClass('column-3');
		}
		$('div.quantity').wrapInner('<div class="quantity-inner" />');
		$('div.quantity .quantity-inner').prepend('<label>Units</label>');
		$('div.quantity').append('<div class="total-price-wrap" />');

		$('body').bind("added_to_cart", get_cart);
		$('body').bind("wc_fragments_refreshed", get_cart);
		$('.variations_form').bind("show_variation", change_images);
	});

})(jQuery);