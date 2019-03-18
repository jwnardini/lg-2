<a class="skip-link screen-reader-text" href="#main">Skip to content</a>

<header class="banner"> 
	<?php if( get_field( 'callout') && is_front_page() ): ?>
		<div class="callout">
			<?php echo get_field('callout'); ?>
		</div>
	<?php endif; ?>
	<div class="status"></div>
	<div class="container">
		<div class="row">
			<div class="col-sm-8 logo">
				<a class="brand" href="<?= esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
			</div>
			<div class="col-sm-4 controls">
				<div class="chinese-page"><a href="/about/chinese/">中文</a></div>
				<div class="menu-toggle" onclick="toggleNav()"><b></b><b></b><b></b></div>
				<?php get_search_form(); ?>
			</div>
		</div>
	</div>
	<nav class="nav-primary">
		<div class="container">
			<?php
			if (has_nav_menu('primary_navigation')) :
				wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']);
			endif;
			?>
		</div>
	</nav>
</header>
