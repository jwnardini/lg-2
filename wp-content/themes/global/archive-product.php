<div class="col-md-3 facet-filter">
	<div class="page-header">
		<h1 class="toggle">Filter By</h1>
	</div>
	<div class="facets">

		<?php
			$options = get_option( 'product-data-settings' );

			if ( ! $options || ! array_key_exists( 'data-points', $options ) )
				$options = [ 'data-points' => [] ];

			foreach ( $options['data-points'] as $datapoint ) {

				$title  = $datapoint['title'];
				$slug   = strtolower( str_replace( ' ', '_', $title ) );

				$filter = array_key_exists( 'filter', $datapoint );

				$filter = $filter ? $datapoint['filter'] : $filter;

				if ( $filter ) { ?>
					<div class="facet">
						<h2><?php echo $title; ?></h2>
						<?php echo facetwp_display( 'facet', $slug ); ?>
					</div>

				<?php
				}
			}

		?>
		<div class="facet">
			<h2>Manufacturers</h2>
			<?php echo facetwp_display( 'facet', 'manufacturers' ); ?>
		</div>
		<div class="facet">
			<h2>Show LG Associates</h2>
			<?php echo facetwp_display( 'facet', 'associate' ); ?>
		</div>
	</div>
</div>

<div class="col-md-9 facets-container products-container">
	<div class="page-header">
		<h1>Products</h1>
		<p><?php echo get_page_by_title( 'products' )->post_content; ?></p>

		<div class="facets-sorter">
			<strong>Sort by</strong>
			<?php echo facetwp_display( 'sort', 'product_type' ); ?>
		</div>

		<div class="products-downloader">
			<a class='toggle-pdf-links' href=""><span>+</span> Click here to show Verification Letters</a>
		</div>
	</div>

	<ul class="products facetwp-template">
	<?php

		$familydescription = get_page_by_path( 'product-family', OBJECT, 'page' );

		if ( $familydescription ) {
			$familydescription = $familydescription->post_content;
		}

		while ( have_posts() ) : the_post(); ?>
		<li>
			<a href="<?php the_permalink(); ?>">
				<div>
					<?php
						the_post_thumbnail( 'medium' );

						if ( Product_Data::is_associate( $id ) )
							echo '<span class="associate">Associate</span>';
					?>
				</div>
				<h3><?php the_title(); ?></h3>


				<?php

				$terms = get_the_terms( get_the_ID(), 'manufacturers' );

				if ( $terms && ! is_wp_error( $terms ) ) :

				    $manufacturers = array();

				    foreach ( $terms as $term ) {
				        $manufacturers[] = $term->name;
				    }

				    $manufacturer_list = join( ", ", $manufacturers );
				    ?>

				  	<p><?php printf( esc_html__( '%s' ), esc_html( $manufacturer_list ) ); ?></p>
				<?php endif; ?>

				<?php

					if ( Product_Data::is_family( $id ) )
						echo '<span class="family">Product Family <i><div>' . $familydescription . '</div></i></span>';
					else
						echo '<span class="family"></span>';
				?>
			</a>

			<?php

				// '1' returns the single value. An array in this case if it exists or an empty string if not.
				$pdf_groups = get_post_meta( get_the_ID(), 'product-pdfs', 1 );

				if ( is_array( $pdf_groups ) && count( $pdf_groups ) > 0 ) :
					$pdf_group = $pdf_groups[0];

					$count = 0;

					if ( count( $pdf_groups ) > 1 ) {

						foreach ( $pdf_groups as $group ) {
							$count += count( $group['pdfs'] );
						}

						$more = '<li><a href="' . get_the_permalink() . '" target="_blank">+ See All ' . $count . ' PDFs</a></li>';
					} else {
						$more = '';
					}

					$counttext = $count ? ' [' . $count . ' total]' : '';

					echo '<ul class="pdfs"><strong>PDF Links:' . $counttext . '</strong>';

						foreach ( $pdf_group['pdfs'] as $pdf_id => $pdf_link ) {
							echo '<li><a href="' . $pdf_link . '" target="_blank">' . get_the_title( $pdf_id ) . '</a></li>';
						}

						echo $more;


					echo '</ul>';

				endif;

			?>
		</li>
	<?php endwhile; ?>
	</ul>
	<div class="products-pager">
		<?php echo facetwp_display( 'pager' ); ?>
	</div>
</div>
