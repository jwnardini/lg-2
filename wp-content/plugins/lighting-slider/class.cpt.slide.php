<?php


class Slide_CPT {

	static function init() {

		add_action( 'init', 'Slide_CPT::slide_init' );
		add_filter( 'post_updated_messages', 'Slide_CPT::slide_updated_messages' );

	}

	static function slide_init() {
		register_post_type( 'slide', array(
			'labels'            => array(
				'name'                => __( 'Slides', 'YOUR-TEXTDOMAIN' ),
				'singular_name'       => __( 'Slide', 'YOUR-TEXTDOMAIN' ),
				'all_items'           => __( 'All Slides', 'YOUR-TEXTDOMAIN' ),
				'new_item'            => __( 'New slide', 'YOUR-TEXTDOMAIN' ),
				'add_new'             => __( 'Add New', 'YOUR-TEXTDOMAIN' ),
				'add_new_item'        => __( 'Add New slide', 'YOUR-TEXTDOMAIN' ),
				'edit_item'           => __( 'Edit slide', 'YOUR-TEXTDOMAIN' ),
				'view_item'           => __( 'View slide', 'YOUR-TEXTDOMAIN' ),
				'search_items'        => __( 'Search slides', 'YOUR-TEXTDOMAIN' ),
				'not_found'           => __( 'No slides found', 'YOUR-TEXTDOMAIN' ),
				'not_found_in_trash'  => __( 'No slides found in trash', 'YOUR-TEXTDOMAIN' ),
				'parent_item_colon'   => __( 'Parent slide', 'YOUR-TEXTDOMAIN' ),
				'menu_name'           => __( 'Slides', 'YOUR-TEXTDOMAIN' ),
			),
			'public'             => true,
			'publicly_queryable' => false,
			'hierarchical'       => false,
			'show_ui'            => true,
			'show_in_nav_menus'  => false,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'has_archive'        => false,
			'rewrite'            => true,
			'query_var'          => true,
			'menu_icon'          => 'dashicons-format-gallery',
		) );

	}

	static function slide_updated_messages( $messages ) {
		global $post;

		$permalink = get_permalink( $post );

		$messages['slide'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __('Product updated. <a target="_blank" href="%s">View slide</a>', 'YOUR-TEXTDOMAIN'), esc_url( $permalink ) ),
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

}