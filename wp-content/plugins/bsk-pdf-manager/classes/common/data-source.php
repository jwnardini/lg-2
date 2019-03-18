<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class BSKPDFM_Common_Data_Source {
    
    
    public static function bsk_pdfm_get_pdfs( $args ){
        global $wpdb;
        
        $where_case_array = array();
        $prepare_values_array = array();
        
        //process id
        if( $args['show_all_pdfs'] ) {
            $where_case_array[] = 'WHERE 1 ';
        }else{
            $ids_array = $args['ids_array'];
            $where_case_array[] = 'WHERE `id` IN('.implode(',', $ids_array).')';
        }
        
        $most_top = isset( $args['most_top'] ) ? absint($args['most_top']) : false;
        $limit_case = '';
        $total_results_count = 0;
        $total_pages = 0;
        if( $most_top > 0 ){
            $limit_case = ' LIMIT 0, '.$most_top;
        }
        
        //process order by case
		$order_by_str = ' ORDER BY `title`'; //default set to title
		$order_str = ' ASC';
		if( $args['order_by'] == 'title' ){
			//default
		}else if( $args['order_by'] == 'date' ){
			$order_by_str = ' ORDER BY `last_date`';
		}else if( $args['order_by'] == 'id' ){
			$order_by_str = ' ORDER BY `id`';
		}
        
		if( strtoupper(trim($args['order'])) == 'DESC' ){
			$order_str = ' DESC';
		}
        $order_case = $order_by_str.$order_str;
        
        if( $args['order_by'] == "" && $args['ids_array'] && is_array( $args['ids_array'] ) && count( $args['ids_array'] ) > 0 ){
            $order_case = ' ORDER BY FIELD(`id`, '.implode(',', $args['ids_array']).')';
        }
        
        
        $sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` '.
                 implode( ' ', $where_case_array ).
                 $order_case.
                 $limit_case;
        $sql = $wpdb->prepare( $sql, $prepare_values_array );
        $results = $wpdb->get_results( $sql );
        if( !$results || !is_array( $results ) || count( $results ) < 1 ){
            return false;
        }

        $pdf_id_as_key_array = array();
        foreach( $results as $obj ){
            $pdf_id_as_key_array[$obj->id] = $obj;
        }
        
        //sort pdfs by id sequence order
        if( $args['ids_array'] && is_array( $args['ids_array'] ) && $args['order_by'] == "" ){
            $pdfs_results_array = array();
            foreach( $args['ids_array'] as $pdf_id ){
                if( !isset($pdf_id_as_key_array[$pdf_id]) ){
                    continue;
                }
                $pdfs_results_array[$pdf_id] = $pdf_id_as_key_array[$pdf_id];
            }
            return array( 'pdfs' => $pdfs_results_array );
        }
        
        return array( 'pdfs' => $pdf_id_as_key_array );
    }
    
    public static function bsk_pdfm_get_pdfs_by_cat( $args ){
        global $wpdb;
        
        $pdfs_tbl = $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name;
        $cats_tbl = $wpdb->prefix.BSKPDFManager::$_cats_tbl_name;
        $rels_tbl = $wpdb->prefix.BSKPDFManager::$_rels_tbl_name;
        
        $where_case_array = array();
        $prepare_values_array = array();
        
        //process category id
        $ids_array = $args['ids_array'];
        if( !$ids_array || !is_array( $ids_array ) || count( $ids_array ) < 1 ){
            return false;
        }
        
        $where_case_array[] = 'WHERE C.`id` IN('.trim(str_repeat( '%d,', count( $ids_array ) ), ',').')';
        foreach( $ids_array as $cat_id_to_query ){
            $prepare_values_array[] = $cat_id_to_query;
        } 

        
        $most_top = isset( $args['most_top'] ) ? intval($args['most_top']) : false;
        $limit_case = '';
        $total_results_count = 0;
        $total_pages = 0;
        if( $most_top > 0 ){
            $limit_case = ' LIMIT 0, '.$most_top;
        }
        
        //process order by case
        $cat_order_by_str = ' FIELD( C.`id`, '.implode(',', $ids_array).' )';
		$pdf_order_by_str = ' P.`title`'; //default set to title
        $pdf_order_str = ' ASC';
        if( $args['order_by'] == 'last_date' || $args['order_by'] == 'date' ){
			$pdf_order_by_str = ' P.`last_date`';
		}else if( $args['order_by'] == 'id' ){
			$pdf_order_by_str = ' P.`id`';
		}
		if( strtoupper(trim($args['order'])) == 'DESC' ){
			$pdf_order_str = ' DESC';
		}
        $order_case = ' ORDER BY '.$cat_order_by_str.','.$pdf_order_by_str.$pdf_order_str;
        
        $sql = 'SELECT P.`id`, P.`title`, P.`file_name`, P.`description`, P.`last_date`, P.`thumbnail_id`, P.`by_media_uploader`, '.
                 'C.`id` AS `cat_id`, C.`title` AS `cat_title`, C.`parent` AS `cat_parent`, '.
                 'C.`description` AS `cat_desc`, C.`empty_message` as `cat_empty_msg` FROM `'.
                 $rels_tbl.'` AS R LEFT JOIN `'.$pdfs_tbl.'` AS P ON R.`pdf_id` = P.`id` LEFT JOIN `'.$cats_tbl.'` AS C ON R.`cat_id` = C.`id`'.
                 implode( ' ', $where_case_array ).
                 $order_case.
                 $limit_case;
        $sql = $wpdb->prepare( $sql, $prepare_values_array );
        $results = $wpdb->get_results( $sql );
        if( !$results || !is_array( $results ) || count( $results ) < 1 ){
            return false;
        }
        
        $pdf_by_category_array = array();
        $categories_for_pdfs = array();
        foreach( $results as $obj ){
            if( !isset( $pdf_by_category_array[$obj->cat_id] ) ){
                $pdf_by_category_array[$obj->cat_id] = array();
            }
            $pdf_by_category_array[$obj->cat_id][$obj->id] = $obj;
            $categories_for_pdfs[$obj->cat_id] = $obj->cat_id;
         }

        return array( 
                            'pdfs' => $pdf_by_category_array, 
                            'categories_for_pdfs' => $categories_for_pdfs 
                         );
    }
    
    public static function bsk_pdfm_get_sub_cat( $parent_id, $cat_order_by, $cat_order ){
        global $wpdb;
        
        $cats_tbl = $wpdb->prefix.BSKPDFManager::$_cats_tbl_name;
        $rels_tbl = $wpdb->prefix.BSKPDFManager::$_rels_tbl_name;
        
        //process order by case
        $cat_order_by_str = ' C.`title`';
		$cat_order_str = ' ASC';
		if( $cat_order_by == 'date' ){
			$cat_order_by_str = ' C.`last_date`';
		}
		if( trim($cat_order) == 'DESC' ){
			$cat_order_str = ' DESC';
		}
        
        $sql = 'SELECT * FROM `'.$cats_tbl.'` AS C '.
                 'WHERE C.`parent` = %d '.
                 'ORDER BY '.$cat_order_by_str.$cat_order_str;
        $sql = $wpdb->prepare( $sql, $parent_id );
        $results = $wpdb->get_results( $sql );
        if( !$results || !is_array( $results ) || count( $results ) < 1 ){
            return false;
        }
        $array_to_return = array();
        foreach( $results as $cat_obj ){
            $array_to_return[$cat_obj->id] = $cat_obj;
        }
        
        return $array_to_return;
    }
    
    public static function bsk_pdfm_get_cat_obj( $cat_id ){
        global $wpdb;
        
        $cats_tbl = $wpdb->prefix.BSKPDFManager::$_cats_tbl_name;
        $sql = 'SELECT * FROM `'.$cats_tbl.'` AS C '.
                 'WHERE C.`id` = %d ';
        $sql = $wpdb->prepare( $sql, $cat_id );
        $results = $wpdb->get_results( $sql );
        if( !$results || !is_array( $results ) || count( $results ) < 1 ){
            return false;
        }
        
        return $results[0];
    }
    
    public static function bsk_pdfm_organise_categories_id_sequence(  
                                                                                                    $id_string, 
                                                                                                    $cat_order_by_str, 
                                                                                                    $cat_order_str 
                                                                                                  ){
        global $wpdb;
        
        if( trim($id_string) == "" ){
            return false;
        }

        $ids_array = array();
        $categories_loop_array = array();
        
        $temp_valid_array = array();
        $sql_base = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` AS C '.
                         'WHERE 1 ';
        $sql = '';
		if( strtoupper($id_string) == 'ALL' ){
            $sql = $sql_base.
                      ' ORDER BY '.$cat_order_by_str.$cat_order_str;
		}else{
			$temp_array = explode(',', $id_string);
            $temp_valid_array = array();
            foreach($temp_array as $key => $cat_id){
                $cat_id = absint(trim($cat_id));
                $temp_valid_array[] = $cat_id;
            }
            
            if( !is_array($temp_valid_array) || count($temp_valid_array) < 1 ){
				return false;
			}
			$sql = $sql_base.
                     ' AND C.`id` IN('.implode(',', $temp_valid_array).') '.
                     ' ORDER BY '.$cat_order_by_str.$cat_order_str;
		}
        //query
        $categories_results = $wpdb->get_results( $sql );
        if( !$categories_results || !is_array( $categories_results) || count( $categories_results ) < 1 ){
            return false;
        }
        
        foreach( $categories_results as $cat_obj ){
            $ids_array[] = $cat_obj->id;
            $categories_loop_array[$cat_obj->id] = $cat_obj;
        }
        return array( 'ids_array' => $ids_array, 'categories_loop' => $categories_loop_array );
    }
}//end of class
