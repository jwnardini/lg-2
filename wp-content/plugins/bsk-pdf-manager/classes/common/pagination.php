<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class BSKPDFM_Common_Pagination {
    
    public static  function bsk_pdf_show_pagination( $total_results_count, $pdfs_per_page, $paged ) {
        if( $total_results_count < 1 || 
            $pdfs_per_page < 1 ||
            $total_results_count <= $pdfs_per_page ){
            return '';
        }
        
        $max_pages_number = ceil( $total_results_count / $pdfs_per_page );
        $page_numbers_array = array();
        $pagination_str = '';
        
        if( $paged > $max_pages_number ){
            $paged = $max_pages_number;
        }
        
        if( $paged < 1 ){
            $paged = 1;
        }
        
        // Add current page to the array.
        if( $paged >= 1 ){
            $page_numbers_array[] = $paged;
        }

        // Add the pages around the current page to the array.
        if( $paged >= 3 ){
            $page_numbers_array[] = $paged - 1;
            $page_numbers_array[] = $paged - 2;
        }

        if( ( $paged + 2 ) <= $max_pages_number ){
            $page_numbers_array[] = $paged + 2;
            $page_numbers_array[] = $paged + 1;
        }

        $pagination_str .= '<div class="bsk-pdfm-pagination">';
        $pagination_str .= '<ul>';

        // Previous page anchor
        if( $paged > 1 ){
            $prev_page_text = apply_filters( 'bsk_pdfm_filter_pagination_prev_text', '&#x000AB; Previous Page' );
            $pagination_str .= '<li class="pagination-previous">
                                            <a href="javascript:void(0);" data-page="'.($paged - 1 ).'" class="bsk-pdfm-pagination-anchor">'.$prev_page_text.'</a>
                                       </li>'. "\n";
        }
        
        //add first page, add ellipsis if need
        if( ! in_array( 1, $page_numbers_array ) ){
            $class = 1 == $paged ? ' class="active"' : '';
            $pagination_str .= '<li'.$class.'><a href="javascript:void(0);" data-page="1" class="bsk-pdfm-pagination-anchor">1</a></li>'. "\n";
            
            //plus ellipsis
            if ( ! in_array( 2, $page_numbers_array ) ) {
                $pagination_str .= '<li class="pagination-omission">&#x02026;</li>' . "\n";
            }

        }

        sort( $page_numbers_array );
        foreach( $page_numbers_array as $page_number ) {
            $class = $paged == $page_number ? ' class="active" ' : '';
            $pagination_str .= '<li'.$class.'><a href="javascript:void(0);" data-page="'.$page_number.'" class="bsk-pdfm-pagination-anchor">'.$page_number.'</a></li>' . "\n";
        }

        // add last page, plus ellipses if necessary.
        if ( ! in_array( $max_pages_number, $page_numbers_array ) ) {

            if ( ! in_array( $max_pages_number - 1, $page_numbers_array ) ) {
                $pagination_str .= '<li class="pagination-omission">&#x02026;</li>' . "\n";
            }

            $class = $paged == $max_pages_number ? ' class="active"' : '';
            $pagination_str .= '<li'.$class.'>
                                            <a href="javascript:void(0);" data-page="'.$max_pages_number.'" class="bsk-pdfm-pagination-anchor">'.$max_pages_number.'</a>
                                       </li>' . "\n";
        }

        // Next page anchor
        $next_page_text = apply_filters( 'bsk_pdfm_filter_pagination_next_text', 'Next Page &#x000BB;' );
        if( $paged < $max_pages_number ){
            $pagination_str .= '<li class="pagination-next">
                                            <a href="javascript:void(0);" data-page="'.( $paged + 1 ).'" class="bsk-pdfm-pagination-anchor">'.$next_page_text.'</a>
                                       </li>' . "\n";
        }

        $pagination_str .= '</ul>';
        
        $ajax_loader_span_class = 'bsk-pdfm-pagination-ajax-loader';
        $ajax_loader_img_url = apply_filters( 'bsk_pdfm_filter_ajax_loader_url', BSKPDFManager::$_ajax_loader_img_url, $ajax_loader_span_class );
        $pagination_str .= '<span class="'.$ajax_loader_span_class.'"><img src="'.$ajax_loader_img_url.'" /></span>';

        $pagination_str .= '</div>';

        return $pagination_str;
    }
    
}//end of class
