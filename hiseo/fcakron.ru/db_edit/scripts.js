var last_scroll = 0;

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
	/* 
	// география поставок +- для разворачивания адреса
    // нужно усложнить, чтобы все остальные захлопывались
    // знаем объект клики. у него должен быть артибут маркер (marker="01")
    */
	$('.box_geograf_main .item_city .name').click(function(){
        let marker = $(this).attr('marker');            // запоминаем маркер
        let acttiv = $(this).hasClass('active');         // активность элемента
		let sel = '.map_new[marker="' + marker + '"]';  // вычисляем селектор карты
		console.log(marker);


        $('.box_geograf_main .item_city .name').removeClass('active');
        $('.item_city .desc').css('display','none');          // все описания закрываем
        //$('.map_new').removeClass('active');             // все карты гасим
		$('.map').attr('src',"images/map_geograf.png");  // все карты гасим

        // проверка на активность
        // если активен то описание и карту закрываем и делаем неактивным (может только один активен)
        if (acttiv) {
			console.log('активен гасим');
            $(this).removeClass('active');                                // отключаем активность
			$('.map img').attr('src',"images/map_geograf.png");  // все карты гасим
            $(this).next('.desc').fadeOut();            //слайдирует следующий за ним тег чьлбы осписание увидеть
        } else {
			console.log('пассивен зажигаем');
            $(this).addClass('active');                                // отключаем активность
			$('.map img').attr('src',"images/map_geograf_" + marker + ".png");  // открываем нужную карту
//            $(sel).addClass('active');                       // открываем нужную карту
            $(this).next('.desc').fadeIn();            //слайдирует следующий за ним тег чьлбы осписание увидеть
        }
	})
	
	
	$('.but-all_contact').click(function(){
		$(this).slideToggle();
		$('.hidden_personal[data-contact='+$(this).attr('data-contact')+']').slideToggle();
	})
    
    // копка каталога посмотреть всё
	$('.but-all_catalog').click(function(){
		$(this).slideToggle();          //плавно скрываем кнопку при нажатии
		$('.item_catalog').fadeIn();    // включаем все элементы каталога
	})

	// категории металлов
	$('.nav-tab-catalog li').click(function(){
        console.log("=================");
		$('.nav-tab li').removeClass('active');
		$(this).addClass('active');
		$('.item_catalog').fadeOut();
		$('.item_catalog[data-tab=' + $(this).attr('data-tab') + ']').fadeIn();
        
        $('.button_line[but=all]').fadeIn(); // включить кнопку "Показать всё"
	})
	
	$('.nav-tab-sklad li').click(function(){
        console.log("=============++++++++======");
		$('.nav-tab li').removeClass('active');
		$(this).addClass('active');
		$('.tab-info').fadeOut();
		$('.tab-info[data-tab='+$(this).attr('data-tab')+']').fadeIn();
	})
	
	
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
	$(".slider_doc").slick({
	  	dots: true,
	  	arrows: false,
	  	infinite: true,
	  	speed: 500,
        slidesToShow: 3,
        variableWidth: false,
        adaptiveHeight: true,
		responsive: [
			{
			      breakpoint: 980,
			      settings: {
				        slidesToShow: 2
			      }
			},
			{
			      breakpoint: 400,
			      settings: {
				        slidesToShow: 1
			      }
			}
		] 
	});
	
	$(".slider_partner").slick({
	  	dots: true,
	  	arrows: false,
	  	infinite: true,
	  	speed: 500,
        slidesToShow: 5,
        variableWidth: false,
        adaptiveHeight: true,
		responsive: [
			{
			      breakpoint: 980,
			      settings: {
				        slidesToShow: 4
			      }
			},
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
   
	
	

	$(window).scroll(function(){
        var top_scroll = window.pageYOffset || document.documentElement.scrollTop;
        if (top_scroll > last_scroll) 
	        if ( (top_scroll > 50))	{
				$('header').addClass('fixed');
			}
		if (top_scroll < last_scroll)	
			if ( (top_scroll < 50) ) {
				$('header').removeClass('fixed');
			}
		last_scroll = top_scroll;
		
		
    });
    
});



(function($) {
$(function() {
	$('.bl_filtr_sidebar input, .styler').styler({
		selectSearch: true
	});
	
	
});
})(jQuery);

