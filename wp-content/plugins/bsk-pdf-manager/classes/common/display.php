<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class BSKPDFM_Common_Display {
    
    //for get parent category structure
    private static $current_parent_category_depth_to_get_options = 0;
    //for get all category structure
    private static $current_category_depth_to_get_options = 0;

    public static  function get_year_month_filter_in_category( $category_id, $filter_format, $label_format, $filter_order = 'DESC' ) {
        global $wpdb;
        
        $sql = 'SELECT DISTINCT( CONCAT(LEFT(`last_date`, 7), "-01 00:00:00") ) AS filter FROM `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` '.
                 'WHERE `cat_id` LIKE "%-cat-'.$category_id.'-%" '.
                 'ORDER BY `last_date` '.$filter_order;
        $results = $wpdb->get_results( $sql );
        
        $return_array = array();
        foreach( $results as $filter_date ){
            $timestamp = strtotime($filter_date->filter);
            $data = date($filter_format, $timestamp);
            $label = date( $label_format, $timestamp);
            $return_array[$data] = array( 'value' => $data, 'label' => $label );
        }
        
        return $return_array;
    }
    
    public static function validate_year( $year_str ){
        if( trim( $year_str ) == "" ){
            return false;
        }
        $year_array = explode( '-', $year_str );
        if( !$year_array || !is_array( $year_array) || count( $year_array ) < 1 ){
            return false;
        }
        $year_from = intval( $year_array[0] );
        if( $year_from < 1000 || $year_from > 9999 ){
            $year_from = false;
        }
        $year_to = false;
        if( count( $year_array ) > 1 ){
            $year_to = intval( $year_array[1] );
            if( $year_to < 1000 || $year_to > 9999 ){
                $year_to = false;
            }
        }
        
        if( $year_from && $year_to ){
            return array( 'from' => $year_from, 'to' => $year_to );
        }
        
        $return = $year_from ? $year_from.'' : ( $year_to ? $year_to.'' : false );
        
        return $return;
    }
    
    public static function validate_month( $month_str ){
        $month_int = intval( $month_str );
        if( ( $month_int < 1 || $month_int > 12 ) && $month_int != 99 ){
            return false;
        }
        $month_str = $month_int.'';
        if( $month_int < 10 ){
            $month_str = '0'.$month_str;
        }
        
        return $month_str;
    }
    
    public static function validate_day( $day_str ){
        $day_int = intval( $day_str );
        if( ( $day_int < 1 || $day_int > 31 ) && $day_int != 99 ){
            return false;
        }
        $day_str = $day_int.'';
        if( $day_int < 10 ){
            $day_str = '0'.$day_str;
        }
        
        return $day_str;
    }
    
    public static function get_mysql_weekday_index( $weekday ){
        $return = false;
        
        switch( $weekday ){
            case 'MON':
                $return = 0;
            break;
            case 'TUE':
                $return = 1;
            break;
            case 'WED':
                $return = 2;
            break;
            case 'FRI':
                $return = 4;
            break;
            case 'SAT':
                $return = 5;
            break;
            case 'SUN':
                $return = 6;
            break;
        }
        
        return $return;
    }
    
    public static function is_leap_year( $year ) {
        return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year % 400) == 0)));
    }
    
    public static function process_shortcodes_bool_attrs( $attr_name, $attrs_array ) {
        if( !$attrs_array || !is_array($attrs_array) || count($attrs_array) < 1 || $attr_name == "" ){
            return false;
        }
        if( !isset($attrs_array[$attr_name]) ){
            return false;
        }
        
        $return_bool = false;
        if( $attrs_array[$attr_name] && is_string($attrs_array[$attr_name]) ){
			$return_bool = strtoupper($attrs_array[$attr_name]) == "YES" ? true : false;
			if( $return_bool == false ){
				$return_bool = strtoupper($attrs_array[$attr_name]) == 'TRUE' ? true : false;
			}
		}else if( is_bool($attrs_array[$attr_name]) ){
			$return_bool = $attrs_array[$attr_name];
		}

        return $return_bool;
    }//end of function
    
    public static function show_pdfs_in_column( 
                                                                     $only_column_single,
                                                                     $pdf_items_results,
                                                                     $show_description,
                                                                     $featured_image, $featured_image_size, 
                                                                     $default_thumbnail_html,
                                                                     $target, $nofollow_tag, $columns,
                                                                     $show_pdf_title, $pdf_title_position,
                                                                     $show_date_in_title, $date_format_str, $date_before_title
                                                                  ){
        if( !$pdf_items_results || !is_array($pdf_items_results) || count($pdf_items_results) < 1 ){
            return '';
        }

        $column_class = '';
        switch( $columns ){
            case 2:
                $column_class = ' bsk-pdfm-one-half';
            break;
            case 3:
                $column_class = ' bsk-pdfm-one-third';
            break;
            case 4:
                $column_class = ' bsk-pdfm-one-fourth';
            break;
            case 5:
                $column_class = ' bsk-pdfm-one-fifth';
            break;
            case 6:
                $column_class = ' bsk-pdfm-one-sixth';
            break;
        }
        
        $featured_image_class = $featured_image ? ' bsk-pdfm-with-featured-image' : ' bsk-pdfm-without-featured-image';
        if( $featured_image_class ){
            $featured_image_class .= ' title-'.$pdf_title_position.'-featured-image';
        }
        $forStr = '<div class="bsk-pdfm-pdfs-columns-list'.$featured_image_class.'">';
        if( $only_column_single ){
            $forStr = '';
        }
        $item_count = 0;
        foreach($pdf_items_results as $pdf_item_obj ){
            if( $pdf_item_obj->file_name == "" &&  $pdf_item_obj->by_media_uploader < 1 ){
                continue;
            }
            $file_url = '';
            if( $pdf_item_obj->by_media_uploader ){
                $file_url = wp_get_attachment_url( $pdf_item_obj->by_media_uploader );
            }else if( file_exists(ABSPATH.$pdf_item_obj->file_name) ){
                $file_url = site_url().'/'.$pdf_item_obj->file_name;
            }
            if( $file_url == "" ){
                continue;
            }

            $column_class_item = ( $item_count % $columns ) == 0 ? $column_class.' bsk-pdfm-first' : $column_class;
            $forStr .= self::show_pdf_item_single_div( 
                                                                         $pdf_item_obj, 
                                                                         $show_description,
                                                                         $featured_image, $featured_image_size, 
                                                                         $default_thumbnail_html,
                                                                         $target, $nofollow_tag, $column_class_item,
                                                                         $show_pdf_title, $pdf_title_position,
                                                                         $show_date_in_title, $date_format_str, $date_before_title
                                                                      );
            $item_count++;
        }
        
        if( !$only_column_single ){
            $forStr .= '</div><!-- // bsk-pdfm-pdfs-columns-list -->';
        }
        
        return $forStr;
    }
        
    public static function show_pdf_item_single_div( 
                                                     $pdf_item_obj, 
                                                     $description,
                                                     $featured_image, $featured_image_size, 
                                                     $default_thumbnail_html,
												     $open_target_str, $nofollow_tag_str, $column_class_item,
                                                     $show_pdf_title, $pdf_title_position,
                                                     $show_date_in_title, $date_format_str, $date_before_title
                                                   ){
        $date_class = ' data-date="'.date('Y-m-d-D', strtotime($pdf_item_obj->last_date) ).'"';
		
		$file_url = site_url().'/'.$pdf_item_obj->file_name;
		if( $pdf_item_obj->by_media_uploader ){
			$file_url = wp_get_attachment_url( $pdf_item_obj->by_media_uploader );
            if( $file_url == false ){
                return '';
            }
		}
		
        //PDF titile str
		$pdf_item_obj_title = $pdf_item_obj->title;
        $pdf_title_str = $pdf_item_obj_title;
		if( $pdf_title_str == "" ){
			$pdf_item_obj_title_array = explode( '/', $file_url );
			$pdf_title_str = $pdf_item_obj_title_array[count($pdf_item_obj_title_array) - 1];
		}
        if( $show_date_in_title ){
            $date_str = '<span class="bsk-pdfm-pdf-date">'.date($date_format_str, strtotime($pdf_item_obj->last_date)).'</span>';
            $pdf_title_str = $date_before_title ? $date_str.$pdf_title_str : $pdf_title_str.$date_str;
        }
        
        //get PDF featured image str
        $featured_image_str = '';
        $thumbnail_html = '';
		if( $featured_image == true ){
			if( $pdf_item_obj->thumbnail_id  && get_post( $pdf_item_obj->thumbnail_id ) ){
				$thumbnail_html = wp_get_attachment_image( $pdf_item_obj->thumbnail_id, $featured_image_size );
			}else if( $default_thumbnail_html){
				$thumbnail_html = $default_thumbnail_html;
			}else{
				$thumbnail_html = '<img src="'.BSKPDFManager::$_default_pdf_icon_url.'" width="150" height="150" />';
			}
			$featured_image_str = '<a href="'.$file_url.'" '.$open_target_str.$nofollow_tag_str.' title="'.$pdf_item_obj->title.'" class="bsk-pdfm-pdf-link-for-featured-image pdf-id-'.$pdf_item_obj->id.'">'."\n".$thumbnail_html."\n".'</a>';
        }
        
        //description str
        $description_str = '';
        if( $description && $pdf_item_obj->description ){
            $description_str = "\n".'<p class="bsk-pdfm-pdf-description">'.$pdf_item_obj->description.'</p>'."\n";
        }
        
        //pdf title link
        $pdf_title_str = '<a href="'.$file_url.'"'.$open_target_str.$nofollow_tag_str.'  title="'.$pdf_item_obj_title.'" class="bsk-pdfm-pdf-link-for-title pdf-id-'.$pdf_item_obj->id.'">'.$pdf_title_str.'</a>';
        /*
          * organise return str
          */
        $forStr = '<div class="bsk-pdfm-columns-single'.$column_class_item.' pdf-id-'.$pdf_item_obj->id.'"'.$date_class.'>';
        if( $featured_image_str ){
            if( $show_pdf_title && $pdf_title_position == 'above' ){
                $forStr .= "\n".'<h3>'.$pdf_title_str.'</h3>'."\n";
            }
            $forStr .= $featured_image_str;
            if( $show_pdf_title && $pdf_title_position == 'below' ){
                $forStr .= '<h3>'.$pdf_title_str.'</h3>';
            }
        }else{
            $forStr .= '<h3>'.$pdf_title_str.'</h3>';
        }
        $forStr .= $description_str;
        $forStr .= '</div>'."\n";
		
		return $forStr;
	}
    
    public static function show_pdfs_in_dropdown( $pdf_items_results, 
                                                                        $class, 
                                                                        $option_none_str, 
                                                                        $target, 
                                                                        $show_date_in_title, 
                                                                        $date_format_str,
                                                                        $date_before_title
                                                                      ){
        if( !$pdf_items_results || !is_array($pdf_items_results) || count($pdf_items_results) < 1 ){
            return '';
        }
        $forStr = '';
        $forStr .= '<select class="'.$class.'"'.$target.'>'."\n";
        if( $option_none_str ){
            $forStr .= '<option value="" selected="selected">'.$option_none_str.'</option>';
        }
        foreach($pdf_items_results as $pdf_item_obj ){
            if( $pdf_item_obj->file_name == "" &&  $pdf_item_obj->by_media_uploader < 1 ){
                continue;
            }
            $file_url = '';
            if( $pdf_item_obj->by_media_uploader ){
                $file_url = wp_get_attachment_url( $pdf_item_obj->by_media_uploader );
            }else if( file_exists(ABSPATH.$pdf_item_obj->file_name) ){
                $file_url = site_url().'/'.$pdf_item_obj->file_name;
            }
            if( $file_url == "" ){
                continue;
            }

            $option_text = $pdf_item_obj->title;
            if( $show_date_in_title ){
                if( $date_before_title ){
                    $option_text = date($date_format_str, strtotime($pdf_item_obj->last_date)).$option_text;
                }else{
                    $option_text .= date($date_format_str, strtotime($pdf_item_obj->last_date));
                }
            }
            $id_str = ' id="'.date('Y-m-d-D', strtotime($pdf_item_obj->last_date) ).'-'.$pdf_item_obj->id.'"';
            $forStr .= '<option value="'.$file_url.'"'.$id_str.'>'.$option_text.'</option>'."\n";
        }
        $forStr .= '</select>';
        
        return $forStr;
    }
    
    public static function show_pdfs_dropdown_option_for_category(   
                                                                                        $pdf_results_of_the_category, 
                                                                                        $category_obj,
                                                                                        $show_date_in_title, 
                                                                                        $date_format_str,
                                                                                        $date_before_title,
                                                                                        $depth
                                                                                     ){
        $prefix = '';
        if( $depth == 2 ){
            $prefix = apply_filters( 'bsk_pdfm_filter_dropdown_option_prefix', '&#8212;&nbsp;', 2 );
        }else if( $depth == 3 ){
            $prefix = apply_filters( 'bsk_pdfm_filter_dropdown_option_prefix', '&#8212;&nbsp;&#8212;&nbsp;', 3 );
        }
        
        
        $forStr = '';
        if( !isset( $pdf_results_of_the_category ) || 
            !is_array( $pdf_results_of_the_category ) || 
            count( $pdf_results_of_the_category ) < 1 ){

            return $forStr;
        }
        
        foreach( $pdf_results_of_the_category as $pdf_item_obj ){
            if( $pdf_item_obj->file_name == "" &&  $pdf_item_obj->by_media_uploader < 1 ){
                continue;
            }
            $file_url = '';
            if( $pdf_item_obj->by_media_uploader ){
                $file_url = wp_get_attachment_url( $pdf_item_obj->by_media_uploader );
            }else if( file_exists(ABSPATH.$pdf_item_obj->file_name) ){
                $file_url = site_url().'/'.$pdf_item_obj->file_name;
            }
            if( $file_url == "" ){
                continue;
            }

            $option_text = $pdf_item_obj->title;
            if( $show_date_in_title ){
                if( $date_before_title ){
                    $option_text = date($date_format_str, strtotime($pdf_item_obj->last_date)).$option_text;
                }else{
                    $option_text .= date($date_format_str, strtotime($pdf_item_obj->last_date));
                }
            }
            $id_str = ' id="'.date('Y-m-d-D', strtotime($pdf_item_obj->last_date) ).'-'.$pdf_item_obj->id.'"';
            $forStr .= '<option value="'.$file_url.'"'.$id_str.'>'.$prefix.$option_text.'</option>'."\n";
        }
        
        return $forStr;
    }
    
    public static function display_pdfs_in_ul_or_ol(
                                                                     $ul_or_ol,
                                                                     $only_li,
                                                                     $ul_or_ol_class,
                                                                     $pdf_items_results, 
                                                                     $target, $nofollow_tag, 
                                                                     $show_date_in_title, $date_format_str, $date_before_title,
                                                                     $pdf_title_tag
                                                                  ){
        
        if( !$pdf_items_results || !is_array($pdf_items_results) || count($pdf_items_results) < 1 ){
            return '';
        }
        
        $ul_or_ol_class .= ' bsk-pdfm-without-featured-image';
        $ul_or_ol_class .= ' bsk-pdfm-without-description';
        $ul_or_ol_class .= ' bsk-pdfm-with-title';
        
        $i_list_item = 1;
        $forStr = $only_li ? '' : '<'.$ul_or_ol.' class="'.$ul_or_ol_class.'">'."\n";
        foreach($pdf_items_results as $pdf_item_obj ){
            if( $pdf_item_obj->file_name == "" &&  $pdf_item_obj->by_media_uploader < 1 ){
                continue;
            }
            $file_url = '';
            if( $pdf_item_obj->by_media_uploader ){
                $file_url = wp_get_attachment_url( $pdf_item_obj->by_media_uploader );
            }else if( file_exists(ABSPATH.$pdf_item_obj->file_name) ){
                $file_url = site_url().'/'.$pdf_item_obj->file_name;
            }
            if( $file_url == "" ){
                continue;
            }

            $list_item_class = $i_list_item % 2 ? ' list-item-odd' : ' list-item-even';
            $forStr .= self::show_pdf_item_single_li(     
                                                                         $ul_or_ol,
                                                                         $pdf_item_obj, 
                                                                         $target, $nofollow_tag, 
                                                                         $show_date_in_title, $date_format_str, $date_before_title,
                                                                         $list_item_class,
                                                                         $pdf_title_tag
                                                                   );
            $i_list_item++;
        }
        $forStr .= $only_li ? '' : '</'.$ul_or_ol.'>';
        
        return $forStr;
    }
    
    public static function show_pdf_item_single_li( 
                                                     $ul_or_ol,
                                                     $pdf_item_obj, 
												     $open_target_str, $nofollow_tag_str, 
                                                     $show_date_in_title, $date_format_str, $date_before_title,
                                                     $list_item_class,
                                                     $pdf_title_tag
                                                   ){
		$date_filter = ' data-date="'.date('Y-m-d-D', strtotime($pdf_item_obj->last_date) ).'"';
		
		$file_url = site_url().'/'.$pdf_item_obj->file_name;
		
        //PDF titile str
		$pdf_item_obj_title = $pdf_item_obj->title;
        $pdf_title_str = $pdf_item_obj_title;
		if( $pdf_title_str == "" ){
			$pdf_item_obj_title_array = explode( '/', $file_url );
			$pdf_title_str = $pdf_item_obj_title_array[count($pdf_item_obj_title_array) - 1];
		}
        if( $show_date_in_title ){
            $date_str = '<span class="bsk-pdfm-pdf-date">'.date($date_format_str, strtotime($pdf_item_obj->last_date)).'</span>';
            $pdf_title_str = $date_before_title ? $date_str.$pdf_title_str : $pdf_title_str.$date_str;
        }
        
        //pdf title link
        $pdf_title_str = '<a href="'.$file_url.'"'.$open_target_str.$nofollow_tag_str.'  title="'.$pdf_item_obj_title.'" class="bsk-pdfm-pdf-link-for-title pdf-id-'.$pdf_item_obj->id.'">'.$pdf_title_str.'</a>';

        /*
          * organise return str
          */
        $forStr  = '<li class="bsk-pdfm-list-item'.$list_item_class.'"'.$date_filter.' date-id="'.$pdf_item_obj->id.'">'."\n";
        $forStr .= $pdf_title_str;
        $forStr .= '</li>'."\n";

        return $forStr;
	}
    
    public static function show_pdfs_link_only(
                                                                 $pdf_items_results, 
                                                                 $target, $nofollow_tag, 
                                                                 $show_date_in_title, $date_format_str, $date_before_title
                                                              ){
        
        if( !$pdf_items_results || !is_array($pdf_items_results) || count($pdf_items_results) < 1 ){
            return '';
        }
        
        $forStr = '';
        foreach($pdf_items_results as $pdf_item_obj ){
            if( $pdf_item_obj->file_name == "" &&  $pdf_item_obj->by_media_uploader < 1 ){
                continue;
            }
            $file_url = '';
            if( $pdf_item_obj->by_media_uploader ){
                $file_url = wp_get_attachment_url( $pdf_item_obj->by_media_uploader );
            }else if( file_exists(ABSPATH.$pdf_item_obj->file_name) ){
                $file_url = site_url().'/'.$pdf_item_obj->file_name;
            }
            if( $file_url == "" ){
                continue;
            }

            $pdf_item_obj_title = $pdf_item_obj->title;
            if( $pdf_item_obj_title == "" ){
                $pdf_item_obj_title_array = explode( '/', $file_url );
                $pdf_item_obj_title = $pdf_item_obj_title_array[count($pdf_item_obj_title_array) - 1];
            }


            $link_text = $pdf_item_obj_title;
            if( $show_date_in_title ){
                if( $date_before_title ){
                    $link_text = '<span class="bsk-pdfm-pdf-date">'.date($date_format_str, strtotime($pdf_item_obj->last_date)).'</span>'.$pdf_item_obj_title;
                }else{
                    $link_text .= '<span class="bsk-pdfm-pdf-date">'.date($date_format_str, strtotime($pdf_item_obj->last_date)).'</span>';
                }
            }

            $forStr .= '<a href="'.$file_url.'"'.$target.$nofollow_tag.'  title="'.$pdf_item_obj_title.'" class="bsk-pdfm-pdf-link pdf-id-'.$pdf_item_obj->id.'">'.$link_text.'</a>'."\n";
        }
        
        return $forStr;
    }
    
    public static function show_pdfs_url_only(
                                                                 $pdf_items_results
                                                              ){
        
        if( !$pdf_items_results || !is_array($pdf_items_results) || count($pdf_items_results) < 1 ){
            return '';
        }
        
        $forStr = '';
        foreach($pdf_items_results as $pdf_item_obj ){
            if( $pdf_item_obj->file_name == "" &&  $pdf_item_obj->by_media_uploader < 1 ){
                continue;
            }
            $file_url = '';
            if( $pdf_item_obj->by_media_uploader ){
                $file_url = wp_get_attachment_url( $pdf_item_obj->by_media_uploader );
            }else if( file_exists(ABSPATH.$pdf_item_obj->file_name) ){
                $file_url = site_url().'/'.$pdf_item_obj->file_name;
            }
            if( $file_url == "" ){
                continue;
            }

            $forStr .= $file_url;
        }
        
        return $forStr;
    }
    
    public static function get_category_dropdown( 
                                                                        $categories_loop_array, 
                                                                        $availabe_ids_array, 
                                                                        $selected_cat_id,
                                                                        $show_cat_hierarchical,
                                                                        $cat_order_by_str,
                                                                        $cat_order_str,
                                                                        $option_null_str,
                                                                        $hide_empty_cat
                                                                     ) {
        global $wpdb;
        
        $selector_str = '<select class="bsk-pdfm-category-dropdown">';
        $selector_str .= trim($option_null_str) == "" ? '' : '<option value="">'.trim($option_null_str).'</option>';
        foreach( $categories_loop_array as $cat_obj ){
            if( $hide_empty_cat ){
                $sql = 'SELECT COUNT(*) FROM `'.$wpdb->prefix.BSKPDFManager::$_rels_tbl_name.'` AS R LEFT JOIN  '.
                         '`'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` AS P ON R.`pdf_id` = P.`id` '.
                         'WHERE R.`cat_id` = '.$cat_obj->id;
                if( $wpdb->get_var( $sql ) < 1 ){
                    continue;
                }
            }
            $selected_str = $selected_cat_id == $cat_obj->id ? ' selected' : '';
            $selector_str .= '<option value="'.$cat_obj->id.'"'.$selected_str.'>'.$cat_obj->title.'</option>';
            
            if( $show_cat_hierarchical ){
                //show child categories
                $sql_base = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` AS C '.
                                 'WHERE 1 AND C.`id` IN('.implode(',', $availabe_ids_array).') ';
                //child categories
                $sql = $sql_base.' AND C.`parent` = '.$cat_obj->id.' ORDER BY '.$cat_order_by_str.$cat_order_str;
                $child_results = $wpdb->get_results( $sql );
                if( $child_results && is_array( $child_results ) && count( $child_results ) > 0 ){
                    foreach( $child_results as $child_cat_obj ){
                        if( $hide_empty_cat ){
                            $sql = 'SELECT COUNT(*) FROM `'.$wpdb->prefix.BSKPDFManager::$_rels_tbl_name.'` AS R LEFT JOIN  '.
                                     '`'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` AS P ON R.`pdf_id` = P.`id` '.
                                     'WHERE R.`cat_id` = '.$child_cat_obj->id;
                            if( $wpdb->get_var( $sql ) < 1 ){
                                continue;
                            }
                        }
                        $prefix = apply_filters( 'bsk_pdfm_filter_selector_option_prefix', '&#8212;&nbsp;', 2 );
                        $selected_str = $selected_cat_id == $child_cat_obj->id ? ' selected' : '';
                        $selector_str .= '<option value="'.$child_cat_obj->id.'"'.$selected_str.'>'.$prefix.$child_cat_obj->title.'</option>';
                        //grand categories
                        $sql = $sql_base.' AND C.`parent` = '.$child_cat_obj->id.' ORDER BY '.$cat_order_by_str.$cat_order_str;
                        $grand_results = $wpdb->get_results( $sql );
                        if( $grand_results && is_array( $grand_results ) && count( $grand_results ) > 0 ){
                            foreach( $grand_results as $grand_cat_obj ){
                                if( $hide_empty_cat ){
                                    $sql = 'SELECT COUNT(*) FROM `'.$wpdb->prefix.BSKPDFManager::$_rels_tbl_name.'` AS R LEFT JOIN  '.
                                             '`'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` AS P ON R.`pdf_id` = P.`id` '.
                                             'WHERE R.`cat_id` = '.$grand_cat_obj->id;
                                    if( $wpdb->get_var( $sql ) < 1 ){
                                        continue;
                                    }
                                }
                                $prefix = apply_filters( 'bsk_pdfm_filter_selector_option_prefix', '&#8212;&nbsp;&#8212;&nbsp;', 3 );
                                $selected_str = $selected_cat_id == $grand_cat_obj->id ? ' selected' : '';
                                $selector_str .= '<option value="'.$grand_cat_obj->id.'"'.$selected_str.'>'.$prefix.$grand_cat_obj->title.'</option>';
                            }
                        }//end for grand
                    }
                }//end for child
            } //end for hierarchical
        }//end foreach
        $selector_str .= '</select>';
        
        return $selector_str;
    } //end of function
    
    public static  function get_category_selector( 
                                                                    $categories_loop_array, 
                                                                    $availabe_ids_array, 
                                                                    $selected_cat_id,
                                                                    $show_cat_hierarchical,
                                                                    $cat_order_by_str,
                                                                    $cat_order_str,
                                                                    $option_null_str, 
                                                                    $hide_empty_cat ) {

        $selector_str = '<div class="bsk-pdfm-category-selector-container">';
        $selector_str .= self::get_category_dropdown( 
                                                                        $categories_loop_array, 
                                                                        $availabe_ids_array, 
                                                                        $selected_cat_id,
                                                                        $show_cat_hierarchical,
                                                                        $cat_order_by_str,
                                                                        $cat_order_str,
                                                                        $option_null_str,
                                                                        $hide_empty_cat
                                                                     );
        $ajax_loader_span_class = 'bsk-pdfm-category-selector-ajax-loader';
        $ajax_loader_img_url = apply_filters( 'bsk-pdfm-filter-ajax-loader-url', BSKPDFManager::$_ajax_loader_img_url, $ajax_loader_span_class );
        $selector_str .= '<span class="'.$ajax_loader_span_class.'"><img src="'.$ajax_loader_img_url.'" /></span>';
        
        $selector_str .= '</div>';
        
        return $selector_str;
    }
    
    public static function show_category_password_require_form( $cat_obj, $show_password_error ){
        $ajax_loader_span_class = 'bsk-pdfm-category-password-verify-ajax-loader';
        $ajax_loader_img_url = apply_filters( 'bsk-pdfm-filter-ajax-loader-url', 
                                                             BSKPDFManager::$_ajax_loader_img_url, 
                                                             $ajax_loader_span_class 
                                                           );
        $ajax_loader_str = '<span class="'.$ajax_loader_span_class.'"><img src="'.$ajax_loader_img_url.'" /></span>';
        
        $form_str  = '<div class="bsk-pdfm-category-password-form cat-'.$cat_obj->id.'" data-cat-id="'.$cat_obj->id.'">';
        $form_str .= '<p class="bsk-pdf-category-password-desc">The content of this category requires password:</p>
                          <p>
                                <input type="text" class="bsk-pdfm-category-password" value="" placeholder="enter password..." />
                                <a href="javascript:void(0);" class="bsk-pdfm-category-password-verify-anchor">Verify</a>'.
                                $ajax_loader_str.'
                          </p>';
        if( $show_password_error ){
            $form_str .= '<p class="bsk-pdfm-category-password-error">Invalid category password</p>';
        }
        $form_str .= '</div>';
        
        return $form_str;
    }
    
    public static  function get_year_dropdown( $year_range, $year_order, $year_option_none_text ) {
        $year_from = date( 'Y', current_time('timestamp') );
        $year_to = date( 'Y', current_time('timestamp') );
        if( $year_range ){
            $year_range_array = self::validate_year( $year_range );
            if( $year_range_array && is_array( $year_range_array ) && count( $year_range_array ) == 2 ){
                $year_from = $year_range_array['from'];
                $year_to = $year_range_array['to'];
            }
        }
        
        $selector_str = '<select class="bsk-pdfm-year-dropdown">';
        $selector_str .= '<option value="">'.$year_option_none_text.'</option>';
        if( strtoupper($year_order) == 'DESC' ){
            for( $i_year = $year_to; $i_year >= $year_from; $i_year-- ){
                $selector_str .= '<option value="'.$i_year.'">'.$i_year.'</option>';
            }
        }else{
            for( $i_year = $year_from; $i_year <= $year_to; $i_year++ ){
                $selector_str .= '<option value="'.$i_year.'">'.$i_year.'</option>';
            }
        }
        
        $selector_str .= '</select>';
        
        return $selector_str;
    }
    
    static function get_plugin_credit_text(){
        $credit_str = '';
        
        $plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
        if( isset( $plugin_settings['enable_credit'] ) && $plugin_settings['enable_credit'] == 'Yes' ){
            $credit_text = 'PDFs powered by PDF Manager Pro';
            if( $plugin_settings['credit_text'] ){
                $credit_text = $plugin_settings['credit_text'];
            }
            $pdf_manager_pro_link = 'https://www.bannersky.com/bsk-pdf-manager/';
            $credit_str .= '<p class="bsk-pdfm-credit-link-container"><a href="'.$pdf_manager_pro_link.'" target="_blank">'.$credit_text.'</a></p>';
        }
        
        return $credit_str;
    }
    
}//end of class
