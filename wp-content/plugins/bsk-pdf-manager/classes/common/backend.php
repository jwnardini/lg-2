<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class BSKPDFM_Common_Backend {
    
    //for get parent category structure
    private static $current_parent_category_depth_to_get_options = 0;
    
    public static  function get_image_sizes() {
        global $_wp_additional_image_sizes;

        $sizes = array();

        foreach ( get_intermediate_image_sizes() as $_size ) {
            if ( $_size == 'bsk-pdf-dashboard-list-thumbnail' ) {
                continue;
            }
            if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
                $sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
                $sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
                $sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
            } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
                $sizes[ $_size ] = array(
                    'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
                    'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                    'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
                );
            }
        }

        return $sizes;
    } //end of function
    
    public static  function get_image_size_dimission( $size_name ) {
        $sizes = self::get_image_sizes();
        if ( isset( $sizes[ $size_name ] ) ) {
            return $sizes[ $size_name ];
        }
        return false;
    } //end of function
    
    public static function bsk_pdf_manager_pdf_convert_hr_to_bytes( $size ) {
		$size  = strtolower( $size );
		$bytes = (int) $size;
		if ( strpos( $size, 'k' ) !== false )
			$bytes = intval( $size ) * 1024;
		elseif ( strpos( $size, 'm' ) !== false )
			$bytes = intval($size) * 1024 * 1024;
		elseif ( strpos( $size, 'g' ) !== false )
			$bytes = intval( $size ) * 1024 * 1024 * 1024;
		return $bytes;
	}
    
    public static  function get_parent_category_dropdown( $parent_max_depth, $dropdown_name, $dropdown_id, $select_text,
                                                                                   $chosen_parent_id, $current_edit_cat_id ) {
        global $wpdb;
        
        if( $parent_max_depth < 1 ){
            $selectr_str = '<select name="'.$dropdown_name.'" id="'.$dropdown_id.'">';
            $options_str = '<option value="0">'.$select_text.'</option>';
            $selectr_str .= $options_str;
            $selectr_str .= '</select>';
            
            return $selectr_str;
        }
        $sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` '.
                 'WHERE `parent` = 0 AND `id` != '.$current_edit_cat_id.' '.
                 'ORDER BY `title` ASC';
        $results = $wpdb->get_results( $sql );
        
        $options_str = '<option value="0">'.$select_text.'</option>';
        foreach( $results as $cat_obj ){
            $selected_str = $chosen_parent_id == $cat_obj->id ? ' selected="selected"' : '';
            $options_str .= '<option value="'.$cat_obj->id.'"'.$selected_str.'>'.$cat_obj->title.'</option>';
            
            $current_category_depth = 1;
            $child_category_depth = $current_category_depth + 1;
            if( $parent_max_depth == 2 ){
                $options_str .= self::get_parent_category_dropdown_options( 
                                                                                                   $cat_obj->id, 
                                                                                                   $chosen_parent_id, 
                                                                                                   $current_edit_cat_id, 
                                                                                                   $child_category_depth );
            }
        }
        
        $selectr_str = '<select name="'.$dropdown_name.'" id="'.$dropdown_id.'" disabled>';
        $selectr_str .= $options_str;
        $selectr_str .= '</select>';
        
        return $selectr_str;
    }
    
    public static  function get_parent_category_dropdown_options( $parent_cat_id, 
                                                                                               $chosen_parent_id, 
                                                                                               $current_edit_cat_id, 
                                                                                               $current_depth ) {
        global $wpdb;
        
        $sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` '.
                 'WHERE `parent` = '.$parent_cat_id.' AND `id` != '.$current_edit_cat_id.' '.
                 'ORDER BY `title` ASC';
        $results = $wpdb->get_results( $sql );
        if( !$results || !is_array($results) || count($results) < 1 ){
            return '';
        }
        
        $prefix = '';
        for( $i = 1; $i < $current_depth; $i++ ){
            $prefix .= '&#8212;&nbsp;';
        }
        
        $options_str = '';
        foreach( $results as $cat_obj ){
            $selected_str = $chosen_parent_id == $cat_obj->id ? ' selected="selected"' : '';
            $options_str .= '<option value="'.$cat_obj->id.'"'.$selected_str.'>'.$prefix.$cat_obj->title.'</option>';
            /*
              * no need to get grand category anymore
              
              
            $options_str .= self::get_parent_category_dropdown_options( $max_depth, $cat_obj->id, $chosen_parent_id, $current_edit_cat_id );
            */
        }
        
        return $options_str;
    }
    
    public static  function get_category_dropdown( $dropdown_name, $dropdown_id, $select_text, $current_cat_ids_array ) {
                                                                        
        global $wpdb;
        
        $sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` '.
                 'WHERE `parent` = 0 '.
                 'ORDER BY `title` ASC';
        $results = $wpdb->get_results( $sql );
        
        $options_str = '<option value="0">'.$select_text.'</option>';
        foreach( $results as $cat_obj ){
            $selected_str = in_array( $cat_obj->id, $current_cat_ids_array ) ? ' selected="selected"' : '';
            $options_str .= '<option value="'.$cat_obj->id.'"'.$selected_str.'>'.$cat_obj->title.'</option>';
            
            $current_category_depth = 1;
            $child_category_depth = $current_category_depth + 1;
            $options_str .= self::get_category_dropdown_options( $cat_obj->id, $current_cat_ids_array, $child_category_depth );
        }
        
        $selectr_str = '<select name="'.$dropdown_name.'" id="'.$dropdown_id.'">';
        $selectr_str .= $options_str;
        $selectr_str .= '</select>';
        
        return $selectr_str;
    }
    
    public static  function get_category_dropdown_options( $parent_cat_id, $current_cat_ids_array, $current_depth ) {
        global $wpdb;
        
        $sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` '.
                 'WHERE `parent` = '.$parent_cat_id.' '.
                 'ORDER BY `title` ASC';
        $results = $wpdb->get_results( $sql );
        if( !$results || !is_array($results) || count($results) < 1 ){
            return '';
        }
        
        $prefix = '';
        for( $i = 1; $i < $current_depth; $i++ ){
            $prefix .= '&#8212;&nbsp;';
        }
        
        $options_str = '';
        foreach( $results as $cat_obj ){
            $selected_str = in_array( $cat_obj->id, $current_cat_ids_array ) ? ' selected="selected"' : '';
            $options_str .= '<option value="'.$cat_obj->id.'"'.$selected_str.'>'.$prefix.$cat_obj->title.'</option>';
            
            $grand_category_depth = $current_depth + 1;
            $options_str .= self::get_category_dropdown_options( $cat_obj->id, $current_cat_ids_array, $grand_category_depth );
        }
        
        return $options_str;
    }
    
    public static  function get_category_children_depth( $cat_id ) {
        global $wpdb;
        
        $depth = 0;
        
        $sql = 'SELECT `id` FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` '.
                 'WHERE `parent` = '.$cat_id.' ';
        $children_results = $wpdb->get_results( $sql );
        while( $children_results && is_array($children_results) && count($children_results) > 0 ){
            $depth++;
            $children_ids = array();
            foreach( $children_results as $cat_obj ){
                $children_ids[] = $cat_obj->id;
            }
            $children_ids_str = implode(',', $children_ids);
            $sql = 'SELECT `id` FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` '.
                     'WHERE `parent` IN( '.$children_ids_str.')';
            $children_results = $wpdb->get_results( $sql );
        }
        
        return $depth;
    }
    
    public static function get_category_parent_ids( $cat_id ) {
        global $wpdb;
        
        $parents_id = array();
        
        $sql = 'SELECT `parent` as parent_id FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` '.
                 'WHERE `id` = '.$cat_id.' ';
        $parent_id = $wpdb->get_var( $sql );
        while( $parent_id ){
            $parents_id[] = $parent_id;
            $sql = 'SELECT `parent` FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` '.
                     'WHERE `id` = '.$parent_id.' ';
            $parent_id = $wpdb->get_var( $sql );
        }
        
        return $parents_id;
    }
    
    public static function get_category_hierarchy_checkbox( $checkbox_name, $checkbox_class, $checked_ids_array ) {
                                                                        
        global $wpdb;
        
        $sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` '.
                 'WHERE `parent` = 0 '.
                 'ORDER BY `title` ASC';
        $results = $wpdb->get_results( $sql );
        if( !$results || !is_array($results) || count($results) < 1 ){
            return '';
        }
        
        $out_str  = '<div class="bsk-pdfm-category-hierarchy-checkbox-container">';
        foreach( $results as $category ){
            $label = $category->title;
            $checkbox_val = $category->id;
            $out_str .= '<ul>';
            $out_str .= '<li>
                                <label>
                                    <input type="checkbox" name="'.$checkbox_name.'" class="'.$checkbox_class.'" value="'.$checkbox_val.'" />'.$label.'
                                </label>';
            if( $category->description ){
                $out_str .= '<p>'.$category->description.'</p>';
            }
            $out_str .= self::get_category_hierarchy_checkbox_children( $category->id, $checkbox_name, $checkbox_class, $checked_ids_array );
            $out_str .= '</li>';
            $out_str .= '</ul>';
        }
        $out_str .= '</div>';
        
        return $out_str;
    }
    
    public static function get_category_hierarchy_checkbox_children( $parent_id, $checkbox_name, $checkbox_class, $checked_ids_array ){
        global $wpdb;
        
        $sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` '.
                 'WHERE `parent` =  '.$parent_id.' '.
                 'ORDER BY `title` ASC';
        $results = $wpdb->get_results( $sql );
        if( !$results || !is_array($results) || count($results) < 1 ){
            return '';
        }
        
        $return_str  = '<ul>';
        foreach( $results as $category ){
            $label = $category->title;
            $checkbox_val = $category->id;
            $return_str .= '<li>
                                    <label>
                                        <input type="checkbox" name="'.$checkbox_name.'" class="'.$checkbox_class.'" value="'.$checkbox_val.'" />'.$label.'
                                    </label>';
            $return_str .= self::get_category_hierarchy_checkbox_children( $category->id, $checkbox_name, $checkbox_class, $checked_ids_array );
            $return_str .= '</li>';

        }
        $return_str .= '</ul>';
        
        return $return_str;
    } //end of function
    
    public static function get_available_extension_with_mime_type(){
        $all_available_extension_with_mime_type = array();
        
        $all_available_extension_with_mime_type['pdf'] = array( 'application/pdf' );
        $all_available_extension_with_mime_type['zip'] = array( 
                                                                     'application/x-compressed', 
                                                                     'application/x-zip-compressed', 
                                                                     'application/zip',
                                                                     'multipart/x-zip'
                                                                   );
		$all_available_extension_with_mime_type['gz'] = array(  
                                                                     'application/x-compressed', 
                                                                     'application/x-gzip'
                                                                  );
        $all_available_extension_with_mime_type['rar'] = array(  
                                                                      'application/x-rar-compressed', 
                                                                      'application/octet-stream'
                                                                  );
        $all_available_extension_with_mime_type['rar'] = array(  
                                                                      'application/x-rar-compressed', 
                                                                      'application/octet-stream'
                                                                  );
        $all_available_extension_with_mime_type['png'] = array(  
                                                                      'image/png'
                                                                  );
        $all_available_extension_with_mime_type['jpg'] = array(  
                                                                      'image/pjpeg', 
                                                                      'image/jpeg'
                                                                  );
        $all_available_extension_with_mime_type['jpeg'] = array(  
                                                                      'image/pjpeg', 
                                                                      'image/jpeg'
                                                                  );
        $all_available_extension_with_mime_type['gif'] = array(  
                                                                      'image/gif'
                                                                  );
        $all_available_extension_with_mime_type['tif'] = array(  
                                                                      'image/tiff', 
                                                                      'image/x-tiff'
                                                                  );
        $all_available_extension_with_mime_type['tiff'] = array(  
                                                                      'image/tiff', 
                                                                      'image/x-tiff'
                                                                  );
        $all_available_extension_with_mime_type['swf'] = array(  
                                                                      'application/x-shockwave-flash'
                                                                  );
        $all_available_extension_with_mime_type['docx'] = array(  
                                                                      'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                                                                  );
        $all_available_extension_with_mime_type['xlsx'] = array(  
                                                                      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                                                                  );
        $all_available_extension_with_mime_type['pptx'] = array(  
                                                                      'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                                                                  );
        $all_available_extension_with_mime_type['csv'] = array(  
                                                                      'text/csv'
                                                                  );
        $all_available_extension_with_mime_type['crtfsv'] = array(  
                                                                      'application/rtf',
                                                                      'application/x-rtf',
                                                                      'text/richtext',
                                                                  );
        $all_available_extension_with_mime_type['pages'] = array(  
                                                                      'application/x-iwork-pages-sffpages'
                                                                  );
        $all_available_extension_with_mime_type['numbers'] = array(  
                                                                      'application/x-iwork-numbers-sffnumbers'
                                                                  );
        $all_available_extension_with_mime_type['keynote'] = array(  
                                                                      'application/x-iwork-keynote-sffkey'
                                                                  );
        $all_available_extension_with_mime_type['ies'] = array(  
                                                                      'application/octet-stream'
                                                                  );
        return $all_available_extension_with_mime_type;
    }
    
    public static function get_supported_extension_with_mime_type(){
        $plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
        $supported_extension = array();
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['supported_extension']) ){
                $supported_extension = $plugin_settings['supported_extension'];
			}
		}
        //pdf is mandatory supported
        if( !in_array( 'pdf', $supported_extension ) ){
            $supported_extension[] = 'pdf';
        }
        
        $all_available_extension_and_mime_type = self::get_available_extension_with_mime_type();
        foreach( $all_available_extension_and_mime_type as $key => $mime_type ){
            if( !in_array( $key, $supported_extension ) ){
                unset( $all_available_extension_and_mime_type[$key] );
                continue;
            }
        }
        
        return $all_available_extension_and_mime_type;
    }
    
    public static function get_cat_pdfs_count( $cat_id ){
        global $wpdb;
        
        $pdfs_tbl = $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name;
        $cats_tbl = $wpdb->prefix.BSKPDFManager::$_cats_tbl_name;
        $rels_tbl = $wpdb->prefix.BSKPDFManager::$_rels_tbl_name;
        
        $sql = 'SELECT COUNT(P.`id`) FROM `'.$pdfs_tbl.'` AS P LEFT JOIN `'.$rels_tbl.'` AS R ON P.`id` = R.`pdf_id` '.
                 'WHERE R.`cat_id` = '.$cat_id.' ';
        
        return $wpdb->get_var( $sql );
    }
    
}//end of class
