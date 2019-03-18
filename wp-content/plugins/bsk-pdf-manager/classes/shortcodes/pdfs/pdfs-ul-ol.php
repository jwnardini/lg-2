<?php

class BSKPDFM_Shortcodes_PDFs_UL_OL {
    
	public function __construct() {
        add_shortcode('bsk-pdfm-pdfs-ul', array($this, 'bsk_pdf_manager_show_pdfs_in_ul') );
        add_shortcode('bsk-pdfm-pdfs-ol', array($this, 'bsk_pdf_manager_show_pdfs_in_ol') );
	}
    
	function bsk_pdf_manager_show_pdfs_in_ul( $atts, $content ){
        if( !is_array( $atts ) ){
            $atts = array();
        }
        $atts['ul_or_ol'] = 'ul';
        
        return $this->bsk_pdf_manager_show_pdfs_in_ul_ol( $atts );
    }
    
    function bsk_pdf_manager_show_pdfs_in_ol( $atts, $content ){
        if( !is_array( $atts ) ){
            $atts = array();
        }
        $atts['ul_or_ol'] = 'ol';
        
        return $this->bsk_pdf_manager_show_pdfs_in_ul_ol( $atts );
    }
    
	function bsk_pdf_manager_show_pdfs_in_ul_ol( $atts ){		
		//read plugin settings
		$default_enable_featured_image = true;
		$default_thumbnail_html = '';
		$default_thumbnail_size = 'thumbnail';
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
			if( isset($plugin_settings['enable_featured_image']) ){
				$default_enable_featured_image = $plugin_settings['enable_featured_image'];
			}
			
			if( isset($plugin_settings['default_thumbnail_size']) ){
				$default_thumbnail_size = $plugin_settings['default_thumbnail_size'];
			}
            if( $default_enable_featured_image && isset($plugin_settings['default_thumbnail_id']) ){
                $default_thumbnail_id = $plugin_settings['default_thumbnail_id'];
                if( $default_thumbnail_id && get_post( $default_thumbnail_id ) ){
                    $default_thumbnail_html = wp_get_attachment_image( $default_thumbnail_id, $default_thumbnail_size );
                }
            }
		}
		
		$shortcode_atts = shortcode_atts( 
                                          array(
                                                   'ul_or_ol' => 'ul',
                                                   'id' => '', 
                                                   'link_only' => 'no',
                                                   'url_only' => 'no',
                                                   'order_by' => '',
                                                   'order' => '', 
                                                   'target' => '',
                                                   'most_top' => 0,
                                                   'nofollow_tag' => 'no',
                                                   'show_date' => 'no',
                                                   'date_format' => ' d/m/Y',
                                                   'date_before_title' => 'no',
                                                   'output_container_class' => ''
                                                 ), 
                                           $atts
                                        );
        
        $ul_or_ol = strtoupper($shortcode_atts['ul_or_ol']) == 'OL' ? 'ol' : 'ul';
            
		//organise ids array
		$ids_array = array();
        $show_all_pdfs = false;
		if( trim($shortcode_atts['id']) == "" ){
			return '';
		}
        if( strtoupper(trim($shortcode_atts['id'])) == 'ALL' ){
            $show_all_pdfs = true;  
        }else if( is_string($shortcode_atts['id']) ){
			$ids_array = explode(',', $shortcode_atts['id']);
			foreach($ids_array as $key => $pdf_id){
				$pdf_id = intval(trim($pdf_id));
				if( $pdf_id < 1 ){
					unset($ids_array[$key]);
                    continue;
				}
				$ids_array[$key] = $pdf_id;
			}
		}
        
		if( ( !is_array($ids_array) || count($ids_array) < 1 ) && $show_all_pdfs == false ){
			return '';
		}
        
		//process open target
		$open_target_str = '';
		if( $shortcode_atts['target'] == '_blank' ){
			$open_target_str = ' target="_blank"';
		}
        
		//link only
		$show_link_only = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('link_only', $shortcode_atts);
		//most top
		$most_top = intval( $shortcode_atts['most_top'] );

