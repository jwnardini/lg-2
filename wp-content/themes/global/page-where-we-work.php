<?php while (have_posts()) : the_post(); ?>
	<?php get_template_part('templates/page', 'header'); ?>
	<?php get_template_part('templates/content', 'page'); ?>

	<style>
			header.banner:after {
				background-image: url( <?php echo get_the_post_thumbnail_url( get_the_ID(), 'medium' ); ?> );
			}

			@media screen and ( min-width: 500px ) {
				header.banner:after {
					background-image: url( <?php echo get_the_post_thumbnail_url(); ?> );
				}
			}
	</style>
<?php endwhile; ?>
