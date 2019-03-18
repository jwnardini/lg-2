<?php

class BSKPDFM_Shortcodes_PDFs_Deprecated {
    
	public function __construct() {
		global $wpdb;
		
		add_shortcode('bsk-pdf-manager-pdf', array($this, 'bsk_pdf_manager_show_pdf') );
	}
	
	function bsk_pdf_manager_show_pdf($atts, $content){
		global $wpdb;
		
		extract( shortcode_atts( array('id' => '', 
									   'linkonly' => '',
									   'linktext' => '',
									   'orderby' => '', 
									   'order' => '', 
									   'target' => '',
									   'dropdown' => '',
									   'showoptionnone' => '',
									   'showall' => '',
									   'mosttop' => 0,
									   'nofollowtag' => false,
									   'showdate' => false,
									   'dateformat' => ' d/m/Y',
									   'datebeforetitle' => false,
                                       'nolist' => 'no',
									   'orderedlist' => '', ), 
								 $atts) );
		//organise ids array
		$ids_array = array();
		if( trim($id) == "" && trim($showall) != 'yes' && trim($showall) != 'true' ){
			return '';
		}
		if( $id && is_string($id) ){
			$ids_array = explode(',', $id);
			foreach($ids_array as $key => $pdf_id){
				$pdf_id = intval(trim($pdf_id));
				if( is_int($pdf_id) == false ){
					unset($ids_array[$key]);
				}
				$ids_array[$key] = $pdf_id;
			}
		}
		if( ( !is_array($ids_array) || count($ids_array) < 1 ) && trim($showall) != 'yes' && trim($showall) != 'true' ){
			return '';
		}

		//process open target
		$open_target_str = '';
		if( $target == '_blank' ){
			$open_target_str = ' target="_blank"';
		}
		//process order
		$order_by_str = ' ORDER BY `title`'; //default set to title
		$order_str = ' ASC';
		if( $orderby == 'title' ){
			//default
		}else if( $orderby == 'filename' ){
			$order_by_str = ' ORDER BY `file_name`';
		}else if( $orderby == 'date' ){
			$order_by_str = ' ORDER BY `last_date`';
		}else if( $orderby == 'custom' ){
			$order_by_str = ' ORDER BY `order_num`';
		}
		if( trim($order) == 'DESC' ){
			$order_str = ' DESC';
		}
		//link only
		$show_link_only = false;
		if( $linkonly && is_string($linkonly) ){
			$show_link_only = strtoupper($linkonly) == 'YES' ? true : false;
			if( $show_link_only == false ){
				$show_link_only = strtoupper($linkonly) == 'TRUE' ? true : false;
			}
		}else if( is_bool($linkonly) ){
			$show_link_only = $linkonly;
		}
		//link text
		$custom_link_text = '';
		if( $linktext && is_string($linktext) ){
			$custom_link_text = $linktext;
		}
		//dropdown
		$output_as_dropdown = false;
		if( $dropdown && is_string($dropdown) ){
			$output_as_dropdown = strtoupper($dropdown) == 'YES' ? true : false;
			if( $output_as_dropdown == false ){
				$output_as_dropdown = strtoupper($dropdown) == 'TRUE' ? true : false;
			}
		}else if( is_bool($dropdown) ){
			$output_as_dropdown = $dropdown;
		}
		//showoptionnone
		$selected_option_text = '';
		if( $output_as_dropdown ){
			$selected_option_text = trim( $showoptionnone );
		}
		//show all
		$show_all_pdfs = false;
		if( $showall && is_string($showall) ){
			$show_all_pdfs = strtoupper($showall) == 'YES' ? true : false;
			if( $show_all_pdfs == false ){
				$show_all_pdfs = strtoupper($showall) == 'TRUE' ? true : false;
			}
		}else if( is_bool($showall) ){
			$show_all_pdfs = $showall;
		}
		//most top
		$most_recent_count = intval($mosttop);
		if( $most_recent_count < 1 ){
			$most_recent_count = 99999;
		}

		//anchor nofollow tag
		$nofollow_tag = false;
		if( $nofollowtag && is_string($nofollowtag) ){
			$nofollow_tag = strtoupper($nofollowtag) == 'YES' ? true : false;
			if( strtoupper($nofollowtag) == 'TRUE' ){
				$nofollow_tag = true;
			}
		}else if( is_bool($nofollowtag) ){
			$nofollow_tag = $nofollowtag;
		}
		//show date in title
		$show_date_in_title = false;
		if( $showdate && is_string($showdate) ){
			$show_date_in_title = strtoupper($showdate) == 'YES' ? true : false;
			if( strtoupper($showdate) == 'TRUE' ){
				$show_date_in_title = true;
			}
		}else if( is_bool($showdate) ){
			$show_date_in_title = $showdate;
		}
		
		//date postion
		$date_before_title = false;
		if( $datebeforetitle && is_string($datebeforetitle) ){
			$date_before_title = strtoupper($datebeforetitle) == 'YES' ? true : false;
			if( strtoupper($datebeforetitle) == 'TRUE' ){
				$date_before_title = true;
			}
		}else if( is_bool($datebeforetitle) ){
			$date_before_title = $datebeforetitle;
		}
		
		//date format
		$date_format_str = $date_before_title ? 'd/m/Y ' : ' d/m/Y';
		if( $dateformat && is_string($dateformat) && $dateformat != ' d/m/Y' ){
			$date_format_str = $dateformat;
		}
		if( $output_as_dropdown == true ){
			$date_format_str = $date_before_title ? 'd/m/Y' : 'd/m/Y';
			if( $dateformat && is_string($dateformat) && $dateformat != ' d/m/Y' ){
				$date_format_str = $dateformat;
			}
		}

		//show as ordered list
		$show_as_ordered_list = false;
		if( $orderedlist && is_string($orderedlist) ){
			$show_as_ordered_list = strtoupper($orderedlist) == 'YES' ? true : false;
			if( strtoupper($orderedlist) == 'TRUE' ){
				$show_as_ordered_list = true;
			}
		}else if( is_bool($orderedlist) ){
			$show_as_ordered_list = $orderedlist;
		}
        //no list
        $show_PDF_without_list = false;
        if( $nolist && is_string($nolist) ){
            $show_PDF_without_list = strtoupper($nolist) == 'YES' ? true : false;
			if( strtoupper($nolist) == 'TRUE' ){
				$show_PDF_without_list = true;
			}
        }else if( is_bool($nolist) ){
			$show_PDF_without_list = $nolist;
		}
		//multi-column
		if( $enable_multi_column_layout ){
			$columns_number = intval($columns);
			if( $columns_number > 1 ){
				$default_column_number = $columns_number;
			}
			if( $multicolumns && is_string($multicolumns) ){
				$enable_multi_column_layout = strtoupper($multicolumns) == 'YES' ? true : false;
				if( strtoupper($multicolumns) == 'TRUE' ){
					$enable_multi_column_layout = true;
				}
			}else if( is_bool($multicolumns) ){
				$enable_multi_column_layout = $multicolumns;
			}
		}

        $str_body = '';
		$sql = '';
		if( $show_all_pdfs ){
			$sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` '.
				   'WHERE 1 '.
				   $order_by_str.$order_str;
			//show all will overwrit ethe id parameter
			$ids_array = array();
		}else{
			$sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` '.
				   'WHERE `id` IN('.implode(',', $ids_array).') '.
				   $order_by_str.$order_str;
		}
        $pdf_items_results = $wpdb->get_results( $sql );
		if( !$pdf_items_results || !is_array($pdf_items_results) || count($pdf_items_results) < 1 ){
			return '';
		}
		
		//remove unpublished and expired ones, get year-moth filter here
		foreach( $pdf_items_results as $key => $pdf_item_obj ){
			if( $pdf_item_obj->publish_date && $pdf_item_obj->publish_date != '0000-00-00 00:00:00' ){
				if( date('Y-m-d 23:59:59', current_time( 'timestamp' ) ) < $pdf_item_obj->publish_date  ){
					unset( $pdf_items_results[$key] );
					continue;
				}
			}

			if( $pdf_item_obj->expiry_date && $pdf_item_obj->expiry_date != '0000-00-00 00:00:00' ){
				if( $pdf_item_obj->expiry_date <= date('Y-m-d 00:00:00', current_time( 'timestamp' ) ) ){
					unset( $pdf_items_results[$key] );
					continue;
				}
			}
		}
		
		//process most top
		if( $most_recent_count != 99999 ){
			$pdf_items_results = array_slice( $pdf_items_results, 0, $most_recent_count );
		}
        
        $rand_id = rand(1, 100000);
		$str_body .= '<div id="bsk_pdfm_list_pdfs_container_'.$rand_id.'" class="bsk-pdfm-list-pdfs-container">';
        
		if( $show_link_only == false ){
			if( $output_as_dropdown == true ){
				$str_body .= '<select name="bsk_pdf_manager_special_pdfs_select" class="bsk-pdf-manager-pdfs-select" attr_target="'.$target.'">';
				if( $selected_option_text ){
					$str_body .= '<option value="" selected="selected">'.$selected_option_text.'</option>';
				}
			}else{
                if( $show_PDF_without_list ){
                    $str_body .= '';
                }else if( $show_as_ordered_list ){
					$str_body .= '<ol class="bsk-special-pdfs-container-ordered-list">'."\n";
				}else{
					$str_body .= '<ul class="bsk-special-pdfs-container">'."\n";
				}
			}
		}

		$nofollow_tag_str = $nofollow_tag ? ' rel="nofollow"' : '';
		if( $orderby == "" && is_array($ids_array) && count($ids_array) > 0 ){
			//order by id sequence
			$pdf_items_results_id_as_key = array();
			foreach( $pdf_items_results as $pdf_object ){
				$pdf_items_results_id_as_key[$pdf_object->id] = $pdf_object;
			}

			$item_count = 0;
			foreach( $ids_array as $pdf_id ){
				if( !isset($pdf_items_results_id_as_key[$pdf_id]) ){
					continue;
				}
				
				$pdf_item = $pdf_items_results_id_as_key[$pdf_id];
				
				if( $pdf_item->file_name == "" &&  $pdf_item->by_media_uploader < 1 ){
					continue;
				}
				$file_url = '';
                if( file_exists(ABSPATH.$pdf_item->file_name) ){
					$file_url = site_url().$pdf_item->file_name;
				}
				if( $file_url == "" ){
					continue;
				}
				if( $show_link_only ){
					$str_body .= $file_url;
				}else{
					if( $output_as_dropdown == true ){
						$pdf_item_title = $pdf_item->title;
						if( $pdf_item_title == "" ){
							$pdf_item_title_array = explode( '/', $file_url );
							$pdf_item_title = $pdf_item_title_array[count($pdf_item_title_array) - 1];
						}
						$option_text = $custom_link_text ? $custom_link_text : $pdf_item_title;
						if( $show_date_in_title ){
							if( $date_before_title ){
								$option_text = date($date_format_str, strtotime($pdf_item->last_date)).'--'.$option_text;
							}else{
								$option_text .= '--'.date($date_format_str, strtotime($pdf_item->last_date));
							}
						}
						$str_body .= '<option value="'.$file_url.'">'.$option_text.'</option>'."\n";
					}else{
						$str_body .= $this->bsk_pdf_manager_show_pdf_item( $pdf_item, $featured_image, $featured_image_size,  $show_PDF_title_with_featured_image, $default_thumbnail_html,
								        $open_target_str, $nofollow_tag_str, $show_date_in_title, $date_format_str, $date_before_title, $custom_link_text, $show_PDF_without_list );
					}
				}
				$item_count++;
			}
		}else{
			$item_count = 0;
			foreach($pdf_items_results as $pdf_item){
				
				if( $pdf_item->file_name == "" &&  $pdf_item->by_media_uploader < 1 ){
					continue;
				}
				$file_url = '';
				if( file_exists(ABSPATH.$pdf_item->file_name) ){
					$file_url = site_url().'/'.$pdf_item->file_name;
				}
				if( $file_url == "" ){
					continue;
				}
				if( $show_link_only ){
					$str_body .= $file_url;
				}else{
					if( $output_as_dropdown == true ){
						$pdf_item_title = $pdf_item->title;
						if( $pdf_item_title == "" ){
							$pdf_item_title_array = explode( '/', $file_url );
							$pdf_item_title = $pdf_item_title_array[count($pdf_item_title_array) - 1];
						}
						$option_text = $custom_link_text ? $custom_link_text : $pdf_item_title;
						if( $show_date_in_title ){
							if( $date_before_title ){
								$option_text = date($date_format_str, strtotime($pdf_item->last_date)).'--'.$option_text;
							}else{
								$option_text .= '--'.date($date_format_str, strtotime($pdf_item->last_date));
							}
						}
						$str_body .= '<option value="'.$file_url.'">'.$option_text.'</option>'."\n";
					}else{
						$str_body .= $this->bsk_pdf_manager_show_pdf_item( $pdf_item, $featured_image, $featured_image_size, $show_PDF_title_with_featured_image, $default_thumbnail_html,
																		   $open_target_str, $nofollow_tag_str, $show_date_in_title, $date_format_str, $date_before_title, $custom_link_text, $show_PDF_without_list );
					}
				}
				$item_count++;
			}
		}
		
		if( $show_link_only == false ){
			if( $output_as_dropdown == true ){
				$str_body .= '</select>';
			}else{
                if( $show_PDF_without_list ){
                    $str_body .= '';
                }else if( $show_as_ordered_list ){
					$str_body .= '</ol>';
				}else{
					$str_body .= '</ul>';
				}
			}
		}
        $str_body .= '<input type="hidden" id="bsk_pdfm_pdfs_columns_'.$rand_id.'" value="'.$default_column_number.'" />';
        $str_body .= '</div>';
        
		return $str_body;
	}
	
