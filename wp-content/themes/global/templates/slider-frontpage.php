<section id="slider">

	<?php

	$slides = new WP_Query( [ 'post_type' => 'slide', 'posts_per_page' => 10, 'orderby' => 'menu_order', 'order' => 'ASC' ] );

	$slides_array = [];

	while ( $slides->have_posts()) :

		$slides->the_post();

		$title          = get_the_title();

		//$gradients      = 'repeating-linear-gradient( 45deg, #0f0f0f 0%, transparent 60%, transparent 100%), repeating-linear-gradient( -90deg, #0f0f0f 0%, transparent 55%, transparent 100%),';


		$text = get_the_content();

		array_push( $slides_array, [ 'title' => $title, 'text' => $text, 'image' => get_the_post_thumbnail_url( get_the_ID(), 'large' ) ] );

		if ( $slides->current_post == 0 ):
	?>
		<div class="slide">

			<style>
				@media screen and ( min-width: 768px ) {
					.slide { 
						background: #000000;
						background-image: url('<?php the_post_thumbnail_url(); ?>'); 
					}
				}
			</style>

			<div class="container">
				<div class="text">
					<h1><?php echo $title; ?></h1>
					<p><?php echo $text; ?></p>
					
					<ul class="slider-controls">

						<?php 
							for ( $i = 0; $i < $slides->post_count; $i++ ) {
								$classes = $i == 0 ? 'active' : null;
								
								echo "<li class='$classes' id='slide$i' tabindex='0' onkeydown='LG.slider.keyHandler(event)'></li>";
							}
						?>

					</ul>
				</div>
			</div>

		</div>
	<?php
		endif;

	endwhile; 

	wp_reset_postdata();
	?>

	<script>

		LG.slider = {
			data : <?php echo json_encode( $slides_array ); ?>
		}

		LG.slider.keyHandler = function(e) {
			if( e.keyCode == 13 ) {
				e.stopPropagation();
				e.preventDefault();
				
				e.target.click();
			}
		}

		LG.slider.change = function(e) {

			if ( e.target.id == '' ) return;

			Array.prototype.forEach.call( document.querySelectorAll( ".slider-controls li" ), function( node ) {
				node.className = '';
			});

			if ( e.target !== e.currentTarget ) {
				var n = e.target.id.slice( -1 );

				document.getElementById( e.target.id ).className += ' active'; 

				document.querySelector( '.slide' ).style.backgroundImage = "url('" + LG.slider.data[n].image + "')";
				document.querySelector( '.slide .text h1' ).innerHTML = LG.slider.data[n].title;
				document.querySelector( '.slide .text p' ).innerHTML = LG.slider.data[n].text;

			}
		}

		LG.slider.data.theControls = document.querySelector( ".slider-controls" );
		LG.slider.data.theControls.addEventListener( "click", LG.slider.change, false);
		 
	</script>
	<?php get_template_part('templates/latestnews', 'frontpage'); ?>

</section>