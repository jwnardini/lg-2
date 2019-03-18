<?php


require_once dirname( __FILE__ ) . '/vendors/cmb2/init.php';
require_once dirname( __FILE__ ) . '/vendors/cmb2-attached-posts/cmb2-attached-posts-field.php';


class Resource_CPT {



	private static $textdomain = 'lighting-resources';




	private static $slug = 'resource';



	/**
	 * Initialize all event registrations with WordPress
	 * 
	 * @return no return
	 */
	static function init() {

		add_action( 'init', 'Resource_CPT::register' );
		add_filter( 'post_updated_messages', 'Resource_CPT::messages' );

		add_action( 'cmb2_admin_init', 'Resource_CPT::register_meta_boxes' );

		add_action( 'cmb2_init', 'Resource_CPT::register_page_meta_boxes' );

	}




	/**
	 * Register the 'resource' post type
	 *
	 * @action init
	 */
	static function register() {

		$single = self::$slug;
		$plural = self::$slug . 's';

		register_post_type( $single, array(
			'labels'            => array(
				'name'                => __( ucfirst( $plural ),                  self::$textdomain ),
				'singular_name'       => __( ucfirst( $single ),                  self::$textdomain ),
				'all_items'           => __( 'All ' . ucfirst( $plural ),         self::$textdomain ),
				'new_item'            => __( 'New ' . ucfirst( $single ),         self::$textdomain ),
				'add_new'             => __( 'Add New',                           self::$textdomain ),
				'add_new_item'        => __( 'Add New ' . ucfirst( $single ),     self::$textdomain ),
				'edit_item'           => __( 'Edit '    . ucfirst( $single ),     self::$textdomain ),
				'view_item'           => __( 'View '    . ucfirst( $single ),     self::$textdomain ),
				'search_items'        => __( 'Search '  . ucfirst( $plural ),     self::$textdomain ),
				'not_found'           => __( 'No ' . $plural . ' found',          self::$textdomain ),
				'not_found_in_trash'  => __( 'No ' . $plural . ' found in trash', self::$textdomain ),
				'parent_item_colon'   => __( 'Parent ' . ucfirst( $single ),      self::$textdomain ),
				'menu_name'           => __( ucfirst( $plural ),                  self::$textdomain ),
			),
			'public'            => true,
			'taxonomies'        => array( 'resource_cat' ),
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'supports'          => array( 'title', 'editor', 'excerpt', 'thumbnail', 'taxonomy' ),
			'has_archive'       => $plural,
			'rewrite'           => array(
				'slug'                => self::$slug,
				'with_front'          => false,
			),
			'query_var'         => true,
			'menu_icon'         => 'dashicons-format-aside',
		) );

	}




