<?php


require_once dirname( __FILE__ ) . '/vendors/cmb2/init.php';
require_once dirname( __FILE__ ) . '/class.product-data.php';


class Product_CPT {




	private static $slug = 'products';




	static function init() {

		add_action( 'init', 'Product_CPT::product_init' );
		add_filter( 'post_updated_messages', 'Product_CPT::product_updated_messages' );

		add_action( 'cmb2_admin_init', 'Product_CPT::register_meta_boxes' );

		add_filter( 'pre_get_posts', 'Product_CPT::set_archive_query' );
		add_filter( 'facetwp_sort_options', 'Product_CPT::facetwp_sort_options', 10, 2 );


		Product_Data::init();

	}

	static function product_init() {
		register_post_type( 'product', array(
			'labels'            => array(
				'name'                => __( 'Products', 'YOUR-TEXTDOMAIN' ),
				'singular_name'       => __( 'Product', 'YOUR-TEXTDOMAIN' ),
				'all_items'           => __( 'All Products', 'YOUR-TEXTDOMAIN' ),
				'new_item'            => __( 'New product', 'YOUR-TEXTDOMAIN' ),
				'add_new'             => __( 'Add New', 'YOUR-TEXTDOMAIN' ),
				'add_new_item'        => __( 'Add New product', 'YOUR-TEXTDOMAIN' ),
				'edit_item'           => __( 'Edit product', 'YOUR-TEXTDOMAIN' ),
				'view_item'           => __( 'View product', 'YOUR-TEXTDOMAIN' ),
				'search_items'        => __( 'Search products', 'YOUR-TEXTDOMAIN' ),
				'not_found'           => __( 'No products found', 'YOUR-TEXTDOMAIN' ),
				'not_found_in_trash'  => __( 'No products found in trash', 'YOUR-TEXTDOMAIN' ),
				'parent_item_colon'   => __( 'Parent product', 'YOUR-TEXTDOMAIN' ),
				'menu_name'           => __( 'Products', 'YOUR-TEXTDOMAIN' ),
			),
			'public'            => true,
			'taxonomies'        => array( 'product_tag', 'product_cat' ),
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'supports'          => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			'has_archive'       => 'products',
			'rewrite'           => array(
				'slug'                => self::$slug,
				'with_front'          => false,
			),
			'query_var'         => true,
			'menu_icon'         => 'dashicons-lightbulb',
		) );

	}

	static function product_updated_messages( $messages ) {
		global $post;

		$permalink = get_permalink( $post );

		$messages['product'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __('Product updated. <a target="_blank" href="%s">View product</a>', 'YOUR-TEXTDOMAIN'), esc_url( $permalink ) ),
			2 => __('Custom field updated.', 'YOUR-TEXTDOMAIN'),
			3 => __('Custom field deleted.', 'YOUR-TEXTDOMAIN'),
			4 => __('Product updated.', 'YOUR-TEXTDOMAIN'),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __('Product restored to revision from %s', 'YOUR-TEXTDOMAIN'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Product published. <a href="%s">View product</a>', 'YOUR-TEXTDOMAIN'), esc_url( $permalink ) ),
			7 => __('Product saved.', 'YOUR-TEXTDOMAIN'),
			8 => sprintf( __('Product submitted. <a target="_blank" href="%s">Preview product</a>', 'YOUR-TEXTDOMAIN'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
			9 => sprintf( __('Product scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview product</a>', 'YOUR-TEXTDOMAIN'),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
			10 => sprintf( __('Product draft updated. <a target="_blank" href="%s">Preview product</a>', 'YOUR-TEXTDOMAIN'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		);

		return $messages;
	}

	static function register_meta_boxes() {
		$prefix = 'product_';

		$product_metabox = new_cmb2_box( array(
			'id'            => $prefix . 'metabox',
			'title'         => __( 'Product Data', 'lighting-products' ),
			'object_types'  => array( 'product', ), // Post type
			// 'show_on_cb' => 'yourprefix_show_if_front_page', // function should return a bool value
			// 'context'    => 'normal',
			// 'priority'   => 'high',
			// 'show_names' => true, // Show field names on the left
			// 'cmb_styles' => false, // false to disable the CMB stylesheet
			// 'closed'     => true, // true to keep the metabox closed by default
			// 'classes'    => 'extra-class', // Extra cmb2-wrap classes
			// 'classes_cb' => 'yourprefix_add_some_classes', // Add classes through a callback.
		) );

		$product_metabox->add_field( array(
			'name'       => __( 'Model', 'lighting-products' ),
			'desc'       => __( 'The product\'s model name', 'lighting-products' ),
			'id'         => 'model',
			'type'       => 'text',
		) );


		$product_metabox->add_field( array(
			'name' => __( 'Associate', 'lighting-products' ),
			'desc' => __( 'Check if manufactured by an Associate.', 'lighting-products' ),
			'id'   => 'lg-associate',
			'type' => 'checkbox',
		) );

		$product_metabox->add_field( array(
			'name' => __( 'Family of Products', 'lighting-products' ),
			'desc' => __( 'Check if product represents a family of products.', 'lighting-products' ),
			'id'   => 'lg-family',
			'type' => 'checkbox',
		) );

		$product_metabox->add_field( array(
			'name' => __( 'More Warranty Information', 'lighting-products' ),
			'desc' => __( 'If filled, will overwrite "Warranty Taxonomy" information on single product page table.', 'lighting-products' ),
			'id'   => 'lg-additional_warranty',
			'type' => 'textarea_small',
		) );

		$product_metabox->add_field( array(
			'name' => __( 'Other Information', 'lighting-products' ),
			'id'   => 'lg-other_information',
			'type' => 'textarea_small',
		) );

		$product_metabox->add_field( array(
			'name'       => __( 'Test Results Expiration Date', 'lighting-products' ),
			'desc'       => __( 'The Expiration Date', 'lighting-products' ),
			'id'         => 'lg-expiration_date',
			'type'       => 'text_date',
		) );


		$pdf_group_id = $product_metabox->add_field( array(
			'id'          => 'product-pdfs',
			'type'        => 'group',
			'description' => __( 'Add groups of PDf files', 'lighting-global' ),
			'options'     => array(
				'group_title'   => __( 'PDF Group #{#}', 'lighting-global' ),
				'add_button'    => __( 'Add Another PDF Group', 'lighting-global' ),
				'remove_button' => __( 'Remove PDF Group', 'lighting-global' ),
				'sortable'      => true,
			),
		) );

		$product_metabox->add_group_field( $pdf_group_id, array(
			'name'        => 'PDF Group Title',
			'id'          => 'title',
			'type'        => 'text',
			'description' => 'Leave empty if only one group of PDFs.',
		) );

		$product_metabox->add_group_field( $pdf_group_id,array(
			'name'       => __( 'PDF Files', 'lighting-products' ),
			'desc'       => __( 'Upload PDFs or add from media gallery.', 'lighting-products' ),
			'id'         => 'pdfs',
			'type'       => 'file_list',
		) );


	}




	static function set_archive_query( $query ) {
		if ( $query->is_post_type_archive( 'product' ) && $query->is_main_query() && ! is_admin() ) {
			$query->set( 'posts_per_page', 12 );
		}

		return $query;
	}




	function facetwp_sort_options( $options, $params ) {
		// $options['default'] = array(
		// 	'label'      => __( 'Date (Newest)', 'fwp' ),
		// 	'query_args' => array(
		// 		'orderby'   => 'date',
		// 		'order'     => 'DESC',
		// 	)
		// );

		return $options;
	}


}