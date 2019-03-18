<?php while (have_posts()) : the_post(); ?>
	<article <?php post_class(); ?>>

		<div class="container">
			<div class="row">
				<div class="col col-sm-8">
					<header>
						<div class="featured-image">
							<?php the_post_thumbnail( 'medium' ); ?>
						</div>
						<h1 class="entry-title"><?php the_title(); ?></h1>

						<?php
							$id      = get_the_ID();
							$rows    = [];

							$options = get_option( 'product-data-settings' );

							foreach ( $options['data-points'] as $datapoint ) {

								$title  = $datapoint['title'];
								$slug   = strtolower( str_replace( ' ', '-', $title ) );

								$table  = array_key_exists( 'table', $datapoint );
								$table  = $table ? $datapoint['table'] : $table;

								if ( $table ) {
								    $terms = wp_get_post_terms( $id, $slug );

								    if( count( $terms ) ) {

								        $rows[$title] = '';

								        foreach ( $terms as $term ) {
								            $rows[$title] .= $term->name . ', ';
								        }

								        $rows[$title] = trim( $rows[$title], ', ' );

								    } else {
								        $rows[$title] = false;
								    }

								}

							}

							//$manufacturer = Product_Data::get_manufacturer( $id );



						 ?>

							<!-- if ( $meta['manufacturer'] ) {

								echo '<p><strong>Manufacturer: </strong> ' . $meta['manufacturer'] . '</p>';
							} -->

						<?php

						$terms = get_the_terms( get_the_ID(), 'manufacturers' );

						if ( $terms && ! is_wp_error( $terms ) ) :

						    $manufacturers = array();

						    foreach ( $terms as $term ) {
						        $manufacturers[] = $term->name;
						    }

						    $manufacturer_list = join( ", ", $manufacturers );

							endif;
						    ?>

						  	<p>
						        <strong>Manufacturer(s):</strong>
										<?php printf( esc_html__( '%s' ), esc_html( $manufacturer_list ) );

											//echo facetwp_display( 'facet', 'manufacturers' );

										?>
						    </p>
						<?php //endif; ?>



						<?php

							$meta = array(
								//'manufacturer' => $manufacturer,
								'model'        => get_post_meta( $id, 'model', 1 ),
								'warranty'     => get_post_meta( $id, 'lg-additional_warranty', 1 ),
								'other'        => get_post_meta( $id, 'lg-other_information', 1 ),
								'expiration'   => get_post_meta( $id, 'lg-expiration_date', 1 ),
							);

							if ( $meta['model'] ) {
								echo '<p><strong>Model: </strong> ' . $meta['model'] . '</p>';
							}
						?>
					</header>
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
					<div class="additional-information">

							<table width="100%" class="alt-table-style">
								<tr>
									<th colspan="2">Additional Information</th>
								</tr>

							<?php

								if ( $meta['other'] ) {
									$rows['Other Information'] = $meta['other'];
								}

								if ( $meta['warranty'] ) {
									$rows['Warranty Information'] = $meta['warranty'];
								}

								if ( $meta['expiration'] ) {
									$date = new DateTime( $meta['expiration'] );
									$rows['Results Expiration Date'] = $date->format('F jS, Y');
								}

								foreach ( $rows as $key => $value ) {
									if ( $value ) {
										echo '<tr><td valign="top"><strong>' . $key . ':</strong></td><td>' . $value . '</td></tr>';
									}
								}
							?>

							</table>

						<?php if ( Product_Data::is_associate( $id ) ) { ?>

							<?php

							$terms = get_the_terms( get_the_ID(), 'manufacturers' );

							if ( $terms && ! is_wp_error( $terms ) ) :

							    $manufacturers = array();

							    foreach ( $terms as $term ) {
							        $manufacturers[] = $term->name;
							    }

							    $manufacturer_list = join( ", ", $manufacturers );
							    ?>

							  	<p>
							       <?php printf( esc_html__( '%s' ), esc_html( $manufacturer_list ) ); ?> is an Associate.
							    </p>

							<?php endif; ?>

							<p><a href="/work-with-us/associate-services/"> Click here</a> to learn about our Associate offerings.</p>
						<?php } ?>
					</div>
				</div>
				<div class="col col-sm-4">
					<section class="pdf-downloads">
						<?php

							$pdf_groups = get_post_meta( $id, 'product-pdfs' );

							if ( is_array( $pdf_groups ) && count( $pdf_groups[0][0] ) > 0 ) {
								$pdf_groups = $pdf_groups[0];

								echo '<h1>PDF Downloads</h1>';
								echo '<ul>';

								foreach ( $pdf_groups as $pdf_group ) {

									if ( isset( $pdf_group['title'] ) )
										echo '<h2>' . $pdf_group['title'] . '</h2>';

									foreach ( $pdf_group['pdfs'] as $pdf_id => $pdf_link ) {
										echo '<li><a href="' . $pdf_link . '" target="_blank">' . get_the_title( $pdf_id ) . '</a></li>';
									}
								}

								echo '</ul>';
							}

						?>
					</div>
				</div>
			</div>
		</div>
		<footer>
			<?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>
		</footer>
		<?php comments_template('/templates/comments.php'); ?>
	</article>
<?php endwhile; ?>
