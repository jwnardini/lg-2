<footer class="content-info">
	<div class="container">
		<div class="row">
			<div class="col col-md-6">
				<?php if ( is_active_sidebar( 'sidebar-footer' ) ) : ?>
            		<?php dynamic_sidebar( 'sidebar-footer' ); ?>
        		<?php endif; ?>
				<a href="<?php echo site_url(); ?>/about/contact" class="btn contact-us">Contact Us</a>
				<?php
				if (has_nav_menu('footer_navigation')) :
					wp_nav_menu(['theme_location' => 'footer_navigation']);
				endif;
				?>
				<div class="copyright">&copy; Copyright <?php echo date("Y") ?> Lighting Global. All rights reserved.</div>
			</div>
			<div class="col col-md-6">
				<a class="brand" href="<?= esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
			</div>
		</div>
	</div>
</footer>

<script>
	if (self == top) {
		var theBody = document.getElementsByTagName('body')[0];
		theBody.style.display = "block";
	} else {
		top.location = self.location;
	}
</script>
