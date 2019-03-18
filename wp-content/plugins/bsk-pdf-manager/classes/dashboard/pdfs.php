<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BSKPDFM_Dashboard_PDFs extends WP_List_Table {
   
    function __construct() {
        global $wpdb;
		
        //Set parent defaults
        parent::__construct( array( 
            'singular' => 'bsk-pdf-manager-pdfs',  //singular name of the listed records
            'plural'   => 'bsk-pdf-manager-pdfs', //plural name of the listed records
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
            case 'file_name':
                echo $item['file_name'];
                break;
            case 'description':
                echo $item['description'];
                break;
			case 'category':
				echo $item['category'];
				break;
			case 'last_date':
               	echo $item['last_date'];
                break;
			case 'order':
				echo $item['order'];
                break;
            case 'download_count':
				echo $item['download_count'];
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
                            'title'     		=> 'Title',
                            'file_name'     	=> 'File',
                            'description'     	=> 'Description',
                            'category'     		=> 'Category',
                            'order'				=> 'Order',
                            'last_date' 		=> 'Date',
                            'download_count' => 'Download Count',
                        );
        
        return $columns;
    }
	
	function extra_tablenav( $which ) {
		if ($which == 'bottom'){
			return;
		}
		
		global $wpdb;
		
		$sql = 'SELECT * FROM '.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name;
		$categoreies = $wpdb->get_results($sql);
		
		$select_str_header = '<div class="alignleft actions">
								<select name="bsk_pdf_manager_categories" id="bsk_pdf_manager_categories_id">';
		$select_str_footer = '	</select>
							  </div>';
		
		if (!$categoreies || count($categoreies) < 1){
			$select_str_body = '<option value="0">Please add category first</option>';
            
            echo $select_str_header.$select_str_body.$select_str_footer;
		}else{
			$current_category_id = 0;
			if( isset($_REQUEST['cat']) ){
				$current_category_id = $_REQUEST['cat'];
			}
			if( $current_category_id < 1 && isset($_REQUEST['bsk_pdf_manager_categories']) ){
				$current_category_id = $_REQUEST['bsk_pdf_manager_categories'];
			}

            $category_select_text = 'Please select category';
			$dropdown_str = BSKPDFM_Common_Backend::get_category_dropdown( 'bsk_pdf_manager_categories', 'bsk_pdf_manager_categories_id', $category_select_text, array( $current_category_id ) );
            
            echo $dropdown_str;
		}
	}
	
	function get_sortable_columns() {
		$c = array(
                    'id'        => 'id',
					'title' 	=> 'title',
					'order'  => 'order_num',
					'last_date' => 'last_date',
                    'download_count' => 'download_count',
					);
		
		return $c;
	}
   
    function get_bulk_actions() {
    
        $actions = array( 
            'delete' => 'Delete',
			'changecat' => 'Change Category'
        );
        
        return $actions;
    }

    function do_bulk_action() {
		global $wpdb;
		
		$lists_id = isset( $_POST['bsk-pdf-manager-pdfs'] ) ? $_POST['bsk-pdf-manager-pdfs'] : false;
		if ( !$lists_id || !is_array( $lists_id ) || count( $lists_id ) < 1 ){
			return;
		}
		
		if( ( isset($_POST['action']) && $_POST['action'] == 'delete') || 
			( isset($_POST['action2']) && $_POST['action2'] == 'delete') ){
				
            $ids = implode(',', $lists_id);
            $ids = trim($ids,',');

            //delete all files
            $sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` WHERE id IN('.$ids.')';
            $pdfs_records = $wpdb->get_results( $sql );
            if ($pdfs_records && count($pdfs_records) > 0){
                foreach($pdfs_records as $pdf_record ){
                    if( $pdf_record->file_name && 
                        file_exists(ABSPATH.$pdf_record->file_name) ){

                        unlink(ABSPATH.$pdf_record->file_name);
                    }
                }
            }
            
            //delete relationships
            $sql = 'DELETE FROM `'.$wpdb->prefix.BSKPDFManager::$_rels_tbl_name.'` WHERE `pdf_id` IN('.$ids.')';
            $wpdb->query( $sql );

            $sql = 'DELETE FROM `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` WHERE `id` IN('.$ids.')';
            $wpdb->query( $sql );
        }
    }

    function get_data() {
		global $wpdb;
		
		$list_thumbnail_size = 'bsk-pdf-dashboard-list-thumbnail';
		
		$current_category_id = 0;
		$key_word = '';
		$orderby = '';
		$order = '';
		if( isset($_REQUEST['cat']) ){
			$current_category_id = $_REQUEST['cat'];
		}
		if ( isset( $_REQUEST['orderby'] ) ){
			$orderby = $_REQUEST['orderby'];
		}
		if ( isset( $_REQUEST['order'] ) ){
			$order = $_REQUEST['order'];
		}
		if ( isset( $_REQUEST['s'] ) ){
			$key_word = $_REQUEST['s'];
		}
		
		$sql = 'SELECT P.* FROM `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` AS P ';
		$whereCase = ' WHERE 1';
		if( $current_category_id ){
            $pdf_id_sql = 'SELECT R.`pdf_id` FROM `'.$wpdb->prefix.BSKPDFManager::$_rels_tbl_name.'` AS R '.
                               'WHERE R.`cat_id` = %d';
            $pdf_id_sql = $wpdb->prepare( $pdf_id_sql, $current_category_id );
			$cat_whereCase = ' AND P.`id` IN( '.$pdf_id_sql.' ) ';
			$whereCase .= $cat_whereCase;
		}
		if( $key_word ){
			$search_whereCase = ' AND ( P.`title` LIKE %s OR P.`file_name` LIKE %s OR P.`description` LIKE %s )';
			$whereCase .= $wpdb->prepare( $search_whereCase, '%'.$key_word.'%', '%'.$key_word.'%', '%'.$key_word.'%' );
		}
		$orderCase = ' ORDER BY P.`last_date` DESC, P.`id` DESC';
        if( $orderby == 'id' ){
			$orderCase = ' ORDER BY P.`id` '.$order;
		}else if( $orderby == 'title' ){
			$orderCase = ' ORDER BY P.title '.$order.', P.last_date DESC, P.`id` DESC';
		}else if( $orderby == 'last_date' ){
			$orderCase = ' ORDER BY P.last_date '.$order.', P.`id` DESC';
		}else if( $orderby == 'order_num' ){
			$orderCase = ' ORDER BY P.order_num '.$order.', P.`id` DESC';
		}else if( $orderby == 'download_count' ){
            $orderCase = ' ORDER BY P.download_count '.$order.', P.`id` DESC';
        }

		//get all pdfs
		$all_pdfs = $wpdb->get_results($sql.$whereCase.$orderCase);
		if (!$all_pdfs || count($all_pdfs) < 1){
			return NULL;
		}
		
		//organise all category_data
		$sql = 'SELECT * FROM '.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name;
		$categoreies = $wpdb->get_results( $sql );
		$categoreies_data_array = array();
		foreach( $categoreies as $category_obj ){
			$categoreies_data_array[$category_obj->id] = $category_obj->title;
		}
		
		$pdfs_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['pdf']  );
		$edit_url = add_query_arg('view', 'edit', $pdfs_page_url);
		$lists_data = array();
		foreach($all_pdfs as $pdf_record){
			$edit_url = add_query_arg('pdfid', $pdf_record->id, $edit_url);
            if( $current_category_id ){
                $edit_url = add_query_arg('cat', $current_category_id, $edit_url);
            }
			$file_str = '';

            if( $pdf_record->file_name && 
                file_exists(ABSPATH.$pdf_record->file_name) ){

                $file_url = site_url().'/'.$pdf_record->file_name;
                $file_str =  '<a href="'.$file_url.'" target="_blank" title="Open PDF">'.$pdf_record->file_name.'</a>';
            }

            //category
			$category_str = '';
            //get all categories which the PDF associated
            $sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_rels_tbl_name.'` WHERE `pdf_id` = '.$pdf_record->id;
            $results = $wpdb->get_results( $sql );
            if( $results && is_array($results) && count($results) > 0 ){
                foreach( $results as $rel_obj ){
                    $category_str .= $categoreies_data_array[$rel_obj->cat_id];
                }
            }

			//pdf order
			$pdf_order_html = '<input type="number" class="bsk_pdfm_pdf_order" rel="'.$pdf_record->id.'" value="'.$pdf_record->order_num.'" min="0" />';
			$pdf_order_html .= '<span id="bsk_pdfm_pdf_order_ajax_loader_ID_'.$pdf_record->id.'" style="display:none;"><img src="'.BSKPDFManager::$_ajax_loader_img_url.'" /></span>';
			
			$row_data =  array( 
                                    'id'			=> $pdf_record->id,
                                    'id_link' 	   => '<a href="'.$edit_url.'" title="Edit">'.$pdf_record->id.'</a>',
                                    'title'     		=> '<a href="'.$edit_url.'" title="Edit">'.$pdf_record->title.'</a>',
                                    'file_name'     	=> $file_str,
                                    'description'     	=> $pdf_record->description,
                                    'category'			=> $category_str,
                                    'last_date' 		=> date('Y-m-d', strtotime($pdf_record->last_date)),
                                    'order'				=> $pdf_order_html,
                                    'download_count' => $pdf_record->download_count
								 );
			$lists_data[] = $row_data;
		}
		
		return $lists_data;
    }

    function prepare_items() {
       
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 20;
        $data = array();
		
        add_thickbox();

        $columns = $this->get_columns();
        $hidden = array(); // no hidden columns
       
        $this->_column_headers = array( $columns, $hidden );
       
        $this->do_bulk_action();
       
        $data = $this->get_data();
   
        $current_page = $this->get_pagenum();
    
        $total_items = 0;
        if( $data&& is_array($data) ){
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
                            'title'     		=> 'Title',
                            'file_name'     	=> 'File Name',
                            'description'       => 'Description',
                            'category'     		=> 'Category',
                            'order'     		=> 'Order',
                            'last_date' 		=> 'Date',
                            'download_count' => 'Download Count',
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