	function bsk_pdf_manager_show_pdf_item( $pdf_item, $featured_image, $featured_image_size, $show_PDF_title_with_featured_image, $default_thumbnail_html,
											$open_target_str, $nofollow_tag_str, $show_date_in_title, $date_format_str, $date_before_title, $custom_link_text, $show_pdf_without_list = false,
											$enable_multi_column = false, $column_class = '', $column_layout_title_positon = 'below'
                                             ){
        $file_url = site_url().'/'.$pdf_item->file_name;
		if( $pdf_item->by_media_uploader ){
            $file_url = wp_get_attachment_url( $pdf_item->by_media_uploader );
        }
		
		$pdf_item_title = $pdf_item->title;
		if( $pdf_item_title == "" ){
			$pdf_item_title_array = explode( '/', $file_url );
			$pdf_item_title = $pdf_item_title_array[count($pdf_item_title_array) - 1];
		}
		
		$str_body = '';
		if( $show_pdf_without_list ){
            $str_body .= '';
		}else{
			$str_body .= '<li>';
		}
		
        $link_text = $custom_link_text ? $custom_link_text : $pdf_item_title;
        if( $show_date_in_title ){
            $date_str = '<span class="bsk-pdf-manager-pdf-date">'.date($date_format_str, strtotime($pdf_item->last_date)).'</span>';
            if( $date_before_title ){
                $link_text = $date_str.$link_text;
            }else{
                $link_text = $link_text.$date_str;
            }
        }
        $str_body .= '<a href="'.$file_url.'" '.$open_target_str.$nofollow_tag_str.' title="'.$pdf_item->title.'">'.$link_text.'</a>'."\n";
		
		if( $show_pdf_without_list ){
            $str_body .= '';
		}else{
			$str_body .= '</li>'."\n";
		}
			
		return $str_body;
	}
}