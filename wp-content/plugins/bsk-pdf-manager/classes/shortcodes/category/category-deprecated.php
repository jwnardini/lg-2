<?php

class BSKPDFM_Shortcodes_Category_Deprecated {

	public function __construct() {
		global $wpdb;
		
		add_shortcode('bsk-pdf-manager-list-category', array($this, 'bsk_pdf_manager_list_pdfs_by_cat') );
		
		add_action( 'wp_ajax_verify_category_password', array( $this, 'bsk_pdfm_verify_category_password_fun' ) );
		add_action( 'wp_ajax_nopriv_verify_category_password', array( $this, 'bsk_pdfm_verify_category_password_fun' ) );
	}
	
	function bsk_pdf_manager_list_pdfs_by_cat($atts, $content){
		global $wpdb;
		
		//read plugin settings
		$default_enable_featured_image = true;
		$default_thumbnail_html = '';
		$default_thumbnail_size = 'thumbnail';
		$enable_multi_column_layout = false;
		$default_column_number = 2;
		$default_column_layout_title_positon = 'below';
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
			if( isset($plugin_settings['enable_featured_image']) ){
				$default_enable_featured_image = $plugin_settings['enable_featured_image'];
			}
			
			if( isset($plugin_settings['default_thumbnail_size']) ){
				$default_thumbnail_size = $plugin_settings['default_thumbnail_size'];
			}
			
			if( isset($plugin_settings['multi_column_layout']) ){
				$enable_multi_column_layout = $plugin_settings['multi_column_layout'];
			}
			
			if( isset($plugin_settings['default_column_number']) ){
				$default_column_number = $plugin_settings['default_column_number'];
			}
			
			if( isset($plugin_settings['column_layout_title_positon']) ){
				$default_column_layout_title_positon = $plugin_settings['column_layout_title_positon'];
			}
		}
		
		extract( shortcode_atts( array('id' => '',
									   'linktext' => '',
									   'orderby' => '', 
									   'order' => '', 
                                       'additional_filter' => 'false',
									   'target' => '', 
									   'showcattitle' => '',
                                       'show_cat_description' => 'no',
									   'dropdown' => '',
									   'showoptionnone' => '',
									   'mosttop' => 0,
									   'featuredimage' => '',
									   'featuredimagesize' => '',
									   'showpdftitle' => '',
									   'nofollowtag' => 'false',
									   'showdate' => 'false',
									   'dateformat' => ' d/m/Y',
									   'datebeforetitle' => false,
									   'orderedlist' => '',
									   'columns' => 1,
									   'multicolumns' => 'no',
									   'password_required' => 'no',
                                       'hide_empty' => 'no',
                                       'include_sub_category' => 'no',
									   'show_empty_message' => 'no' ),
								  $atts ) );
		
		//show category title or not
		$show_cat_title = false;
		if( $showcattitle && is_string($showcattitle) ){
			$show_cat_title = strtoupper($showcattitle) == "YES" ? true : false;
			if( $show_cat_title == false ){
				$show_cat_title = strtoupper($showcattitle) == 'TRUE' ? true : false;
			}
		}else if( is_bool($showcattitle) ){
			$show_cat_title = $showcattitle;
		}
		
		//organise id array
		$ids_array = array();
		$ids_string = trim($id);
		if( !$ids_string ){
			return '';
		}

		if( strtoupper($ids_string) == 'ALL' ){
			$sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` WHERE 1 ORDER BY `title` ASC';
			$categories = $wpdb->get_results($sql);
			if( !$categories || !is_array($categories) || count($categories) < 1 ){
				return '';
			}
		}else{
			if( $ids_string && is_string($ids_string) ){
				$ids_array = explode(',', $ids_string);
				foreach($ids_array as $key => $pdf_id){
					$pdf_id = intval(trim($pdf_id));
					if( is_int($pdf_id) == false ){
						unset($ids_array[$key]);
					}
					$ids_array[$key] = $pdf_id;
				}
			}
			if( !is_array($ids_array) || count($ids_array) < 1 ){
				return '';
			}
			$sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` '.
                     'WHERE id IN('.implode(',', $ids_array).') ORDER BY `title` ASC';
			$categories = $wpdb->get_results($sql);
			if( !$categories || !is_array($categories) || count($categories) < 1 ){
				return '';
			}
		}
		
		//organise category by id
		$categories_id_as_key = array();
		foreach( $categories as $category_obj ){
			$categories_id_as_key[$category_obj->id] = $category_obj;
		}

