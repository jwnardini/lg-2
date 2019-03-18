
  <?php get_template_part('templates/page', 'header'); ?>

	<dl class="accordion">

		<?php 

			$i = 0; //counter for numbers beside faq items
			// check if the repeater field has rows of data

			if( have_rows('faqs') ):

		 	// loop through the rows of data
		    while ( have_rows('faqs') ) : the_row();
			
			$i++;

			// vars
			$question = get_sub_field('question');
			$answer = get_sub_field('answer');

		?>
			
			<dt><h3><?php echo $question; ?></h3></dt>
			<dd><p><?php echo $answer; ?></p></dd>

			<?php endwhile; ?>

		<?php endif; ?>

	</dl>
