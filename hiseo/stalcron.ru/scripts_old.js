function formPosition(form) {
	var top =  window.pageYOffset || document.documentElement.scrollTop;
	//alert($(form).outerHeight());
	if ($(window).outerHeight() < $(form).outerHeight()) {
		top += 20;
	}
	else {
		top += $(window).outerHeight()/2-$(form).outerHeight()/2;
	}
	$(form).css({'top':top,	 'margin-left':($(window).width()-$(form).outerWidth())/2});								
}


function heightContentSidebar() {
	if ( $(window).outerWidth() > 979 ) {	
		  var content = $('.box_catalog .col_product');
		  var sidebar = $('.box_catalog .col_filter');
		  var getContentHeight = content.outerHeight();
		  var getSidebarHeight = sidebar.outerHeight();
		  
		  if (getContentHeight > getSidebarHeight) {
		    sidebar.css('min-height', getContentHeight);
		    }
		  if (getSidebarHeight > getContentHeight) {
		    content.css('min-height', getSidebarHeight);
		    }
	}		    
}
jQuery(document).ready(function($) {
    heightContentSidebar();
});


$(function(){
	$('.but-a_error').click(function(e){
		e.preventDefault();
		formPosition('.popup-site_error');
		$('.popup-site_error').fadeIn(800);
		$('.popup_bg').fadeIn(800);
	})	
			
						
	$('.box_category .item_category a, .but-zayav').click(function(e){
		e.preventDefault();
		formPosition('.popup-zayav');
		$('.popup-zayav').fadeIn(800);
		$('.popup_bg').fadeIn(800);
	})	
					
	$('.but-zvonok').click(function(){
		formPosition('.popup-zvonok');
		$('.popup-zvonok').fadeIn(800);
		$('.popup_bg').fadeIn(800);
       
	})
						
	$('.but-reg').click(function(){
		formPosition('.popup-reg');
		$('.popup-reg').fadeIn(800);
		$('.popup_bg').fadeIn(800);
       
	})				
	$('.but-auth').click(function(){
		formPosition('.popup-auth');
		$('.popup-auth').fadeIn(800);
		$('.popup_bg').fadeIn(800);
       
	})					
	$('.but-auth_reg').click(function(){
		formPosition('.popup-auth');
		$('.popup-reg').fadeOut(800);
		$('.popup-auth').fadeIn(800);
       
	})	
	
	
	$('.popup_bg, .popup .close').click(function(){
		$('.popup').fadeOut(500);
		$('.popup_bg').fadeOut(500);
	})
	
	
	$('.menu_toggler').on('click', function(e) {
		e.preventDefault();
		$(this).toggleClass('active');
		$('.bl_nav').toggleClass('active');
		$('body').toggleClass('noscroll');
	});	
    $('.bl_nav .close').on('click', function(e) {
		$(this).toggleClass('active');
		$('.bl_nav').toggleClass('active');
		$('body').toggleClass('noscroll');
	});	
	
	
	
	$(".slider_about").slick({
        dots: true,
        arrows: false,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 8000,
        speed: 300,
        slidesToShow: 1
    });
    $(".slider_sert").slick({
	  	dots: true,
	  	arrows: false,
	  	infinite: true,
	  	speed: 500,
        slidesToShow: 3,
        variableWidth: false,
        adaptiveHeight: true,
		responsive: [
			{
			      breakpoint: 768,
			      settings: {
				        slidesToShow: 3
			      }
			},
			{
			      breakpoint: 640,
			      settings: {
				        slidesToShow: 2
			      }
			}
		] 
	});
    
    $(".fancy_img").fancybox();
    $("input[name=phone]").mask("+7(999)999-99-99");
   
	
	$('.top_filter .col_filter .item').on('click', function(e) {
    	$(this).parent().children('.item').removeClass('active');
		$(this).addClass('active');
	});	
    $('.top_filter .open_filter').on('click', function(e) {
		$(this).toggleClass('opened').next('.var_filter').slideToggle(300);
	});	
	
	$('.category_prays .item_categ').on('click', function(e) {
    	$(this).parent().children('.item_categ').removeClass('active');
		$(this).addClass('active');
	});	
	
    $('.table_prays .bl_lom .title_lom').on('click', function(e) {
		$(this).toggleClass('opened').next('.var_lom').slideToggle(300);
	});	
    $('.table_prays .item_var .but-roll').on('click', function(e) {
		$(this).parents('.item_var').toggleClass('opened').find('.desc').slideToggle(300);
	});	
	
	
    $('.postav_prays .bl_lom .title_lom').on('click', function(e) {
		$(this).toggleClass('opened').next('.var_lom').slideToggle(300);
	});	
    $('.postav_prays .item_var .but-roll').on('click', function(e) {
		$(this).parents('.item_var').toggleClass('opened').find('.desc').slideToggle(300);
	});	
	
	
	$('.box_catalog .bl_category li').click(function(e){
		$(this).toggleClass('active');
	})
	
	
					
	$('.account_order_item .order_check').click(function(){
       $(this).toggleClass('active').parents('.row').toggleClass('active')
	})	
	
	
	var box_prays_head = $('.box_prays .head_table').outerHeight();
	var last_scroll = 0;
	$(window).scroll(function(){
        var top_scroll = window.pageYOffset || document.documentElement.scrollTop;
        if (top_scroll > last_scroll) 
	        if ( (top_scroll > 0))	{
				$('header').addClass('fixed');
			}
		if (top_scroll < last_scroll)	
			if ( (top_scroll < 300) ) {
				$('header').removeClass('fixed');
			}
		last_scroll = top_scroll;
		
		
		if ( $(window).outerWidth() < 640 ) {
	    	
	        var top_scroll = window.pageYOffset || document.documentElement.scrollTop;
			
	        $('.scroll_active').each(function( index ) {
			  	var top_item = $(this).offset().top,
				  	height_item = $(this).outerHeight();
			  	if ( (top_scroll+$(window).outerHeight()/2-height_item/2 < top_item+height_item/3) &&
				   	 (top_scroll+$(window).outerHeight()/2+height_item/2 > top_item+height_item/3) ) {
					
					$(this).addClass('active');
				}
				else {
					$(this).removeClass('active');
				}
			});
		} 
		
		var box_prays = $('.table_prays').offset().top,
			box_prays_height = $('.box_prays').offset().top + $('.box_prays').outerHeight(),
			header_fixed = $('header.fixed').outerHeight();
			header_fixed = 0;
        if ( (top_scroll+header_fixed-50 > box_prays) && (top_scroll+header_fixed+100 < box_prays_height) )	{
        	//$('.table_prays').css('padding-top',box_prays_head);
			$('.box_prays .head_table').addClass('fixed').css({'top':header_fixed});
			$('.box_prays .head_table .line_table').css({'width':$('.box_prays').outerWidth()});
		}
		else {
        	//$('.table_prays').css('padding-top','0');
			$('.box_prays .head_table').removeClass('fixed').css({'top':'auto'});
			$('.box_prays .head_table .line_table').css({'width':'100%'});
		}
		
    });
    
});



(function($) {
$(function() {
	$('.bl_filtr_sidebar input, .styler').styler({
		selectSearch: true
	});
	
	
});
})(jQuery);

