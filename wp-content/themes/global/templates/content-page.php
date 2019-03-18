<?php the_content(); ?>
<?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>

<?php
	if ( class_exists( 'Resource_CPT' ) ) {
		Resource_CPT::render_related_resources( get_the_id() );
	}
?>