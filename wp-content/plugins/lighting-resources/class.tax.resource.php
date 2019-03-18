<?php

class Resource_TAX {




	private static $textdomain = 'lighting-resources';




	static function init() {
		add_action( 'init', 'Resource_TAX::register' );
	}




	static function register() {

		$slug = Resource_CPT::get_slug();

		register_taxonomy(
			$slug. '_type',
			$slug,
			array(
				'label'        => __( ucfirst( $slug ) . ' Type', self::$textdomain ),
				'rewrite'      => array( 'slug' => 'type' ),
				'hierarchical' => true,
			)
		);

		register_taxonomy(
			$slug . '_country',
			$slug,
			array(
				'label'        => __( 'Country', self::$textdomain ),
				'rewrite'      => array( 'slug' => 'country' ),
				'hierarchical' => true,
			)
		);

		register_taxonomy(
			$slug . '_language',
			$slug,
			array(
				'label'        => __( 'Language', self::$textdomain ),
				'rewrite'      => array( 'slug' => 'language' ),
				'hierarchical' => true,
			)
		);
	}




	static function render( $id ) {
		$taxonomies = [ 'resource_type', 'resource_country', 'resource_language' ];

        foreach ( $taxonomies as $tax ) {
          $terms = get_the_terms(  $id , $tax );

          if ( ! $terms ) continue;

          $out = '<div class="taxonomy"><p>';

          foreach ( $terms as $i => $term ) {

            if ( $i == 0 ) {
              $label = get_taxonomy( $term->taxonomy );

              $out .= $label->labels->name . ': ';
            }

            $out .= $term->name;

            if ( count( $terms ) !== $i + 1 ) $out .= ', ';
          }

          $out .= '</p></div>';

          echo $out;
        }
	}
}

?>