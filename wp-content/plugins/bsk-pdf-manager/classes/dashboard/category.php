<?php

class BSKPDFM_Dashboard_Category {

	var $_plugin_pages_name = array();
    
	public function __construct() {
		global $wpdb;
		
		add_action('bsk_pdf_manager_category_save', array($this, 'bsk_pdf_manager_category_save_fun'));
	}
	
	function bsk_pdf_manager_category_edit( $category_id = -1 ){
		global $wpdb;
		
		$cat_title = '';
        $current_edit_cat_id = 0;
        $chosen_parent_id = 0;
        $current_cat_depth = 0;
        $description = '';
		$cat_date = date( 'Y-m-d', current_time('timestamp') );
		$cat_password = '';
		$cat_empty_message = '';
		if ($category_id > 0){
			$sql = 'SELECT * FROM '.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.' WHERE id = %d';
			$sql = $wpdb->prepare( $sql, $category_id );
			$category_obj_array = $wpdb->get_results( $sql );
			if (count($category_obj_array) > 0){
                $current_edit_cat_id = $category_id;
                $chosen_parent_id = $category_obj_array[0]->parent;
				$cat_title = $category_obj_array[0]->title;
                $description = $category_obj_array[0]->description;
				$cat_date = date( 'Y-m-d', strtotime($category_obj_array[0]->last_date) );
				$cat_password = $category_obj_array[0]->password;
				$cat_empty_message = $category_obj_array[0]->empty_message;
                
                $current_cat_depth = BSKPDFM_Common_Backend::get_category_children_depth( $category_id );
			}
		}
        
        $parent_category_select_text = 'Select parent category...';
		
		$str = '<div class="bsk_pdf_manager_category_edit" style="padding-top: 10px;">';
		$str .='<p><span>Category Title * :</span><input type="text" name="cat_title" id="cat_title_id" value="'.$cat_title.'" maxlength="512" /></p>';
		$str .='<p><span>Parent Category:</span>'.BSKPDFM_Common_Backend::get_parent_category_dropdown( 2 - $current_cat_depth, 'cat_parent_category', 'cat_parent_category_ID', $parent_category_select_text, $chosen_parent_id, $current_edit_cat_id ).'</p>';
        $str .='<p><span>&nbsp;</span><i>The max category depth is 3, it means you can only chose categories with depth 1 or 2 as parent category.</i></p>';
        if( $current_cat_depth > 0 ){
            $str .='<p><span>&nbsp;</span><i>The current category alreay have child category so you can only chose categoreis with depth 1 as its parent category.</i></p>';
        }
        $str .='<div style="width: 100%;">
                    <div style="width: 13%; height: 160px; float: left; vertical-align: middle; display: table;">
                        <span style="vertical-align:middle; display: table-cell;">Description:</span>
                    </div>
                    <div style="width: 85%; height: 160px; float: left;">
                        <textarea name="cat_description" id="cat_description_id" maxlength="512" style="width:35.3%;" disabled />'.$description.'</textarea>
                    </div>
                 </div>';
		$str .='<p><span>Category Password:</span><input type="text" name="cat_password" id="cat_password_id" value="'.$cat_password.'" maxlength="32" disabled /></p>';
        
        $str .='<div style="width: 100%;">
                    <div style="width: 13%; height: 160px; float: left; vertical-align: middle; display: table;">
                        <span style="vertical-align:middle; display: table-cell;">Empty Message:</span>
                    </div>
                    <div style="width: 85%; height: 160px; float: left;">
                        <textarea name="cat_empty_message" id="cat_empty_message_id" maxlength="512" style="width:35.3%;" disabled />'.$cat_empty_message.'</textarea>
                    </div>
                 </div>';
        
		$str .='<p><span>Date * :</span><input type="text" name="cat_date" id="cat_date_id" value="'.$cat_date.'" class="bsk-date" /></p>';
		$str .='<p>
					<input type="hidden" name="bsk_pdf_manager_action" value="category_save" />
					<input type="hidden" name="bsk_pdf_manager_category_id" value="'.$category_id.'" />'.
					wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_pdf_manager_category_save_oper_nonce', true, false ).'
				</p>
				</div>';
		
		echo $str;
	}
	
	function bsk_pdf_manager_category_save_fun( $data ){
		global $wpdb;

        //check nonce field
		if ( !wp_verify_nonce( $data['bsk_pdf_manager_category_save_oper_nonce'], plugin_basename( __FILE__ ) ) ){
			return;
		}
		
		if ( !isset($data['bsk_pdf_manager_category_id']) ){
			return;
		}
		$id = $data['bsk_pdf_manager_category_id'];
		$title = trim($data['cat_title']);
        $cat_parent_category = 0;
        $description = trim($data['cat_description']);
		$password = $data['cat_password'];
		$empty_message = trim($data['cat_empty_message']);
		$last_date = trim($data['cat_date']);
		$last_date = $last_date ? $last_date.' 00:00:00' : date( 'Y-m-d 00:00:00', current_time('timestamp') );
		
		$title = wp_unslash($title); 
        $description = wp_unslash($description); 
        $empty_message = wp_unslash($empty_message); 
		
		$data_to_update = array();
		$data_to_update['title'] = $title;
        $data_to_update['parent'] = $cat_parent_category;
        $data_to_update['description'] = $description;
		$data_to_update['last_date'] = $last_date;
		$data_to_update['password'] = $password;
		$data_to_update['empty_message'] = $empty_message;
		if ( $id > 0 ){
			$wpdb->update( $wpdb->prefix.BSKPDFManager::$_cats_tbl_name, $data_to_update, array( 'id' => $id ) );
		}else if($id == -1){
			//insert
			$wpdb->insert( $wpdb->prefix.BSKPDFManager::$_cats_tbl_name, $data_to_update );
		}
		
		$redirect_to = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['base'] );
		wp_redirect( $redirect_to );
		exit;
	} //end of function
}