		//process open target
		$open_target_str = '';
		if( $target == '_blank' ){
			$open_target_str = 'target="_blank"';
		}
		//process order
		$order_by_str = ' ORDER BY P.`title`'; //default set to title
		$order_str = ' ASC';
		if( $orderby == 'title' ){
			//default
		}else if( $orderby == 'filename' ){
			$order_by_str = ' ORDER BY P.`file_name`';
		}else if( $orderby == 'date' ){
			$order_by_str = ' ORDER BY P.`last_date`';
		}else if( $orderby == 'custom' ){
			$order_by_str = ' ORDER BY P.`order_num`';
		}
		if( trim($order) == 'DESC' ){
			$order_str = ' DESC';
		}

		//link text
		$custom_link_text = '';
		if( $linktext && is_string($linktext) ){
			$custom_link_text = $linktext;
		}
		//dropdown
		$output_as_dropdown = false;
		if( $dropdown && is_string($dropdown) ){
			$output_as_dropdown = strtoupper($dropdown) == 'TRUE' ? true : false;
			if( $output_as_dropdown == false ){
				$output_as_dropdown = strtoupper($dropdown) == 'YES' ? true : false;
			}
		}else if( is_bool($dropdown) ){
			$output_as_dropdown = $dropdown;
		}
		//showoptionnone
		$selected_option_text = '';
		if( $output_as_dropdown ){
			$selected_option_text = trim( $showoptionnone );
		}
		//most recent count
		$most_recent_count = intval($mosttop);
		if( $most_recent_count < 1 ){
			$most_recent_count = 99999;
		}
		//featured image
		$featured_image = false;
		$featured_image_size = '';
		if( $featuredimage && is_string($featuredimage) ){
			$featured_image = strtoupper($featuredimage) == 'TRUE' ? true : false;
			if( $featured_image == false ){
				$featured_image = strtoupper($featuredimage) == 'YES' ? true : false;
			}
		}else if( is_bool($featuredimage) ){
			$featured_image = $featuredimage;
		}
		
		//check if plugin setting -> enable featured image
		if( $default_enable_featured_image == false ){
			$featured_image = false;
		}
		
		//show pdf title with featured imaged
		$show_PDF_title_with_featured_image = false;
		$featured_image_size = '';
		if( $featured_image ){
			if( $showpdftitle && is_string($showpdftitle) ){
				$show_PDF_title_with_featured_image = strtoupper($showpdftitle) == 'TRUE' ? true : false;
				if( $show_PDF_title_with_featured_image == false ){
					$show_PDF_title_with_featured_image = strtoupper($showpdftitle) == 'YES' ? true : false;
				}
			}else if( is_bool($showpdftitle) ){
				$show_PDF_title_with_featured_image = $showpdftitle;
			}
			$featured_image_size = trim($featuredimagesize);
			if( $featured_image_size == "" ){
				$featured_image_size = $default_thumbnail_size;
			}
			if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
				if( isset($plugin_settings['default_thumbnail_id']) ){
					$default_thumbnail_id = $plugin_settings['default_thumbnail_id'];
					if( $default_thumbnail_id && get_post( $default_thumbnail_id ) ){
						$default_thumbnail_html = wp_get_attachment_image( $default_thumbnail_id, $featured_image_size );
					}
				}
			}
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
		
