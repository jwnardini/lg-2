<?php get_template_part('templates/page', 'header'); ?>

<p>We can't find the page you're looking for. To find it, you can use our search (available at the top of each page), or explore our sitemap.</p>
<p>You may find what you're looking for within our new site by starting at one of the following pages:</p>

<?php $args = array(
	'menu' => 'primary',
    'theme_location' => 'primary_navigation',
    'depth' => 1 
    );
?>
<?php wp_nav_menu($args); ?>
