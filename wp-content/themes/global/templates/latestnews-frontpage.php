<aside id="latest-news">
	<h2>Latest News</h2>

<?php 

$latest_posts = new WP_Query( [ 'post_type' => 'post', 'posts_per_page' => 2 ] ); ?>

<ul>
<?php
	while ( $latest_posts->have_posts()) : $latest_posts->the_post();
	?>

		<li>
			<?php 
				if ( has_post_thumbnail() ) :
					the_post_thumbnail();
				endif;
			?>

			<?php the_date( 'M d Y', '<date>', '</date>' ); ?>
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</li>

	<?php

	endwhile; 

	wp_reset_postdata();
?>
</ul>

<a href="/news" class="read-more">Read all news</a>

</aside>