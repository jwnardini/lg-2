<?php

class BSKPDFM_Shortcodes_Category_Functions {

    public static $_shortcode_category_atts = array(
                                                                        'id' => '',
                                                                        'cat_order_by' => '',
                                                                        'cat_order' => '',
                                                                        'show_cat_title' => '',
                                                                       );
    public static $_shortcode_pdfs_atts = array(
                                                                'order_by' => '',
                                                                'order' => '',
                                                                'target' => '',
                                                                'most_top' => 0,
                                                                'nofollow_tag' => 'no',
                                                                'show_date' => 'no',
                                                                'date_before_title' => 'no',
                                                                'date_format' => ' d/m/Y'
                                                                );
    public static $_shortcode_output_container_atts = array(
                                                                                    'output_container_class' => '',
                                                                                  );
    
	public function __construct() {
        
	}
    
    public static function process_bool_attr_val( $attr_val) {
        $return_bool = false;
        if( is_string( $attr_val ) ){
			$return_bool = strtoupper($attr_val) == "YES" ? true : false;
			if( $return_bool == false ){
				$return_bool = strtoupper($attr_val) == 'TRUE' ? true : false;
			}
		}else if( is_bool($attr_val) ){
			$return_bool = $attr_val;
		}

        return $return_bool;
    }
    
    public static function process_shortcode_parameters( $shortcode_atts ){
        
        $processed = array();
        foreach( $shortcode_atts as $key => $val ){
            $attr_val = trim( $val );
            switch( $key ){
                case 'id':
                    if( strtoupper( $attr_val ) == 'ALL' ){
                        $processed['id'] = 'ALL';
                    }else{
                        $temp_array = explode(',', $attr_val);
                        $temp_valid_array = array();
                        if( $temp_array && is_array( $temp_array ) && count( $temp_array ) > 0 ){
                            foreach( $temp_array as $temp_cat_id ){
                                $temp_valid_array[] = absint( $temp_cat_id );
                            }
                        }
                        $processed['id'] = implode( ',', $temp_valid_array );
                    }
                break;
                case 'shortcode_only_single':
                    $processed['shortcode_only_single'] = self::process_bool_attr_val( $attr_val );
                break;
                case 'cat_order_by':
                    $cat_order_by_str = ' C.`title`';
                    if( $attr_val == 'date' || $shortcode_atts['cat_order_by'] == 'last_date' ){
                        $cat_order_by_str = ' C.`last_date`';
                    }
                    $processed['cat_order_by'] = $cat_order_by_str;
                break;
                case 'cat_order':
                    $cat_order_str = ' ASC';
                    if( strtoupper(trim($attr_val)) == 'DESC' ){
                        $cat_order_str = ' DESC';
                    }
                    $processed['cat_order'] = $cat_order_str;
                break;
                case 'show_cat_title':
                    $processed['show_cat_title'] = self::process_bool_attr_val( $attr_val );
                break;
                /*
                  * for PDFs
                  */
                case 'order_by':
                    $processed['order_by'] = $attr_val;
                break;
                case 'order':
                    $processed['order'] = strtoupper($attr_val) == 'DESC' ? 'DESC' : 'ASC';
                break;
                case 'target':
                    $processed['target'] = $attr_val == '_blank' ? ' _blank' : '';
                break;
                case 'most_top':
                    $processed['most_top'] = absint( $attr_val );
                break;
                case 'nofollow_tag':
                    $processed['nofollow_tag'] = self::process_bool_attr_val( $attr_val );
                break;
                case 'show_date':
                    $processed['show_date'] = self::process_bool_attr_val( $attr_val );
                break;
                case 'date_before_title':
                    $processed['date_before_title'] = self::process_bool_attr_val( $attr_val );
                break;
                case 'date_format':
                    $date_format_str = $processed['date_before_title'] ? 'd/m/Y ' : ' d/m/Y';
                    if( $val != ' d/m/Y' ){
                        $date_format_str = $val;
                    }
                    $processed['date_format'] = $date_format_str;
                break;
                /*
                  * for output_container
                  */
                case 'output_container_class':
                    $processed['output_container_class'] = $attr_val;
                break;
                default:
                    $processed[$key] = $attr_val;
                break;
            }
        }

        /*
          * additional parameters
          */
        if( isset( $processed['ul_or_ol'] ) ){
            $processed['ul_or_ol'] = trim($shortcode_atts['ul_or_ol']) == 'ol' ? 'ol' : 'ul';
        }
        return $processed;
    }
    
    public static function get_shortcode_parameters_output( $processed_shortcode_parameters ){
        $shortcode_parameters_str = '';
        //output all shortcode parameters
        foreach( $processed_shortcode_parameters as $attr_name => $attr_val ){
            if( $attr_name == 'default_thumbnail_html' ){
                $shortcode_parameters_str .= '<input type="hidden" class="bsk-pdfm-shortcode-attr" data-attr_name="'.$attr_name.'" value="" />';
                continue;
            }
            $attr_val_str = $attr_val;
            if( is_array( $attr_val ) ){
                $attr_val_str = count( $attr_val ) > 0 ? implode(',', $attr_val_str) : '';
            }else if( is_bool( $attr_val ) ){
                $attr_val_str = $attr_val ? 'YES' : 'NO';
            }

            $shortcode_parameters_str .= '<input type="hidden" class="bsk-pdfm-shortcode-attr" data-attr_name="'.$attr_name.'" value="'.$attr_val_str.'" />';
        }
        return $shortcode_parameters_str;
    }
}