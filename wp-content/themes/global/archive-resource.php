<div class="col-md-3 facet-filter">
	<div class="page-header">
		<h1 class="toggle">Filter By</h1>
	</div>
	<div class="facets">
		<div class="facet">
			<h2>Resource Type</h2>
			<?php echo facetwp_display( 'facet', 'resource_type' ); ?>
		</div>
		<div class="facet">
			<h2>Language</h2>
			<?php echo facetwp_display( 'facet', 'language' ); ?>
		</div>
	</div>
</div>
<div class="col-md-9 facets-container resources-container">
	<div class="page-header">
		<h1>Resources</h1>

		<div class="facets-sorter">
			<strong>Sort by</strong>
			<?php echo facetwp_display( 'sort', 'publication_type' ); ?>
		</div>

	</div>

	<ul class="resources facetwp-template">
	<?php

		while ( have_posts() ) : the_post(); ?>
		<li>
			<article>
				<?php
					$class = '';
					if ( ! has_post_thumbnail() ) {
						$class = 'empty';
					}
				?>
				<div class="<?php echo $class; ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></div>
				<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
				<?php
					if ( class_exists( 'Resource_CPT' ) ) {
						Resource_CPT::render_subtitle( get_the_id() );
					}

					the_excerpt();
				?>
			</article>
		</li>
	<?php endwhile; ?>
	</ul>
	<div class="resources-pager">
		<?php echo facetwp_display( 'pager', 'resource_type' ); ?>
	</div>
</div>
