<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class BSKPDFM_Common_Search {
    
    public static function show_search_bar( 
                                                            $search_bar_type, 
                                                            $categories_loop_array,
                                                            $availabe_ids_array,
                                                            $category_hierchical, 
                                                            $cat_order_by_str,
                                                            $cat_order_str,
                                                            $year_range, 
                                                            $year_order 
                                                            ){
        if( $search_bar_type != 'KEYWORDS' && 
            $search_bar_type != 'YEAR_KEYWORDS' && 
            $search_bar_type != 'CATEGORY_YEAR_KEYWORDS' ){
            return '';
        }
        
        $category_placeholder = apply_filters( 'bsk_pdfm_filter_search_bar_category_option_none', 'All Categories' );
        $year_placeholder = apply_filters( 'bsk_pdfm_filter_search_bar_year_option_none', 'Any Year' );
        $keywords_placeholder = apply_filters( 'bsk_pdfm_filter_search_bar_keywords_placeholder', 'Keywords' );
        $clear_anchor_text = apply_filters( 'bsk-pdfm-filter-search-bar-clear-text', 'Reset' );
        
        $ajax_loader_span_class = 'bsk-pdfm-searchbar-ajax-loader';
        $ajax_loader_img_url = apply_filters( 'bsk-pdfm-filter-ajax-loader-url', BSKPDFManager::$_ajax_loader_img_url, $ajax_loader_span_class );
        
        $category_select_str = '';
        if( $search_bar_type == 'CATEGORY_YEAR_KEYWORDS' ){
            $category_select_str = BSKPDFM_Common_Display::get_category_dropdown(   
                                                                                                            $categories_loop_array, 
                                                                                                            $availabe_ids_array, 
                                                                                                            0,
                                                                                                            $category_hierchical,
                                                                                                            $cat_order_by_str,
                                                                                                            $cat_order_str,
                                                                                                            'Any Category',
                                                                                                            false );
        }
        $year_select_str = '';
        if( $search_bar_type == 'CATEGORY_YEAR_KEYWORDS' || $search_bar_type == 'YEAR_KEYWORDS' ){
            $year_select_str = BSKPDFM_Common_Display::get_year_dropdown( $year_range, $year_order, $year_placeholder );
        }
        
        $search_bar_input_class = ' '.strtolower( $search_bar_type );
        $search_bar_str  = '<div class="bsk-pdfm-search-bar">
                                        <div class="bsk-pdfm-search-input'.$search_bar_input_class.'">'.
                                            $category_select_str.$year_select_str.'
                                            <input class="bsk-pdfm-search-keywords" placeholder="'.$keywords_placeholder.'" />
                                            <button type="button" class="bsk-pdfm-search-anchor">
                                                <svg class="icon icon-search" aria-hidden="true" role="img" viewBox="0 0 30 32" width="100%" height="100%">
                                                    <path class="path1" d="M20.571 14.857q0-3.304-2.348-5.652t-5.652-2.348-5.652 2.348-2.348 5.652 2.348 5.652 5.652 2.348 5.652-2.348 2.348-5.652zM29.714 29.714q0 0.929-0.679 1.607t-1.607 0.679q-0.964 0-1.607-0.679l-6.125-6.107q-3.196 2.214-7.125 2.214-2.554 0-4.884-0.991t-4.018-2.679-2.679-4.018-0.991-4.884 0.991-4.884 2.679-4.018 4.018-2.679 4.884-0.991 4.884 0.991 4.018 2.679 2.679 4.018 0.991 4.884q0 3.929-2.214 7.125l6.125 6.125q0.661 0.661 0.661 1.607z"></path>
                                                </svg>
                                                <span class="screen-reader-text">Search</span>
                                            </button>
                                        </div>
                                        <div class="bsk-pdfm-search-results">
                                            <span class="'.$ajax_loader_span_class.'"><img src="'.$ajax_loader_img_url.'" /></span>
                                            <h3 class="bsk-pdfm-search-results-desc">
                                                <span class="bsk-pdfm-search-results-records">Search Results for: </span><span class="bsk-pdfm-search-results-keyword"></span>
                                            </h3>
                                            <a class="bsk-pdfm-search-clear-anchor" href="javascript:void(0);">'.$clear_anchor_text.'</a>
                                        </div>
                                    </div>';
        
        return $search_bar_str;
    }//end of function
    
}//end of class
