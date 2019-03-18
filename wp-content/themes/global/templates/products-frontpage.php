<section id='featured-products'>
	<div class="container">
		<h1>Quality Verified Products</h1>
		<a class="view-all" href="/products">View all products</a>
	</div>

	<div class="products-container">
		<div class="container">


		<?php

		$products = new WP_Query( [ 'post_type' => 'product', 'posts_per_page' => 12, 'orderby' => 'rand' ] );

		$products_array = [];

		?>

		<ul>
			<li class="control prev" tabindex="0" onkeydown="LG.products.keyHandler(event)"></li>
		<?php
			while ( $products->have_posts()) : $products->the_post();

				$title        = get_the_title();
				$link         = get_the_permalink();
				$manufacturer = Product_Data::get_manufacturer( get_the_ID() );
				$image        = get_the_post_thumbnail_url( get_the_ID(), 'small' );

				array_push( $products_array, [ 'title' => $title, 'link' => $link, 'manufacturer' => $manufacturer, 'image' => $image ] );

				if ( $products->current_post < 4 ):
			?>

					<li class="product">
						<a href="<?php the_permalink(); ?>">
							<?php

								if ( has_post_thumbnail() ) :
									the_post_thumbnail( 'small' );
								else :
									echo '<img src="">';
								endif;
							?>
							<div>
								<strong><?php echo $title; ?></strong>
								<span><?php echo $manufacturer; ?></span>
							</div>
						</a>
					</li>

			<?php
				endif;

			endwhile;

			wp_reset_postdata();
		?>
			<li class="control next" tabindex="0" onkeydown="LG.products.keyHandler(event)"></li>
		</ul>

		<script>
			LG.products = {
				position : 0,
				data     : <?php echo json_encode( $products_array ); ?>
			};

			LG.products.keyHandler = function(e) {
				if( e.keyCode == 13 ) {
					e.stopPropagation();
					e.preventDefault();

					e.target.click();
				}
			}

			LG.products.change = function(e) {
				if ( e.target.classList.contains( 'next' ) )
					LG.products.position++;
				else
					LG.products.position--;

				LG.products.position = LG.products.position < 0 ? LG.products.position = 2 : LG.products.position % 3;
				LG.products.nodes = document.querySelectorAll( "#featured-products .product" );

				Array.prototype.forEach.call( LG.products.nodes, function( node, i ) {
					var ii = LG.products.position * 4 + i;

					node.querySelector( 'img' ).src          = LG.products.data[ii].image;
					node.querySelector( 'img' ).srcset       = LG.products.data[ii].image;
					node.querySelector( 'strong' ).innerHTML = LG.products.data[ii].title;
					node.querySelector( 'span' ).innerHTML   = LG.products.data[ii].manufacturer;
					node.querySelector( 'a' ).href           = LG.products.data[ii].link;
				});

			}

			LG.products.theControls = document.querySelectorAll( "#featured-products .control" );

			Array.prototype.forEach.call( LG.products.theControls, function( node ) {
				node.addEventListener( "click", LG.products.change, false);
			});
		</script>
		</div>
	</div>

</section>