		if( $output_as_dropdown ){
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
		
		$column_class = 'bsk-pdfm-one-half';
		if( $enable_multi_column_layout ){
			switch( $default_column_number ){
				case 2:
					$column_class = 'bsk-pdfm-one-half';
				break;
				case 3:
					$column_class = 'bsk-pdfm-one-third';
				break;
				case 4:
					$column_class = 'bsk-pdfm-one-fourth';
				break;
				case 5:
					$column_class = 'bsk-pdfm-one-fifth';
				break;
				case 6:
					$column_class = 'bsk-pdfm-one-sixth';
				break;
			}
		}
		
		// Password protect
		$password_protect = false;
		if( $password_required && is_string($password_required) ){
			$password_protect = strtoupper($password_required) == 'YES' ? true : false;
			if( strtoupper($password_required) == 'TRUE' ){
				$password_protect = true;
			}
		}else if( is_bool($password_required) ){
			$password_protect = $password_required;
		}
        
        //hide empty message
        $skip_empty_category = false;
        if( $hide_empty && is_string($hide_empty) ){
			$skip_empty_category = strtoupper($hide_empty) == 'YES' ? true : false;
			if( strtoupper($hide_empty) == 'TRUE' ){
				$skip_empty_category = true;
			}
		}else if( is_bool($hide_empty) ){
			$skip_empty_category = $hide_empty;
		}
        
		//show empty message
		$show_empty_message_bool = false;
		if( $show_empty_message && is_string($show_empty_message) ){
			$show_empty_message_bool = strtoupper($show_empty_message) == 'YES' ? true : false;
			if( strtoupper($show_empty_message) == 'TRUE' ){
				$show_empty_message_bool = true;
			}
		}else if( is_bool($show_empty_message) ){
			$show_empty_message_bool = $show_empty_message;
		}
        
        //show category description
        $show_cat_description_bool = false;
        if( $show_cat_description && is_string($show_cat_description) ){
			$show_cat_description_bool = strtoupper($show_cat_description) == 'YES' ? true : false;
			if( strtoupper($show_cat_description) == 'TRUE' ){
				$show_cat_description_bool = true;
			}
		}else if( is_bool($show_cat_description) ){
			$show_cat_description_bool = $show_cat_description;
		}
        
        //include sub category
        $include_sub_category_bool = false;
        if( $include_sub_category && is_string($include_sub_category) ){
			$include_sub_category_bool = strtoupper($include_sub_category) == 'YES' ? true : false;
			if( strtoupper($include_sub_category) == 'TRUE' ){
				$include_sub_category_bool = true;
			}
		}else if( is_bool($include_sub_category) ){
			$include_sub_category_bool = $include_sub_category;
		}
		
		$home_url = site_url();
		$forStr = '';
		$categories_loop_array = array();
		if( strtoupper($ids_string) == 'ALL' ){
			foreach( $categories_id_as_key as $cat_obj ){
				$categories_loop_array[] = $cat_obj->id;
			}
		}else{
			$categories_loop_array = $ids_array;
		}
        
		$random_val = rand( 1111, 9999 );
		foreach( $categories_loop_array as $category_id ){ //order category by id sequence
			if( !isset($categories_id_as_key[$category_id]) ){
				continue;
			}
			$forStr .=	'<div class="bsk-pdf-category cat-'.$category_id.'" id="bsk_pdf_category_content_container_ID_'.$category_id.'">'."\n";
			
            //get pdf items in the category
			$sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` AS P '.
                     'LEFT JOIN `'.$wpdb->prefix.BSKPDFManager::$_rels_tbl_name.'` AS R ON P.`id` = R.`pdf_id` '.
				     'WHERE R.`cat_id` = '.$category_id.
				     $order_by_str.$order_str;
			$pdf_items_results = $wpdb->get_results( $sql );
            if( $skip_empty_category && ( !$pdf_items_results || !is_array($pdf_items_results) || count($pdf_items_results) < 1 ) ){
                continue;
            }
            
			if( $show_cat_title ){
				$forStr .=	'<h2>'.$categories_id_as_key[$category_id]->title.'</h2>'."\n";
			}
			
            if( $show_cat_description_bool ){
                $forStr .=	'<div class="bsk-pdf-category-description cat-description-for-id-'.$category_id.'">'.$categories_id_as_key[$category_id]->description.'</div>'."\n";
            }
            
			/*
              * come to here it means hide_empty is not applied if category is empty
            */
			if( !$pdf_items_results || !is_array($pdf_items_results) || count($pdf_items_results) < 1 ){
				if( $show_empty_message_bool && $categories_id_as_key[$category_id]->empty_message ){
					$forStr .= '<p><span class="bsk-pdf-category-empty-message">'.$categories_id_as_key[$category_id]->empty_message.'</span></p>';
				}
				$forStr .=  '</div>'."\n";
				continue;
			}
            
            //check if passowrd protected
			if( $password_protect && $categories_id_as_key[$category_id]->password != "" ){
				if( !isset($_SESSION['bsk_pdf_category_password']) || 
					!isset($_SESSION['bsk_pdf_category_password']['cat-'.$category_id]) || 
					$_SESSION['bsk_pdf_category_password']['cat-'.$category_id] != $categories_id_as_key[$category_id]->password ){
					
					$forStr .= '<p><span class="bsk-pdf-category-password-desc">The content of this category requires password: </span>';
					$forStr .= '<input type="text" id="bsk_pdf_category_password_of_cat_'.$category_id.'" class="bsk-pdf-category-password" />';
					$forStr .= '&nbsp;&nbsp;<a href="javascript:void(0);" id="bsk_pdf_category_category_password_verify_anchor_of_cat_'.$category_id.'" class="bsk-pdf-category-password-verify" rel="'.$category_id.'" randval="'.$random_val.'">Verify Password</a>';
					$forStr .= '&nbsp;&nbsp;<span id="bks_pdf_category_category_password_verify_ajax_loader_of_cat_'.$category_id.'" style="display:none;"><img src="'.BSKPDFManager::$_ajax_loader_img_url.'" /></span>';
					$forStr .= '&nbsp;&nbsp;<span id="bks_pdf_category_category_password_verify_error_message_cat_'.$category_id.'" style="color:#ff0000;display:none;">Invalid password</span></p>';
					
					$forStr .=  '</div>'."\n";
					continue;
				}
			}
			
			//remove unpublished and expired ones
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
			
			if( count($pdf_items_results) < 1 ){
				if( $show_empty_message_bool && $categories_id_as_key[$category_id]->empty_message ){
					$forStr .= '<p><span class="bsk-pdf-category-empty-message">'.$categories_id_as_key[$category_id]->empty_message.'</span></p>';
				}
				$forStr .=  '</div>'."\n";
				continue;
			}
			
			if( $output_as_dropdown == true ){
				$forStr .= '<select name="bsk_pdf_manager_special_pdfs_select" class="bsk-pdf-manager-pdfs-select cat-'.$category_id.'" attr_target="'.$target.'">';
				if( $selected_option_text ){
					$forStr .= '<option value="" selected="selected">'.$selected_option_text.'</option>';
				}
			}else if( $enable_multi_column_layout == true ){
				$forStr .= '<div class="bsk-specific-pdfs-multi-column-container">'."\n";
			}else if( $show_as_ordered_list ){
                $forStr .= '<ol class="bsk-special-pdfs-container-ordered-list">'."\n";
            }else{
                $forStr .= '<ul class="bsk-special-pdfs-container">'."\n";
			}
			$nofollow_tag_str = $nofollow_tag ? ' rel="nofollow"' : '';
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
				
				if( $output_as_dropdown == true ){
					$pdf_item_obj_title = $pdf_item_obj->title;
					if( $pdf_item_obj_title == "" ){
						$pdf_item_obj_title_array = explode( '/', $file_url );
						$pdf_item_obj_title = $pdf_item_obj_title_array[count($pdf_item_obj_title_array) - 1];
					}
					$option_text = $custom_link_text ? $custom_link_text : $pdf_item_obj_title;
					if( $show_date_in_title ){
						if( $date_before_title ){
							$option_text = date($date_format_str, strtotime($pdf_item_obj->last_date)).'--'.$option_text;
						}else{
							$option_text .= '--'.date($date_format_str, strtotime($pdf_item_obj->last_date));
						}
					}
					$forStr .= '<option value="'.$file_url.'" id="'.$pdf_item_obj->id.'">'.$option_text.'</option>'."\n";
				}else if( $enable_multi_column_layout == true ){
					$column_class_item = ( $item_count % $default_column_number ) == 0 ? $column_class.' bsk-pdfm-first' : $column_class;
					$forStr .= $this->bsk_pdf_manager_show_pdf_item_single( $pdf_item_obj, $featured_image, $featured_image_size, $show_PDF_title_with_featured_image, $default_thumbnail_html,
																				$open_target_str, $nofollow_tag_str, $show_date_in_title, $date_format_str, $date_before_title, $custom_link_text,
																				true, $column_class_item, $default_column_layout_title_positon );
				}else{
					$forStr .= $this->bsk_pdf_manager_show_pdf_item_single( $pdf_item_obj, $featured_image, $featured_image_size, $show_PDF_title_with_featured_image, $default_thumbnail_html,
																				$open_target_str, $nofollow_tag_str, $show_date_in_title, $date_format_str, $date_before_title, $custom_link_text );
				}
				$item_count++;
			}
			if( $output_as_dropdown == true ){
				$forStr .= '</select>';
			}else if( $enable_multi_column_layout == true ){
				$forStr .=  '<div class="bsk-pdf-clear-both"></div>';
				$forStr .= '</div>';
			}else if( $show_as_ordered_list ){
					$forStr .= '</ol>';
            }else{
					$forStr .= '</ul>';
			}
			$forStr .=  '</div>'."\n";
		}
		
		//add hidden parameters for verify password
		if( $password_protect ){
			$show_cat_title_str = $show_cat_title ? 'YES' : '';
			$show_empty_message_str = $show_empty_message_bool ? 'YES' : '';
			$as_dropdown_str = $output_as_dropdown ? 'YES' : '';
			$dropdown_selected_option_text = $selected_option_text;
			$show_as_ordered_list_hidden = $show_as_ordered_list ? 'YES' : '';
			$featured_image_hidden = $featured_image ? 'YES' : '';
			$show_PDF_title_with_featured_image_hidden = $show_PDF_title_with_featured_image ? 'YES' : '';
			$nofollow_tag_hidden = $nofollow_tag ? 'YES' : '';
			$show_date_in_title_hidden = $show_date_in_title ? 'YES' : '';
			$date_before_title_str = $date_before_title ? 'YES' : '';
			$multicolumns_str = $enable_multi_column_layout ? 'YES' : '';
			$custom_link_text_str = $custom_link_text;
			
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_show_cat_title_'.$random_val.'" value="'.$show_cat_title_str.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_show_empty_msg_'.$random_val.'" value="'.$show_empty_message_str.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_order_by_'.$random_val.'" value="'.$order_by_str.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_order_'.$random_val.'" value="'.$order_str.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_output_as_dropdown_'.$random_val.'" value="'.$as_dropdown_str.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_dropdown_selected_option_'.$random_val.'" value="'.$dropdown_selected_option_text.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_category_top_'.$random_val.'" value="'.$most_recent_count.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_show_ordered_list_'.$random_val.'" value="'.$show_as_ordered_list_hidden.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_show_thumbnail_'.$random_val.'" value="'.$featured_image_hidden.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_thumbnail_size_'.$random_val.'" value="'.$featured_image_size.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_thumbnail_with_title_'.$random_val.'" value="'.$show_PDF_title_with_featured_image_hidden.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_open_target_'.$random_val.'" value="'.$target.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_nofollow_tag_'.$random_val.'" value="'.$nofollow_tag_hidden.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_show_date_in_title_'.$random_val.'" value="'.$show_date_in_title_hidden.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_date_format_'.$random_val.'" value="'.$date_format_str.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_date_before_title_'.$random_val.'" value="'.$date_before_title_str.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_columns_'.$random_val.'" value="'.$columns.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_multicolumn_'.$random_val.'" value="'.$multicolumns_str.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_multicolumn_title_pos_'.$random_val.'" value="'.$default_column_layout_title_positon.'" />';
			$forStr .= '<input type="hidden" id="bsk_pdfm_cat_password_protect_custom_link_text_'.$random_val.'" value="'.$custom_link_text_str.'" />';
		}
		$forStr .= '<input type="hidden" id="bsk_pdfm_category_columns_'.$random_val.'" value="'.$columns.'" />';
        
		return $forStr;
	}
	

	function get_all_pdfs_within_category( $category_id, $order_by, $order, $dropdown, $dropdown_selected, $most_top, $ordered_list, 
										   $show_thumbnail, $thumbnail_size, $show_thumbnail_with_title, $open_target, $nofollow_tag, 
										   $show_date_in_title, $date_format_str, $date_before_title, $columns = 2, $multicolumns = false, 
										   $title_postion_4_mulitcolumn = 'below',
										   $custom_link_text = '' ){
		if( $category_id == 0 ){
			return '';
		}
		
		global $wpdb;
		
		//read plugin settings
		$default_enable_featured_image = true;
		$default_thumbnail_html = '';
		$featured_image_size = $thumbnail_size ? $thumbnail_size : 'thumbnail';
		$enable_multi_column_layout = false;
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
			if( isset($plugin_settings['enable_featured_image']) ){
				$default_enable_featured_image = $plugin_settings['enable_featured_image'];
			}
			
			if( isset($plugin_settings['default_thumbnail_size']) ){
				$featured_image_size = $plugin_settings['default_thumbnail_size'];
			}
			if( isset($plugin_settings['default_thumbnail_id']) ){
				$default_thumbnail_id = $plugin_settings['default_thumbnail_id'];
				if( $default_thumbnail_id && get_post( $default_thumbnail_id ) ){
					$default_thumbnail_html = wp_get_attachment_image( $default_thumbnail_id, $thumbnail_size );
				}
			}
			
			if( isset($plugin_settings['multi_column_layout']) ){
				$enable_multi_column_layout = $plugin_settings['multi_column_layout'];
			}
		}

		//check if plugin setting -> enable featured image
		if( $default_enable_featured_image == false ){
			$show_thumbnail = false;
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
		
		$column_class = 'bsk-pdfm-one-half';
		if( $enable_multi_column_layout ){
			switch( $default_column_number ){
				case 2:
					$column_class = 'bsk-pdfm-one-half';
				break;
				case 3:
					$column_class = 'bsk-pdfm-one-third';
				break;
				case 4:
					$column_class = 'bsk-pdfm-one-fourth';
				break;
				case 5:
					$column_class = 'bsk-pdfm-one-fifth';
				break;
				case 6:
					$column_class = 'bsk-pdfm-one-sixth';
				break;
			}
		}
		
		$pdf_items_results = array();
		if( $category_id == -1 ){
			$sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` '.
				     'WHERE 1 '.
				     $order_by.$order.' ';
			$pdf_items_results = $wpdb->get_results( $sql );
			if( !$pdf_items_results || !is_array($pdf_items_results) || count($pdf_items_results) < 1 ){
				return '';
			}
		}else{
			//get pdf items in the category
			$sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` AS P '.
                     'LEFT JOIN `'.$wpdb->prefix.BSKPDFManager::$_rels_tbl_name.'` AS R ON P.`id` = R.`pdf_id` '.
				     'WHERE R.`cat_id` = %d'.
				     $order_by.$order;
			$sql = $wpdb->prepare( $sql, $category_id );

            $pdf_items_results = $wpdb->get_results( $sql );
			if( !$pdf_items_results || !is_array($pdf_items_results) || count($pdf_items_results) < 1 ){
				return '';
			}
		}
		
		//remove unpublished and expired ones
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
		if( $most_top > 0 ){
			$pdf_items_results = array_slice( $pdf_items_results, 0, $most_top );
		}
		
		$forStr = '';
		$output_as_dropdown = $dropdown;
		if( $output_as_dropdown == true ){
			$forStr .= '<select name="bsk_pdfm_selector_pdfs_dropdown" class="bsk-pdfm-selector-pdfs-dropdown cat-'.$category_id.'" attr_target="'.$open_target.'">';
			$dropdown_selected = $dropdown_selected ? $dropdown_selected : 'Please selecte category...';
            if( $dropdown_selected ){
				$forStr .= '<option value="">'.$dropdown_selected.'</option>';
			}
		}else if( $enable_multi_column_layout == true ){
			$forStr .= '<div class="bsk-specific-pdfs-multi-column-container">'."\n";
		}else if( $ordered_list ){
            $forStr .= '<ol class="bsk-pdfm-selector-pdfs-ordered-list cat-'.$category_id.'">'."\n";
        }else{
            $forStr .= '<ul class="bsk-pdfm-selector-pdfs-list cat-'.$category_id.'">'."\n";
		}
		$nofollow_tag_str = $nofollow_tag ? ' rel="nofollow"' : '';
		$open_target_str = $open_target == '_blank' ? ' target="_blank"' : '';
		$item_count = 0;
		foreach($pdf_items_results as $pdf_item_obj ){
			if( $pdf_item_obj->file_name == "" &&  $pdf_item_obj->by_media_uploader < 1 ){
				continue;
			}
			$file_url = '';
			if( $pdf_item_obj->by_media_uploader ){
				$file_url = wp_get_attachment_url( $pdf_item_obj->by_media_uploader );
			}else{
				$file_url = site_url().'/'.$pdf_item_obj->file_name;
			}
			if( $file_url == "" ){
				continue;
			}
				
			if( $output_as_dropdown == true ){
				$pdf_item_obj_title = $pdf_item_obj->title;
				if( $pdf_item_obj_title == "" ){
					$pdf_item_obj_title_array = explode( '/', $file_url );
					$pdf_item_obj_title = $pdf_item_obj_title_array[count($pdf_item_obj_title_array) - 1];
				}
				$option_text = $custom_link_text ? $custom_link_text : $pdf_item_obj_title;
				if( $show_date_in_title ){
					if( $date_before_title ){
						$option_text = date($date_format_str, strtotime($pdf_item_obj->last_date)).'--'.$option_text;
					}else{
						$option_text .= '--'.date($date_format_str, strtotime($pdf_item_obj->last_date));
					}
				}
				$forStr .= '<option value="'.$file_url.'">'.$option_text.'</option>'."\n";
			}else if( $enable_multi_column_layout == true ){
				$column_class_item = ( $item_count % $default_column_number ) == 0 ? $column_class.' bsk-pdfm-first' : $column_class;
				$forStr .= $this->bsk_pdf_manager_show_pdf_item_single( $pdf_item_obj, $show_thumbnail, $thumbnail_size, $show_thumbnail_with_title, $default_thumbnail_html,
																			$open_target_str, $nofollow_tag_str, $show_date_in_title, $date_format_str, $date_before_title, $custom_link_text,
																			true, $column_class_item, $title_postion_4_mulitcolumn );
			}else{
				$forStr .= $this->bsk_pdf_manager_show_pdf_item_single( $pdf_item_obj, $show_thumbnail, $thumbnail_size, $show_thumbnail_with_title, $default_thumbnail_html,
																			$open_target_str, $nofollow_tag_str, $show_date_in_title, $date_format_str, $date_before_title, $custom_link_text );
			}
			$item_count++;
		}
		if( $output_as_dropdown == true ){
			$forStr .= '</select>';
		}else if( $enable_multi_column_layout == true ){
			$forStr .= '<div class="bsk-pdf-clear-both"></div>';
			$forStr .= '</div>';
		}else if( $ordered_list ){
            $forStr .= '</ol>';
        }else{
            $forStr .= '</ul>';
		}
		
		return $forStr;
	}
	
	function bsk_pdf_manager_show_pdf_item_single( $pdf_item_obj, $featured_image, $featured_image_size, $show_PDF_title_with_featured_image, $default_thumbnail_html,
													   $open_target_str, $nofollow_tag_str, $show_date_in_title, $date_format_str, $date_before_title, $custom_link_text,
													   $enable_multi_column = false, $column_class = '', $column_layout_title_positon = 'below' ){
        $forStr = '';
		if( $enable_multi_column && $column_class ){
			$forStr .= '<div class="bsk-pdf-item '.$column_class.'">';
		}else{
			$forStr .= '<li>';
		}
		
		$file_url = site_url().'/'.$pdf_item_obj->file_name;
		if( $pdf_item_obj->by_media_uploader ){
			$file_url = wp_get_attachment_url( $pdf_item_obj->by_media_uploader );
		}
		
		$pdf_item_obj_title = $pdf_item_obj->title;
		if( $pdf_item_obj_title == "" ){
			$pdf_item_obj_title_array = explode( '/', $file_url );
			$pdf_item_obj_title = $pdf_item_obj_title_array[count($pdf_item_obj_title_array) - 1];
		}

		if( $featured_image == true ){
			//get PDF featured image
			$thumbnail_html = '';
			if( $pdf_item_obj->thumbnail_id  && get_post( $pdf_item_obj->thumbnail_id ) ){
				$thumbnail_html = wp_get_attachment_image( $pdf_item_obj->thumbnail_id, $featured_image_size );
			}else if( $default_thumbnail_html){
				$thumbnail_html = $default_thumbnail_html;
			}else{
				$thumbnail_html = '<img src="'.BSKPDFManager::$_default_pdf_icon_url.'" width="150" height="150" />';
			}
			$featured_image_str = '<a href="'.$file_url.'" '.$open_target_str.$nofollow_tag_str.' title="'.$pdf_item_obj->title.'">'.$thumbnail_html.'</a>';
			if( $show_PDF_title_with_featured_image ){
				$PDF_title = $pdf_item_obj_title;
				$anchor_title = $pdf_item_obj_title;
				if( $show_date_in_title ){
					if( $date_before_title ){
						$PDF_title = '<span class="bsk-pdf-manager-pdf-date">'.date($date_format_str, strtotime($pdf_item_obj->last_date)).'</span>'.$PDF_title;
					}else{
						$PDF_title = $PDF_title.'<span class="bsk-pdf-manager-pdf-date">'.date($date_format_str, strtotime($pdf_item_obj->last_date)).'</span>';
					}
				}
				$pdf_title_str  =  '<span class="bsk-pdf-manager-pdf-title">'.
								   '<a href="'.$file_url.'" '.$open_target_str.$nofollow_tag_str.' title="'.$anchor_title.'">'.$PDF_title.'</a>'.
								   '</span>';
				if( $column_layout_title_positon == 'below' ){
					$forStr .= $featured_image_str.$pdf_title_str;
				}else{
					$forStr .= $pdf_title_str.$featured_image_str;
				}
			}else{
				$forStr .= $featured_image_str;
			}
		}else{
			$link_text = $custom_link_text ? $custom_link_text : $pdf_item_obj_title;
			if( $show_date_in_title ){
				if( $date_before_title ){
					$link_text = '<span class="bsk-pdf-manager-pdf-date">'.date($date_format_str, strtotime($pdf_item_obj->last_date)).'</span>'.$link_text;
				}else{
					$link_text .= '<span class="bsk-pdf-manager-pdf-date">'.date($date_format_str, strtotime($pdf_item_obj->last_date)).'</span>';
				}
			}
			$forStr .= '<a href="'.$file_url.'" '.$open_target_str.$nofollow_tag_str.'  title="'.$pdf_item_obj->title.'">'.$link_text.'</a>'."\n";
		}
		
		if( $enable_multi_column && $column_class ){
			$forStr .= '</div>'."\n";
		}else{
			$forStr .= '</li>'."\n";
		}
		
		return $forStr;
	}
	
	function bsk_pdfm_verify_category_password_fun(){
		if( !isset($_POST['cat_id']) || $_POST['cat_id'] < 1 ){
			die( 'ERROR - Invalid category ID' );
		}
		
		if( !isset($_POST['password_val']) || $_POST['password_val'] == "" ){
			die( 'ERROR - Empty password' );
		}
		
		global $wpdb;
		
		$sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` WHERE `id` = %d AND `password` = %s';
		$sql = $wpdb->prepare( $sql, $_POST['cat_id'], $_POST['password_val'] );
		$results = $wpdb->get_results( $sql );
		if( !$results || !is_array($results) || count($results) < 1 ){
			die( 'ERROR - Invalid password' );
		}
		$_SESSION['bsk_pdf_category_password'] = array();
		$_SESSION['bsk_pdf_category_password']['cat-'.$_POST['cat_id']] = $_POST['password_val'];
		
		$cat_obj = $results[0];
		
		//get all PDFs
		$category_id = $_POST['cat_id'];
		$show_cat_title = $_POST['show_cat_title'] == 'YES' ? true : false;
		$show_empty_message = $_POST['show_empty_message'] == 'YES' ? true : false;
		$order_by = $_POST['order_by'];
		$order = $_POST['order'];
		$dropdown = $_POST['out_dropdown'] == 'YES' ? true : false;
		$dropdown_selected = $_POST['dropdown_selected'];
		$most_top = $_POST['category_top'];
		$ordered_list = $_POST['show_as_ordered_list'] == 'YES' ? true : false;
		$show_thumbnail = $_POST['show_thumbnail'] == 'YES' ? true : false;
		$thumbnail_size = $_POST['thumbnail_size'];
		$show_thumbnail_with_title = $_POST['show_PDF_title_with_featured_image'] == 'YES' ? true : false;
		$open_target = $_POST['open_target'];
		$nofollow_tag = $_POST['nofollow_tag'] == 'YES' ? true : false;
		$show_date_in_title = $_POST['show_date_in_title'] == 'YES' ? true : false;
		$date_format_str = $_POST['date_format'];
		$date_before_title = $_POST['date_before_title'] == 'YES' ? true : false;
		$columns = $_POST['columns'];
		$multicolumns = $_POST['enable_multi_column_layout'] == 'YES' ? true : false;
		$title_postion_4_mulitcolumn = $_POST['multi_column_title_position'];
		$custom_link_text = $_POST['custom_link_text'];
		
		$str = $this->get_all_pdfs_within_category( $category_id, $order_by, $order, $dropdown, $dropdown_selected, $most_top, $ordered_list, 
                                                                    $show_thumbnail, $thumbnail_size, $show_thumbnail_with_title, $open_target, $nofollow_tag, 
                                                                    $show_date_in_title, $date_format_str, $date_before_title, $columns, $multicolumns, 
                                                                    $title_postion_4_mulitcolumn, $custom_link_text );
		if( $show_empty_message && $str == "" ){
			$str = '<p><span class="bsk-pdf-category-empty-message">'.$cat_obj->empty_message.'</span></p>';
		}
		
		if( $show_cat_title ){
			$str =	'<h2>'.$cat_obj->title.'</h2>'."\n".$str;
		}
		die( $str );
	}
}