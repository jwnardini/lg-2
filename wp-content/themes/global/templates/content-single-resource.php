<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <header>
      <h1 class="entry-title"><?php the_title(); ?></h1>
      <?php
          if ( class_exists( 'Resource_CPT' ) ) {
            Resource_CPT::render_subtitle( get_the_id() );
          }
        ?>
      <div class="entry-meta">
        <?php
          if ( class_exists( 'Resource_CPT' ) ) {
            Resource_CPT::render_date( get_the_id() );
          }
        ?>
      </div>
    </header>
    <div class="entry-thumbnail">
      <?php the_post_thumbnail( 'medium' ); ?>
    </div>
    <div class="entry-content">
      <?php the_content(); ?>
      <?php
        if ( class_exists( 'Resource_TAX' ) ) {
          Resource_TAX::render( get_the_id() );
        }
      ?>
      <?php
        if ( class_exists( 'Resource_CPT' ) ) {
          Resource_CPT::render_pdfs( get_the_id() );
        }
      ?>
    </div>
  </article>
<?php endwhile; ?>
