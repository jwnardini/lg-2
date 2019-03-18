<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class BSKPDFM_Common_Filter {
    
    public static function validate_date_weekday_filter( $filter_str ){
        
        $filter_str = trim( $filter_str );
        if( $filter_str == "" ){
            return false;
        }
        $filter_array = explode( '-', $filter_str );
        
        $return = array();
        foreach( $filter_array as $filter_format ){
            switch ( $filter_format ) {
                case 'Y':
                    $return['year' ] = $filter_format;
                break;
                case 'm':
                case 'M':
                case 'F':
                    $return['month' ] = $filter_format;
                break;
                case 'd':
                case 'j':
                case 'jS':
                    $return['day' ] = $filter_format;
                break;
                case 'D':
                case 'l':
                    $return['weekday' ] = $filter_format;
                break;
            }
        }
        if( count($return) < 1 ){
            return false;
        }
        
        return $return;
    }

    public static function show_date_filter( $pdf_results, $filter, $filter_order, $align_right ) {
        $validate_date_weekday_filter_array = self::validate_date_weekday_filter( $filter );
        if( $validate_date_weekday_filter_array == false ){
            return '';
        }
        
        if( !$pdf_results || 
            !is_array($pdf_results) ||
            count($pdf_results) < 1 ){

            return '';
        }
        
        $years_array = array();
        $months_array = array();
        $days_array = array();
        $weekdays_array = array();
        $weekdays_array_to_sort = array();
        $valid_date_array = array();
        foreach( $pdf_results as $pdf_obj ){
            $timestamp = strtotime( $pdf_obj->last_date );
            if( isset($validate_date_weekday_filter_array['year']) && $validate_date_weekday_filter_array['year'] ){
                $year = date( $validate_date_weekday_filter_array['year'], $timestamp );
                if( $year < 0 || $year > 3000 ){
                    continue;
                }
                $years_array[$year] = $year;
            }
            if( isset($validate_date_weekday_filter_array['month']) && $validate_date_weekday_filter_array['month'] ){
                $month_str = date( 'm', $timestamp );
                $month = date( $validate_date_weekday_filter_array['month'], $timestamp );
                $months_array[''.$month_str] = $month;
            }
            if( isset($validate_date_weekday_filter_array['day']) && $validate_date_weekday_filter_array['day'] ){
                $day_str = date( 'd', $timestamp );
                $day = date( $validate_date_weekday_filter_array['day'], $timestamp );
                $days_array[$day_str] = $day;
            }
            if( isset($validate_date_weekday_filter_array['weekday']) && $validate_date_weekday_filter_array['weekday'] ){
                $weekday = date( 'D', $timestamp );
                $label = date( $validate_date_weekday_filter_array['weekday'], $timestamp );
                $weekdays_array_to_sort[date( 'N', $timestamp )] = array( 'value' => $weekday, 'label' => $label );
            }
            $exist_date = date( 'Y-m-d', $timestamp );
            $valid_date_array[$exist_date] = date( 'Y-m-d-D', $timestamp );
        }
        
        //default ASC order
        ksort( $years_array );
        ksort( $months_array );
        ksort( $days_array );
        ksort( $weekdays_array_to_sort );
        
        $valid_filter_type = array_keys( $validate_date_weekday_filter_array );
        if( $filter_order ){
            $filter_order_array = explode( '-', $filter_order );
            if( $filter_order_array && is_array( $filter_order_array ) && count( $filter_order_array ) > 0 ){
                for( $i = 0; $i < count( $filter_order_array ); $i++ ){
                    if( strtoupper( $filter_order_array[$i] ) != 'DESC' ){
                        continue;
                    }
                    if( count($valid_filter_type) <= $i ){
                        break;
                    }
                    switch( $valid_filter_type[$i] ){
                        case 'year':
                            krsort( $years_array );
                        break;
                        case 'month':
                            krsort( $months_array );
                        break;
                        case 'day':
                            krsort( $days_array );
                        break;
                        case 'weekday':
                            krsort( $weekdays_array_to_sort );
                        break;
                    }
                }
            }
        }

        if( $weekdays_array_to_sort && count($weekdays_array_to_sort) > 0 ){
            foreach( $weekdays_array_to_sort as $weekday_index => $array_element ){
                $weekdays_array[$array_element['value']] = $array_element['label'];
            }
        }
        
        $data_filter_desc = apply_filters( 'bsk_pdfm_filter_date_filter_desc', '' );
        
        $class = $align_right ? ' bsk-pdfm-date-fitler-align-right' : '';
        $return_str = '';
        $return_str .= '<div class="bsk-pdfm-date-filter'.$class.'">';
        foreach( $validate_date_weekday_filter_array as $key => $format ){
            if( $key == 'year' && count($years_array) > 0 ){
                $return_str .= '<select class="bsk-pdfm-date-year">';
                $date_filter_year_default_text = apply_filters( 'bsk_pdfm_filter_date_filter_text_year', 'Year' );
                $return_str .= '<option value="">'.$date_filter_year_default_text.'</option>';
                foreach( $years_array as $year_int => $year_str ){
                    $return_str .= '<option value="'.$year_int.'">'.$year_str.'</option>';
                }
                $return_str .= '</select>';
            }

            if( $key == 'month' && count($months_array) > 0 ){
                $return_str .= '<select class="bsk-pdfm-date-month">';
                $date_filter_month_default_text = apply_filters( 'bsk_pdfm_filter_date_filter_text_month', 'Month' );
                $return_str .= '<option value="">'.$date_filter_month_default_text.'</option>';
                foreach( $months_array as $month_int => $month_str ){
                    $return_str .= '<option value="'.$month_int.'">'.$month_str.'</option>';
                }
                $return_str .= '</select>';
            }

            if( $key == 'day' && count($days_array) > 0 ){
                $return_str .= '<select class="bsk-pdfm-date-day">';
                $date_filter_day_default_text = apply_filters( 'bsk_pdfm_filter_date_filter_text_day', 'Day' );
                $return_str .= '<option value="">'.$date_filter_day_default_text.'</option>';
                foreach( $days_array as $day_int => $day_str ){
                    $return_str .= '<option value="'.$day_int.'">'.$day_str.'</option>';
                }
                $return_str .= '</select>';
            }
            
            if( $key == 'weekday' && count($weekdays_array) > 0 ){
                $return_str .= '<select class="bsk-pdfm-date-weekday">';
                $date_filter_weekday_default_text = apply_filters( 'bsk_pdfm_filter_date_filter_text_weekday', 'Weekday' );
                $return_str .= '<option value="">'.$date_filter_weekday_default_text.'</option>';
                foreach( $weekdays_array as $value => $label ){
                    $return_str .= '<option value="'.$value.'">'.strtoupper($label).'</option>';
                }
                $return_str .= '</select>';
            }
        }

        foreach( $valid_date_array as $date_str ){
            $return_str .= '<input type="hidden" class="bsk-pdfm-valid-date" value="'.$date_str.'" />';
        }
        $return_str .= '</div>';

        return $return_str;
    }
    
}//end of class
