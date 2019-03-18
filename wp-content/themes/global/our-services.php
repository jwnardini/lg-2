<?php
	/* Template Name: Our Services */
?>

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>

    <div class="content">
        <?php the_content(); ?>
    </div>

<?php endwhile; ?>

<?php

$args = array( 'hide_empty' => false, );

$terms_list = get_terms( 'manufacturers', $args );
if ( ! empty( $terms_list ) && ! is_wp_error( $terms_list )){ ?>

<div class="manufacturers-list">
  <h2>Manufacturers</h2>

    <div class="grid">

    <?php foreach ( $terms_list as $term ) {

    $taxonomy = 'manufacturers';
    $term_id = $term->term_id;

     if( get_field('manufacturer_associate', $taxonomy . '_' . $term_id) ) {

        $alt_name = get_field('alternate_name', $taxonomy . '_' . $term_id); ?>

        <p>

        <?php  if ( $term->description ) { ?>

              <a href="#<?php echo $term->slug ?>">
               <?php echo $alt_name ? $alt_name : $term->name; ?>
              </a>

        <?php  } else { ?>
              <?php echo $alt_name ? $alt_name : $term->name; ?>
        <?php  } ?>

        </p>

      <?php } else {
    }

  } ?>

    </div>
    </div>

<?php } ?>

<div class="distributors-list">
    <h2>Distributors</h2>

    <?php

        // check if the repeater field has rows of data
        if( have_rows('distributors') ):

            // loop through the rows of data
            while ( have_rows('distributors') ) : the_row(); ?>

            <p>
                <?php if(get_sub_field('distributor_slug')){ ?>
                    <a href="#<?php the_sub_field('distributor_slug'); ?>">
                        <?php echo get_sub_field('distributor_name'); ?>
                    </a>
                <?php } else { ?>
                     <?php the_sub_field('distributor_name'); ?>
                <?php } ?>
            </p>

        <?php

        endwhile; endif;

    ?>


</div>




<?php

$args = array( 'hide_empty' => false, );

$terms_list = get_terms( 'manufacturers', $args );
if ( ! empty( $terms_list ) && ! is_wp_error( $terms_list )){

  foreach ( $terms_list as $term ) {

  $taxonomy = 'manufacturers';
  $term_id = $term->term_id;


  //var_dump($term);

     $alt_name = get_field('alternate_name', $taxonomy . '_' . $term_id);
     $image = get_field('category_logo', $term->taxonomy . '_' . $term->term_id );
     $url = get_field('manufacturer_url', $term->taxonomy . '_' . $term->term_id );

       // if( get_field('manufacturer_associate', $taxonomy . '_' . $term_id) ) {

     ?>

<?php if( get_field('manufacturer_associate', $taxonomy . '_' . $term_id) ) { ?>

     <div class="manufacturer" id="<?php echo $term->slug; ?>">
        <p>
            <?php if( $image ){

                 if( $url){ ?>
                   <a target="_blank" href="<?php echo $url; ?>">
                 <?php } ?>

          	       <img src="<?php echo $image["url"]; ?>">

                 <?php if( $url){ ?>
                    </a>
                  <?php } ?>

           <?php   echo $term->description; ?>
         </p>



  <?php } ?>

  </div>

<?php

    }

  }

} ?>

  <?php

    // check if the repeater field has rows of data
    if( have_rows('distributors') ):

        // loop through the rows of data
        while ( have_rows('distributors') ) : the_row(); ?>

        <div class="distributor" id="<?php echo get_sub_field('distributor_slug'); ?>">

          <!-- <h2>Distributors</h2> -->

                <?php

                    $image = get_sub_field('distributor_logo');

                    if( !empty($image) ): ?>

                      <?php if(get_sub_field('distributor_url')){ ?>
                        <a target="_blank" href="<?php echo get_sub_field('distributor_url'); ?>">
                      <?php  } ?>

                        <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />

                      <?php if(get_sub_field('distributor_url')){ ?>
                        </a>
                      <?php  } ?>

                <?php endif; ?>

            <p>
                <?php echo get_sub_field('distributor_description'); ?>
            </p>
        </div>

    <?php

    endwhile; endif;

?>
