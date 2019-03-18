<?php

class BSKPDFM_Shortcodes_PDFs_Dropdown {
    
	public function __construct() {
		add_shortcode('bsk-pdfm-pdfs-dropdown', array($this, 'bsk_pdf_manager_show_pdfs_in_dropdown') );
	}
	
	function bsk_pdf_manager_show_pdfs_in_dropdown( $atts, $content ){
        //read plugin settings

		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
        
		$shortcode_atts = shortcode_atts( 
                                          array(
                                                   'id' => '', 
                                                   'option_none' => 'Select to open...',
                                                   'order_by' => '',
                                                   'order' => '', 
                                                   'target' => '_blank',
                                                   'most_top' => 0,
                                                   'show_date' => 'no',
                                                   'date_format' => ' d/m/Y',
                                                   'date_before_title' => 'no',
                                                   'output_container_class' => ''
                                                 ), 
                                           $atts
                                        );
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
        
        $option_none_text = trim( $shortcode_atts['option_none'] );
            
		//process open target
		$open_target_str = '';
		if( $shortcode_atts['target'] == '_blank' ){
			$open_target_str = ' data-target="_blank"';
		}
        
		//most top
		$most_top = intval( $shortcode_atts['most_top'] );
		//show date in title
		$show_date = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('show_date', $shortcode_atts);
		//date postion
		$date_before_title = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('date_before_title', $shortcode_atts);
		//date format
		$date_format_str = $date_before_title ? 'd/m/Y ' : ' d/m/Y';
		if( $shortcode_atts['date_format'] && is_string($shortcode_atts['date_format']) && $shortcode_atts['date_format'] != ' d/m/Y' ){
			$date_format_str = $shortcode_atts['date_format'];
		}
        
        $query_args = array();
        $query_args['show_all_pdfs'] = $show_all_pdfs;
        $query_args['ids_array'] = $ids_array;
        $query_args['order_by'] = $shortcode_atts['order_by'];
        $query_args['order'] = $shortcode_atts['order'];
        $query_args['most_top'] = $most_top;
        
        $pdfs_query_return = BSKPDFM_Common_Data_Source::bsk_pdfm_get_pdfs( $query_args );
        
        $output_container_class = trim($shortcode_atts['output_container_class']) ? ' '.trim($shortcode_atts['output_container_class']) : '';
        $str_body = '<div class="bsk-pdfm-output-container shortcode-pdfs layout-dropdown'.$output_container_class.'">';
        if( !$pdfs_query_return || !is_array($pdfs_query_return) || count($pdfs_query_return) < 1 ){
            $str_body .= '</div><!-- //bsk-pdfm-output-containe -->';
            return $str_body;
        }
        
        $str_body .= '<div class="bsk-pdfm-pdfs-output pdfs-in-dropdown">';
        
        $pdfs_results_array = $pdfs_query_return['pdfs'];
        $str_body .= BSKPDFM_Common_Display::show_pdfs_in_dropdown( 
                                                                                                             $pdfs_results_array, 
                                                                                                             'bsk-pdfm-pdfs-dropdown', 
                                                                                                             $option_none_text,
                                                                                                             $open_target_str,
                                                                                                             $show_date, 
                                                                                                             $date_format_str,
                                                                                                             $date_before_title
                                                                                                            );
        
        $str_body .= '</div><!--// bsk-pdfm-pdfs-output-->';
        
        //credit
        if( isset( $plugin_settings['enable_credit'] ) && $plugin_settings['enable_credit'] == 'Yes' ){
            $credit_text = 'PDFs powered by PDF Manager Pro';
            if( $plugin_settings['credit_text'] ){
                $credit_text = $plugin_settings['credit_text'];
            }
            $pdf_manager_pro_link = 'https://www.bannersky.com/bsk-pdf-manager/';
            $str_body .= '<p class="bsk-pdfm-credit-link-container"><a href="'.$pdf_manager_pro_link.'" target="_blank">'.$credit_text.'</a></p>';
        }
        $str_body .= '</div><!--// bsk-pdfm-output-container-->';

        return $str_body;
	}
}