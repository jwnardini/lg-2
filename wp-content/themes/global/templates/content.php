<article <?php post_class(); ?>>
	
	
	<?php if(has_post_thumbnail()) { ?>
		<div class="col-md-2 thumbnail">
			<?php the_post_thumbnail('thumbnail'); ?>
		</div>
	    <div class="col-md-10 article-content">
  	<?php } else { ?>
  		<div class="article-content">
  	<?php } ?>
	  <header>
	    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
	    <?php get_template_part('templates/entry-meta'); ?>
	  </header>
	  <div class="entry-summary">
	    <?php the_excerpt(); ?>
	  </div>
	</div>
</article>
