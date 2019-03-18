<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BSKPDFM_Dashboard_Categories extends WP_List_Table {
   
    function __construct() {
        global $wpdb;
		
        //Set parent defaults
        parent::__construct( array( 
            'singular' => 'bsk-pdf-manager-categories',  //singular name of the listed records
            'plural'   => 'bsk-pdf-manager-categories', //plural name of the listed records
            'ajax'     => false                          //does this table support ajax?
        ) );
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
			case 'id':
				echo $item['id_link'];
				break;
			case 'title':
				echo $item['title'];
				break;
            case 'description':
				echo $item['description'];
				break;
			case 'password':
                echo $item['password'];
                break;
            case 'last_date':
                echo $item['last_date'];
                break;
            case 'count':
                echo $item['count'];
                break;
        }
    }
   
    function column_cb( $item ) {
        return sprintf( 
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            esc_attr( $this->_args['singular'] ),
            esc_attr( $item['id'] )
        );
    }

    function get_columns() {
    
        $columns = array( 
			'cb'        		=> '<input type="checkbox"/>',
			'id'				=> 'ID',
            'title'     	=> 'Title',
            'description'     => 'Description',
			'password'     		=> 'Password',
            'last_date' 		=> 'Date',
            'count' 		=> 'PDFs Count'
        );
        
        return $columns;
    }
   
	function get_sortable_columns() {
		$c = array(
					'title' => 'title',
					'last_date'    => 'last_date'
					);
		
		return $c;
	}
	
    function get_views() {
		//$views = array('filter' => '<select name="a"><option value="1">1</option></select>');
		
        return array();
    }
   
    function get_bulk_actions() {
    
        $actions = array( 
            'delete'=> 'Delete'
        );
        
        return $actions;
    }

    function do_bulk_action() {
		global $wpdb;
		
		$categories_id = isset( $_POST['bsk-pdf-manager-categories'] ) ? $_POST['bsk-pdf-manager-categories'] : false;
		if ( !$categories_id || !is_array( $categories_id ) || count( $categories_id ) < 1 ){
			return;
		}
		$action = -1;
		if ( isset($_POST['action']) && $_POST['action'] != -1 ){
			$action = $_POST['action'];
		}
		if ( isset($_POST['action2']) && $_POST['action2'] != -1 ){
			$action = $_POST['action2'];
		}
		if ( $action == -1 ){
			return;
		}else if ( $action == 'delete' ){
			if( count($categories_id) < 1 ){
				return;
			}
			
			$ids = implode(',', $categories_id);
			$ids = trim($ids);
            $sql = 'SELECT `id`, `parent` FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` WHERE `id` IN('.$ids.')';
            $categories_to_delete = $wpdb->get_results( $sql );
            if( !$categories_to_delete && !is_array($categories_to_delete) && count($categories_to_delete) < 1 ){
                return;
            }
            $categories_to_delete_with_parent_array = array();
            foreach( $categories_to_delete as $obj ){
                $categories_to_delete_with_parent_array[$obj->id] = $obj->parent;
            }
            
			//when a category deleted for Pro edition
			//1. If PDF only have one same category then delte the PDF
			//2. If the PDF belong to multiple caegories then remove this category from the PDF
            //3. If the category has children then set its children' parent to its parent ( if have )
			foreach( $categories_to_delete_with_parent_array as $category_id => $cat_parent ){
                /*
                  *process children categories
                  */
                //update parent to the deleted one's parent or ancestor
                $sql = 'SELECT `id` FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` WHERE `parent` = '.$category_id;
			    $children_categories = $wpdb->get_results( $sql );
                if( $children_categories && is_array($children_categories) && count($children_categories) > 0 ){
                    $parent_to_be_set = $cat_parent;
                    //check if the parent cat to be deleted, yes then loop to its ancestor which won't be deleted
                    while( $parent_to_be_set && in_array( $parent_to_be_set, $categories_id ) ){
                        $sql = 'SELECT `parent` FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` WHERE `id` = '.$parent_to_be_set;
                        $parent_to_be_set = $wpdb->get_var( $sql );
                    }
                    
                    //update children categories' parent
                    $children_categories_ids = array();
                    foreach( $children_categories as $child_category_obj ){
                        $children_categories_ids[] = $child_category_obj->id;
                    }
                    $children_categories_ids_str = implode(',', $children_categories_ids);
                    $sql = 'UPDATE `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` '.
                             'SET `parent` = '.$parent_to_be_set.' '.
                             'WHERE `id` IN( '.$children_categories_ids_str.' )';
                    $wpdb->query( $sql );
                }
                
                /*
                  *process pdfs
                  */
                $sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_rels_tbl_name.'` WHERE cat_id = %d';
				$sql = $wpdb->prepare( $sql, $category_id );
				$pdfs = $wpdb->get_results( $sql );
				if ( !$pdfs || !is_array($pdfs) || count($pdfs) < 1 ){
                    continue;
                }
                
                foreach($pdfs as $pdf_rel_obj ){
                    //query to see if the $pdf only associated to one cateogry
                    $sql = 'SELECT P.`id`, P.`file_name`, P.`by_media_uploader` FROM `'.$wpdb->prefix.BSKPDFManager::$_rels_tbl_name.'` AS R '.
                             'LEFT JOIN `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` AS P ON R.`pdf_id` = P.`id` '.
                             'WHERE R.`pdf_id` = '.$pdf_rel_obj->pdf_id;
                    $results = $wpdb->get_results( $sql );
                    if( $results == 1 && is_array($results) && count($results) == 1 ){
                        $pdf_obj = $results[0];
                        if( $pdf_obj->by_media_uploader < 1 &&
                            $pdf_obj->file_name && 
                            file_exists(ABSPATH.$pdf->file_name) ){
                            unlink(ABSPATH.$pdf->file_name);
                        }
                    }
                }
                
                //delete all relations associated to the category
                $sql = 'DELETE FROM `'.$wpdb->prefix.BSKPDFManager::$_rels_tbl_name.'` '.
                         'WHERE `cat_id` = '.$category_id;
                $wpdb->query( $sql );
			}
            
            //delete all categories
			$sql = 'DELETE FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` WHERE id IN('.$ids.')';
			$wpdb->query( $sql );
		}
    }

    function get_data() {
		global $wpdb;
		
        $search = '';
		$orderby = '';
		$order = '';
        // check to see if we are searching
        if( isset( $_POST['s'] ) ) {
            $search = trim( $_POST['s'] );
        }
		if ( isset( $_REQUEST['orderby'] ) ){
			$orderby = $_REQUEST['orderby'];
		}
		if ( isset( $_REQUEST['order'] ) ){
			$order = $_REQUEST['order'];
		}
		
		$sql = 'SELECT * FROM '.
		       $wpdb->prefix.BSKPDFManager::$_cats_tbl_name.' AS c';

		$whereCase = $search ? ' c.title LIKE "%'.$search.'%"' : '';
		$orderCase = ' ORDER BY c.last_date DESC';
		if ( $orderby ){
			$orderCase = ' ORDER BY c.'.$orderby.' '.$order;
		}
		$whereCase = $whereCase ? ' WHERE '.$whereCase : ' WHERE 1';
		$whereCase .= ' AND c.`parent` = 0 ';
		$catgories = $wpdb->get_results($sql.$whereCase.$orderCase);
		
		if (!$catgories || count($catgories) < 1){
			return NULL;
		}
		$category_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['base'] );
		
       
		$categories_data = array();
		foreach ( $catgories as $category ) {
            $current_category_depth = 1;
			$category_edit_page = add_query_arg( array('view' => 'edit', 
                                                                    'categoryid' => $category->id),
                                                                    $category_page_url );
			$categories_data[] = array( 
			    'id' 				=> $category->id,
				'id_link' 			=> '<a href="'.$category_edit_page.'">'.$category->id.'</a>',
				'title'     	=> '<strong><a class="row-title" href="'.$category_edit_page.'">'.$category->title.'</a></strong>',
                'description'    => $category->description,
				'password'			=> $category->password,
				'last_date'			=> date('Y-m-d', strtotime($category->last_date)),
                'count'             => BSKPDFM_Common_Backend::get_cat_pdfs_count( $category->id ),
			);
		}
		
		return $categories_data;
    }

    function prepare_items() {
       
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 20;
        $data = array();
		
        add_thickbox();

		$this->do_bulk_action();
       
        $data = $this->get_data();
   
        $current_page = $this->get_pagenum();
        $total_items = 0;
        if( $data && is_array( $data ) ){
            $total_items = count( $data );
        }
	    if ($total_items > 0){
        	$data = array_slice( $data,( ( $current_page-1 )*$per_page ),$per_page );
		}
        $this->items = $data;

        $this->set_pagination_args( array( 
            'total_items' => $total_items,                  // We have to calculate the total number of items
            'per_page'    => $per_page,                     // We have to determine how many items to show on a page
            'total_pages' => ceil( $total_items/$per_page ) // We have to calculate the total number of pages
        ) );
    }
	

	
	function get_column_info() {
		
		$columns = array( 
							'cb'        		=> '<input type="checkbox"/>',
							'id'				=> 'ID',
							'title'     	    => 'Title',
                            'description'     	=> 'Description',
							'password'     		=> 'Password',
							'last_date' 		=> 'Date',
                            'count'             => 'PDFs Count',
						);
		
		$hidden = array();

		$_sortable = apply_filters( "manage_{$this->screen->id}_sortable_columns", $this->get_sortable_columns() );

		$sortable = array();
		foreach ( $_sortable as $id => $data ) {
			if ( empty( $data ) )
				continue;

			$data = (array) $data;
			if ( !isset( $data[1] ) )
				$data[1] = false;

			$sortable[$id] = $data;
		}

		$_column_headers = array( $columns, $hidden, $sortable, array() );

		return $_column_headers;
	}
}