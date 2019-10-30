<?php
/*
 * Template Name: Карта сайта
 */
get_header();
?>
<section class="container">
	<div class="title_box" <?php if(get_field("title_background")) echo 'style="background-image: url('.get_field("title_background").');"'; ?>>	
		<h1><?php echo get_field("h1_main") ? get_field("h1_main") : 'Карта сайта' ?></h1>
	</div>	
    <!-- Хлебные крошки Yoast -->
    <div class="bread_crumbs">
        <?php
        if ( function_exists( 'yoast_breadcrumb' ) ) :
           yoast_breadcrumb( '<div id="breadcrumbs">', '</div>' );
        endif;
        ?>                
    </div>
</section>
<section class="container">
    <div class="sitemap">
        <?php the_content(); ?>
    </div>
    <div class="sitemap">
            
        <ul>
            <li><a href="https://cvetmet-st-peterburg.akron-holding.ru/">Главная</a></li>
            <li><a href="https://cvetmet-st-peterburg.akron-holding.ru/priem-cvetnyh-metallov/">Цветной лом</a>
                <ul class="children">
                    <li><a href="https://cvetmet-st-peterburg.akron-holding.ru/priem-cvetnyh-metallov/priem-medi/">Медь</a></li>
                    <li><a href="https://cvetmet-st-peterburg.akron-holding.ru/priem-cvetnyh-metallov/priem-svinca/">Свинец</a></li>
                    <li><a href="https://cvetmet-st-peterburg.akron-holding.ru/priem-cvetnyh-metallov/priem-nerzhaveyushchey-stali/">Нержавеющая сталь</a></li>
                    <li><a href="https://cvetmet-st-peterburg.akron-holding.ru/priem-cvetnyh-metallov/priem-alyuminiya/">Алюминий</a></li>
                </ul>
            </li>
            <li><a href="https://cvetmet-st-peterburg.akron-holding.ru/redkozem/">Редкоземельный лом</a></li>
            <li><a href="https://cvetmet-st-peterburg.akron-holding.ru/prays/">Прайс</a></li>
            <li><a href="https://cvetmet-st-peterburg.akron-holding.ru/licenzii/">Лицензии</a></li>
            <li><a href="https://cvetmet-st-peterburg.akron-holding.ru/kontakty/">Пункты приёма</a></li>
        </ul>
    </div> 
    <br>    
</section>

<?php get_footer(); ?>