		</main>
	</div>
</div>

<?php get_template_part('templates/slider', 'frontpage'); ?>

<?php //while (have_posts()) : the_post(); ?>
  <?php //get_template_part('templates/content', 'page'); ?>
<?php //endwhile; ?>


<div class="container">
	<section id="our-impact">

	<h1>Our Impact</h1>

	<a href="<?php echo site_url(); ?>/about/our-impact" class="view-all">View our Impact</a>
	<br>


		<div class="col-sm-4">
			<?php echo get_field('homepage_col1'); ?>
		</div>

		<div class="col-sm-4">
			<?php

			$image1 = get_field('col2_graphic');
			$col2_link = get_field( 'col2_link' );

			if( !empty($image1) ): ?>
				<div class="image">
					<?php if ( $col2_link ) { ?>
						<a href="<?php echo esc_attr( $col2_link ); ?>"><img src="<?php echo $image1['url']; ?>" alt="<?php echo $image1['alt']; ?>" /></a>
					<?php } else { ?>
						<img src="<?php echo $image1['url']; ?>" alt="<?php echo $image1['alt']; ?>" />
					<?php } ?>
				</div>


			<?php endif; ?>
			<strong><?php echo get_field('homepage_col2_title'); ?></strong>
			<?php echo get_field('homepage_col2'); ?>
			<div class="info"><i></i> <span><?php echo get_field('col2_tooltip'); ?></span></div>
		</div>

		<div class="col-sm-4">
			<?php

			$image2 = get_field('col3_graphic');
			$col3_link = get_field( 'col3_link' );

			if( !empty($image2) ): ?>
				<div class="image">
					<?php if ( $col3_link ) { ?>
						<a href="<?php echo esc_attr( $col3_link ); ?>"><img src="<?php echo $image2['url']; ?>" alt="<?php echo $image2['alt']; ?>" /></a>
					<?php } else { ?>
						<img src="<?php echo $image2['url']; ?>" alt="<?php echo $image2['alt']; ?>" />
					<?php } ?>
				</div>


			<?php endif; ?>
			<strong><?php echo get_field('homepage_col3_title'); ?></strong>
			<?php echo get_field('homepage_col3'); ?>
			<div class="info"><i></i> <span><?php echo get_field('col3_tooltip'); ?></span></div>

		</div>

	</section>
</div>

<?php get_template_part('templates/products', 'frontpage'); ?>

<section id="associate-program">
	<div class="container">
		<h2>Regional Programs</h2>

		<div class="row">
			<div class="col-md-7">

				<?php

				$associate_image = get_field('associate_graphic');

				if( !empty($associate_image) ): ?>

					<img src="<?php echo $associate_image['url']; ?>" alt="<?php echo $associate_image['alt']; ?>" />

				<?php endif; ?>

				<?php echo get_field('associate_program'); ?>

			</div>

			<ul class="countries col-md-5">
		 		<li class="africa"><a href="<?php echo site_url(); ?>/where-we-work/lighting-africa/">Lighting <b>Africa</b><i></i></a></li>
		 		<li class="asia"><a href="<?php echo site_url(); ?>/where-we-work/lighting-asia/">Lighting <b>Asia</b><i></i></a></li>
			</ul>
		</div>
	</div>
</section>

<div class="container"></div>