		//anchor nofollow tag
		$nofollow_tag = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('nofollow_tag', $shortcode_atts);
        if( $nofollow_tag ){
            $nofollow_tag = ' rel="nofollow"';
        }
		//show date in title
		$show_date = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('show_date', $shortcode_atts);
		//date postion
		$date_before_title = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('date_before_title', $shortcode_atts);
		
		//date format
		$date_format_str = $date_before_title ? 'd/m/Y ' : ' d/m/Y';
		if( $shortcode_atts['date_format'] && is_string($shortcode_atts['date_format']) && $shortcode_atts['date_format'] != ' d/m/Y' ){
			$date_format_str = $shortcode_atts['date_format'];
		}

        //show pdf ulr only
        $show_PDF_url_only = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('url_only', $shortcode_atts);

        $query_args = array();
        $query_args['show_all_pdfs'] = $show_all_pdfs;
        $query_args['ids_array'] = $ids_array;
        $query_args['order_by'] = $shortcode_atts['order_by'];
        $query_args['order'] = $shortcode_atts['order'];
        $query_args['most_top'] = $most_top;
        
        $pdfs_query_return = BSKPDFM_Common_Data_Source::bsk_pdfm_get_pdfs( $query_args );
        
        if( $show_link_only ){
            if( !$pdfs_query_return || !is_array($pdfs_query_return) || count($pdfs_query_return) < 1 ){
                return '';
            }
            $pdfs_results_array = $pdfs_query_return['pdfs'];
            $str_body = BSKPDFM_Common_Display::show_pdfs_link_only(
                                                                                                     $pdfs_results_array, 
                                                                                                     $open_target_str, $nofollow_tag, 
                                                                                                     $show_date, $date_format_str, $date_before_title
                                                                                                    );
            return $str_body;
        }
        
        if( $show_PDF_url_only ){
            if( !$pdfs_query_return || !is_array($pdfs_query_return) || count($pdfs_query_return) < 1 ){
                return '';
            }
            $pdfs_results_array = $pdfs_query_return['pdfs'];
            $str_body = BSKPDFM_Common_Display::show_pdfs_url_only( $pdfs_results_array );
            return $str_body;
        }
        
        $output_container_class = trim($shortcode_atts['output_container_class']) ? ' '.trim($shortcode_atts['output_container_class']) : '';
        $str_body = '<div class="bsk-pdfm-output-container shortcode-pdfs layout-'.$ul_or_ol.$output_container_class.'">';
        if( !$pdfs_query_return || !is_array($pdfs_query_return) || count($pdfs_query_return) < 1 ){
            $str_body .= '</div><!-- //bsk-pdfm-pdfs-output -->';
            return $str_body;
        }
        
        $str_body .= '<div class="bsk-pdfm-pdfs-output pdfs-in-'.$ul_or_ol.'">';
        
        $pdfs_results_array = $pdfs_query_return['pdfs'];
        $str_body .= BSKPDFM_Common_Display::display_pdfs_in_ul_or_ol(
                                                                                                     $ul_or_ol,
                                                                                                     false,
                                                                                                     'bsk-pdfm-pdfs-'.$ul_or_ol.'-list',
                                                                                                     $pdfs_results_array, 
                                                                                                     $open_target_str, $nofollow_tag, 
                                                                                                     $show_date, $date_format_str, $date_before_title,
                                                                                                     'h3'
                                                                                                    );
        $str_body .= '</div><!-- //end for bsk-pdfm-pdfs-output -->';
        
        //credit
        if( isset( $plugin_settings['enable_credit'] ) && $plugin_settings['enable_credit'] == 'Yes' ){
            $credit_text = 'PDFs powered by PDF Manager Pro';
            if( $plugin_settings['credit_text'] ){
                $credit_text = $plugin_settings['credit_text'];
            }
            $pdf_manager_pro_link = 'https://www.bannersky.com/bsk-pdf-manager/';
            $str_body .= '<p class="bsk-pdfm-credit-link-container"><a href="'.$pdf_manager_pro_link.'" target="_blank">'.$credit_text.'</a></p>';
        }
        
        
        $str_body .= '</div><!-- //end for bsk-pdfm-output-container-->';

        return $str_body;
	} //end of function
    
}//end of class