	/**
	 * Filters the array of messages for 'resource' post type CRUD actions
	 *
	 * @filter post_updated_messages
	 * 
	 * @param  Array $messages The array of message strings
	 * @return Array           The array of message strings
	 */
	static function messages( $messages ) {

		global $post;

		$single = self::$slug;
		$plural = self::$slug . 's';

		$permalink = get_permalink( $post );

		$messages[$single] = array(
			 0 => '', 
			 1 => sprintf( __( ucfirst( $single ) . ' updated. <a target="_blank" href="%s">View ' . ucfirst( $single ) . '</a>', self::$textdomain ), esc_url( $permalink ) ),
			 2 => __( 'Custom field updated.', self::$textdomain ),
			 3 => __( 'Custom field deleted.', self::$textdomain ),
			 4 => __( ucfirst( $single ) . ' updated.', self::$textdomain ),
			 5 => isset($_GET['revision']) ? sprintf( __( ucfirst( $single ) . ' restored to revision from %s', self::$textdomain ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			 6 => sprintf( __( ucfirst( $single ) . ' published. <a href="%s">View ' . ucfirst( $single ) . '</a>', self::$textdomain ), esc_url( $permalink ) ),
			 7 => __( ucfirst( $single ) . ' saved.', self::$textdomain ),
			 8 => sprintf( __( ucfirst( $single ) . ' submitted. <a target="_blank" href="%s">Preview ' . ucfirst( $single ) . '</a>', self::$textdomain ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
			 9 => sprintf( __( ucfirst( $single ) . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . ucfirst( $single ) . '</a>', self::$textdomain ),
			          date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
			10 => sprintf( __( ucfirst( $single ) . ' draft updated. <a target="_blank" href="%s">Preview ' . ucfirst( $single ) . '</a>', self::$textdomain ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		);

		return $messages;
	}




	/**
	 * Register the CMB2 boxes to appear on the 'resource' edit admin page
	 *
	 * @action cmb2_admin_init
	 */
	static function register_meta_boxes() {

		$prefix = self::$slug . '_';

		$metabox = new_cmb2_box( array(
			'id'            => $prefix . 'metabox',
			'title'         => __( 'Resource Data', self::$textdomain ),
			'object_types'  => array( self::$slug, ),
		) );

		$metabox->add_field( array(
			'name'       => __( 'Subtitle', self::$textdomain ),
			'desc'       => __( 'The Resource Subtitle', self::$textdomain ),
			'id'         => 'lg-subtitle',
			'type'       => 'text',
		) );

		$metabox->add_field( array(
			'name'       => __( 'Resource Date Override', self::$textdomain ),
			'desc'       => __( 'Optional: Enter a non-specific date (\'2015\' or \'March 2016\').<br>If present, this will override the above \'Resource Date\' on the front-end template.', self::$textdomain ),
			'id'         => 'lg-resource_date_override',
			'type'       => 'text',
		) );

		$pdf_group_id = $metabox->add_field( array(
			'id'          => 'resource-pdfs',
			'type'        => 'group',
			'description' => __( 'Add PDF files.<br>Typically used for different language translations of the same resource. (English, French, etc)', self::$textdomain ),
			'options'     => array(
				'group_title'   => __( 'PDF #{#}', self::$textdomain ),
				'add_button'    => __( 'Add Another PDF', self::$textdomain ),
				'remove_button' => __( 'Remove PDF', self::$textdomain ),
				'sortable'      => true,
			),
		) );

		$metabox->add_group_field( $pdf_group_id, array(
			'name'        => __( 'PDF Title', self::$textdomain ),
			'id'          => 'title',
			'type'        => 'text',
			'description' => __( 'Feel free to leave empty if only one PDFs', self::$textdomain ),
		) );

		$metabox->add_group_field( $pdf_group_id, array(
			'name'       => __( 'PDF File', self::$textdomain ),
			'desc'       => __( 'Upload a PDF or add from media gallery.', self::$textdomain ),
			'id'         => 'link',
			'type'       => 'file',
		) );
	}




	/**
	 * Register the CMB2 meta boxes for the 'Associated Resource' section on Pages
	 *
	 * @action cmb2_init
	 */
	static function register_page_meta_boxes() {
		
		$related_page_meta = new_cmb2_box( array(
			'id'           => 'related_resources',
			'title'        => __( 'Related Resources', self::$textdomain ),
			'object_types' => array( 'page' ),
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => false,
		) );

		$related_page_meta->add_field( array(
			'name'    => __( 'Related Resources', self::$textdomain ),
			'desc'    => __( 'Drag resources from the left column to the right column to attach them to this page.<br>You may rearrange the order of the resources in the right column by dragging and dropping.', self::$textdomain ),
			'id'      => 'resources',
			'type'    => 'custom_attached_posts',
			'options' => array(
				'show_thumbnails' => true,
				'filter_boxes'    => true,
				'query_args'      => array( 'post_type' => 'resource', 'posts_per_page' => -1 ),
			),
		) );

	}




	/**
	 * Renders the related resources for a page
	 * 
	 * Utility function for use in theming
	 * 
	 * @param  Int $post_id The current post id
	 */
	static function render_related_resources( $post_id ) {

		$resources = get_post_meta( $post_id, 'resources', 1 );

		// If there are no resources, return nothing
		if ( ! $resources ) {
			return $resources;
		}

		?>

		<aside class='related-resources'>
			<h2>Related Resources</h2>
			<ul>

				<?php
				
					foreach ( $resources as $resource_id ) {

						$resource = [
										'title' => get_the_title( $resource_id ),
										'link'  => get_the_permalink( $resource_id ),
										'thumb' => get_the_post_thumbnail( $resource_id, 'medium' ),
						            ];

						if ( ! $resource['thumb'] ) {
							$resource['thumb'] = '<img class="none">';
						}

						echo '<li><a href="' . esc_url( $resource['link'] ) . '">' . $resource['thumb'] . '<span>' . esc_html( $resource['title'] ) . '</span></a></li>';
					}

				?>

			</ul>
		</aside>

		<?php
	}



	/**
	 * Renders the correct date
	 *
	 * Utility function for use in theming
	 * 
	 * @param  Int $post_id The current post id
	 */
	static function render_date( $post_id ) {

		$date_override = get_post_meta( $post_id, 'lg-resource_date_override', 1 );

		if ( $date_override ) {
			echo '<time>' . $date_override . '</time>';
		} else {
			echo '<time datetime="' . get_post_time( 'c', true ) . '">' . get_the_date() . '</time>';
		}
	}




	/**
	 * Renders the list of attached pdfs
	 * 
	 * Utilty function for use in theming
	 * 
	 * @param  Int $post_id The current post id
	 */
	static function render_pdfs( $post_id ) {

		$pdfs = get_post_meta( $post_id, 'resource-pdfs', 1 );

		// If there are no pdfs, return nothing
		if ( ! $pdfs ) {
			return $pdfs;
		}

		?>

		<ul class="pdfs">

			<?php

			foreach ( $pdfs as $pdf ) {

				if ( ! array_key_exists( 'title', $pdf ) ) {
					$pdf['title'] = 'Read PDF';
				} else {
					$pdf['title'] = 'Read ' . $pdf['title'] . ' PDF';
				}

				if ( ! array_key_exists( 'link', $pdf ) ) {
					$pdf['link'] = '';
				}

				echo '<li><a class="btn btn-default" target="_blank" href="' . esc_url( $pdf['link'] ) . '">' . esc_html( $pdf['title'] ) . '</a></li>';
			}

			?>

		</ul>

		<?php
	}



	/**
	 * Renders the resource subtitle
	 *
	 * Utility function for use in theming
	 * 
	 * @param  Int $post_id The current post id
	 */
	static function render_subtitle( $post_id ) {

		$subtitle = get_post_meta( $post_id, 'lg-subtitle', 1 );

		if ( ! $subtitle ) {
			return $subtitle;
		}

		echo '<h2>' . esc_html( $subtitle ) . '</h2>';
	}




	/**
	 * A simple getter to retrieve the slug for other classes
	 * 
	 * @return String    The slug of the CPT
	 */
	public static function get_slug() {
		return self::$slug;
	}

}