<?php

class Product_Data {

	private static $key = 'product-data-settings';

	private static $id  = 'product_data_settings';





	static function init() {

		add_action( 'cmb2_admin_init', 'Product_Data::register_meta_boxes' );

		add_action( 'cmb2_render_link', 'Product_Data::render_link', 10, 5 );

		add_action( 'admin_menu', 'Product_Data::register_settings_page' );

		add_action( 'init', 'Product_Data::register_taxonomies' );

	}


	public static function get_manufacturer( $id ) {
		$manufacturer = wp_get_post_terms( $id, 'manufacturers' );

		if ( ! is_array( $manufacturer ) ) return false;

		$manufacturer = count( $manufacturer ) ? $manufacturer[0]->name : '';

		return $manufacturer;
	}



	public static function is_associate( $id ) {
		$is_associate = false;

		$meta = get_post_meta( $id, 'lg-associate', 1 );

		if ( isset( $meta ) && $meta == 'on' ) {
			$is_associate = true;
		}

		return $is_associate;
	}



	public static function is_family( $id ) {
		$is_family = false;

		$meta = get_post_meta( $id, 'lg-family', 1 );

		if ( isset( $meta ) && $meta == 'on' ) {
			$is_family = true;
		}

		return $is_family;
	}




	static function register_settings_page() {
		add_submenu_page( 
			'edit.php?post_type=product',
			'Product Data',
			'Data Settings',
			'manage_options',
			'product-data',
			'Product_Data::render'
		);
	}





	static function register_taxonomies() {

		$taxonomies = get_option( self::$key );

		if ( ! $taxonomies || ! array_key_exists( 'data-points', $taxonomies ) ) return;

		foreach ( $taxonomies['data-points'] as $taxonomy ) {

			$title = $taxonomy['title'];
			$slug  = strtolower( str_replace( ' ', '-', $title ) );

			register_taxonomy(  
				$slug, 
				'product',
				array(
					'hierarchical' => true,
					'show_in_menu' => false,
					'label'        => $title,
				)
			);

			add_submenu_page( 
				'product-data',
				$title,
				$title,
				'manage_options',
				'edit-tags.php?taxonomy=' . $slug . '&post_type=product'
			);
		}
	}




	static function render_link( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		$index = $field->group->index;
		$title = $field->group->value[$index]['title'];
		$slug  = strtolower( str_replace( ' ', '-', $title ) );

		$href  = 'edit-tags.php?taxonomy=' . $slug . '&post_type=product';

		echo '<a href="' . $href . '">Edit ' . $title . ' Data</a>';
	}




	static function register_meta_boxes() {

		$settings = new_cmb2_box( array(
			'id'         => self::$id,
			'hookup'     => false,
			'cmb_styles' => true,
			'show_on'    => array(
			    'key'         => 'options-page',
			    'value'       => array( self::$key ),
			),
		) );

		$data_group_id = $settings->add_field( array(
			'id'          => 'data-points',
			'type'        => 'group',
			'description' => __( 'Build dynamic product data points', 'lighting-global' ),
			'options'     => array(
				'group_title'   => __( 'Data Point #{#}', 'lighting-global' ),
				'add_button'    => __( 'Add Another Data point', 'lighting-global' ),
				'remove_button' => __( 'Remove Data Point', 'lighting-global' ),
				'sortable'      => true,
			),
		) );

		$settings->add_group_field( $data_group_id, array(
			'name'        => 'Data Point Title',
			'id'          => 'title',
			'type'        => 'text',
		) );

		$settings->add_group_field( $data_group_id, array(
			'name'        => 'Link',
			'id'          => 'link',
			'type'        => 'link',
		) );


		$settings->add_group_field( $data_group_id, array(
			'name'        => 'Filter?',
			'id'          => 'filter',
			'type'        => 'checkbox',
			'description' => 'Check if this should be a facet filter on the main products page.'
		) );


		$settings->add_group_field( $data_group_id, array(
			'name'        => 'Table?',
			'id'          => 'table',
			'type'        => 'checkbox',
			'description' => 'Check to display on single product page table.'
		) );


	}

	static function render() {
		?>
		
		<div class="wrap">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( self::$id, self::$key ); ?>
		</div>
		
		<?php
	}

}