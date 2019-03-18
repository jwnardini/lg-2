<?php
class BSKPDFManagerWidget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'bsk_pdf_manager_widget', // Base ID
            'BSK PDF Manager', // Name
            array( 'description' => __( 'Display specific PDFs in widget area', 'bsk-pdfm-pro' ), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        
        $pdfm_widget_title = '';
        $pdfm_all_or_specific = 'SPECIFIC';
        $pdfm_specific_ids = '';
        $pdfm_order_by = 'IDS_SEQUENCE';
        $pdfm_order = '';
        $pdfm_most_top_of = 0;
        $pdfm_show_date = 'NO';
        $pdfm_date_format_str = '';
        $pdfm_date_before_title = 'NO';            
        $pdfm_open_in_new = 'NO';
        $pdfm_ordered_list = 'NO';
        $pdfm_no_follow_tag = 'NO';
        if( isset( $instance[ 'pdfm_widget_title' ] ) ){
            //new version 2.0
            $pdfm_widget_title = $instance['pdfm_widget_title'];
            $pdfm_all_or_specific = $instance['pdfm_all_or_specific'];
            $pdfm_specific_ids = $instance['pdfm_specific_ids'];
            $pdfm_order_by = $instance['pdfm_order_by'];
            $pdfm_order = $instance['pdfm_order'];
            $pdfm_most_top_of = $instance['pdfm_most_top_of'];
            $pdfm_show_date = $instance['pdfm_show_date'];
            $pdfm_date_format_str = $instance['pdfm_date_format_str'];
            $pdfm_date_before_title = $instance['pdfm_date_before_title'];            
            $pdfm_open_in_new = $instance['pdfm_open_in_new'];
            $pdfm_ordered_list = $instance['pdfm_ordered_list'];
            $pdfm_no_follow_tag = $instance['pdfm_no_follow_tag'];
        }else{
            //version 1.0
            $pdfm_widget_title = $instance['widget_title'];
            $pdfm_specific_ids = $instance['bsk_pdf_manager_ids'];
            $pdfm_open_in_new = $instance['bsk_pdf_manager_open_in_new'];
            $pdfm_ordered_list = $instance['bsk_pdf_manager_show_ordered_list'];
        }
        
        echo $args['before_widget'];

        if( trim($pdfm_widget_title) ){
            echo '<h2 class="widget-title">'.$pdfm_widget_title.'</h2>';
        }

        //id string
        $ids_array = array();
        if( $pdfm_all_or_specific != 'ALL' ){
            $ids_string = trim( $pdfm_specific_ids );
            if( $ids_string == "" ){
                echo $args['after_widget'];
                return;
            }
            $ids_array = explode(',', $ids_string);
            if( !is_array($ids_array) || count($ids_array) < 1 ){
                echo $args['after_widget'];
                return;
            }
            foreach($ids_array as $key => $pdf_id){
                $pdf_id = intval(trim($pdf_id));
                if( is_int($pdf_id) == false ){
                    unset($ids_array[$key]);
                }
                $ids_array[$key] = $pdf_id;
            }
        }
        
        $query_args = array();
        $query_args['ids_array'] = $ids_array;
        $query_args['show_all_pdfs'] = $pdfm_all_or_specific == 'ALL' ? true : false;
        $query_args['order_by'] = $pdfm_order_by == 'IDS_SEQUENCE' ? '' : $pdfm_order_by;
        $query_args['order'] = $pdfm_order;
        $query_args['most_top'] = $pdfm_order_by == 'IDS_SEQUENCE' ? 0 : $pdfm_most_top_of;

        $pdfs_query_return = BSKPDFM_Common_Data_Source::bsk_pdfm_get_pdfs( $query_args );
        if( !$pdfs_query_return || !is_array($pdfs_query_return) || count($pdfs_query_return) < 1 ){
            echo $args['after_widget'];
            return;
        }

        $ul_or_ol = $pdfm_ordered_list == 'YES' ? 'ol' : 'ul';
		$show_pdf_title = false;
        $image_position = 'left';
        $open_target_str = $pdfm_open_in_new == 'YES' ? ' target="_blank"' : '';
        $nofollow_tag = $pdfm_no_follow_tag == 'YES' ? ' rel="nofollow"' : '';
        $show_date = $pdfm_show_date == 'YES' ? true : false;
        $date_format_str = $pdfm_date_format_str;
        $date_before_title = $pdfm_date_before_title == 'YES' ? true : false;
        $date_format_str = $date_before_title ? 'd/m/Y ' : ' d/m/Y';
        if( trim($pdfm_date_format_str) ){
            $date_format_str = $pdfm_date_format_str;
        }    
        
        $pdfs_results_array = $pdfs_query_return['pdfs'];
        
        //sort by id sequence
        $sorted_pdf_results_array = array();
        if( $pdfm_all_or_specific == 'SPECIFIC' && $pdfm_order_by == 'IDS_SEQUENCE' ){
            $i = 0;
            foreach( $ids_array as $id ){
                if( !isset($pdfs_results_array[$id]) ){
                    continue;
                }
                $sorted_pdf_results_array[$id] = $pdfs_results_array[$id];
                $i++;
                if( $pdfm_most_top_of > 0 && $i >= $pdfm_most_top_of ){
                    break;
                }
            }
        }else{
            $sorted_pdf_results_array = $pdfs_results_array;
        }
        
        echo '<div class="bsk-pdfm-widget-output-container">';
        $str_body = BSKPDFM_Common_Display::display_pdfs_in_ul_or_ol(
                                                                                                     $ul_or_ol,
                                                                                                     false, //$only_li
                                                                                                     'bsk-pdfm-pdfs-'.$ul_or_ol.'-list',
                                                                                                     $sorted_pdf_results_array, 
                                                                                                     $open_target_str, $nofollow_tag, 
                                                                                                     $show_date, $date_format_str, $date_before_title,
                                                                                                     'h3'
                                                                                                    );
        echo $str_body;
        
        //credit
        if( isset( $plugin_settings['enable_credit'] ) && $plugin_settings['enable_credit'] == 'Yes' ){
            $credit_text = 'PDFs powered by PDF Manager Pro';
            if( $plugin_settings['credit_text'] ){
                $credit_text = $plugin_settings['credit_text'];
            }
            $pdf_manager_pro_link = 'https://www.bannersky.com/bsk-pdf-manager/';
            echo '<p class="bsk-pdfm-credit-link-container"><a href="'.$pdf_manager_pro_link.'" target="_blank">'.$credit_text.'</a></p>';
        }
        
        echo '</div><!-- //bsk-pdfm-widget-output-container -->';
        
        echo $args['after_widget'];
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();

        $instance['pdfm_widget_title'] = strip_tags( $new_instance['pdfm_widget_title'] );
        $instance['pdfm_all_or_specific'] = $new_instance['pdfm_all_or_specific'];
        $instance['pdfm_specific_ids'] = strip_tags( $new_instance['pdfm_specific_ids'] );
        $instance['pdfm_order_by'] = $new_instance['pdfm_order_by'];
        $instance['pdfm_order'] = $new_instance['pdfm_order_by'];
        $instance['pdfm_most_top_of'] = $new_instance['pdfm_most_top_of'];
        $instance['pdfm_show_date'] = $new_instance['pdfm_show_date'];
        $instance['pdfm_date_format_str'] = $new_instance['pdfm_date_format_str'];
        $instance['pdfm_date_before_title'] = $new_instance['pdfm_date_before_title'];
        $instance['pdfm_ordered_list'] = $new_instance['pdfm_ordered_list'];
        $instance['pdfm_open_in_new'] = $new_instance['pdfm_open_in_new'];
        $instance['pdfm_no_follow_tag'] = $new_instance['pdfm_no_follow_tag'];

        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $pdfm_widget_title = '';
        $pdfm_all_or_specific = 'SPECIFIC';
        $pdfm_specific_ids = '';
        $pdfm_order_by = 'IDS_SEQUENCE';
        $pdfm_order = '';
        $pdfm_most_top_of = '';
        $pdfm_show_date = 'NO';
        $pdfm_date_format_str = '';
        $pdfm_date_before_title = 'NO';
        $pdfm_open_in_new = 'NO';
        $pdfm_ordered_list = 'NO';
        $pdfm_no_follow_tag = 'NO';
        if( isset( $instance[ 'pdfm_widget_title' ] ) ){
            //new version 2.0
            $pdfm_widget_title = $instance['pdfm_widget_title'];
            $pdfm_all_or_specific = $instance['pdfm_all_or_specific'];
            $pdfm_specific_ids = $instance['pdfm_specific_ids'];
            $pdfm_order_by = $instance['pdfm_order_by'];
            $pdfm_order = $instance['pdfm_order'];
            $pdfm_most_top_of = $instance['pdfm_most_top_of'];
            $pdfm_show_date = $instance['pdfm_show_date'];
            $pdfm_date_format_str = $instance['pdfm_date_format_str'];
            $pdfm_date_before_title = $instance['pdfm_date_before_title'];
            $pdfm_open_in_new = $instance['pdfm_open_in_new'];
            $pdfm_ordered_list = $instance['pdfm_ordered_list'];
            $pdfm_no_follow_tag = $instance['pdfm_no_follow_tag'];
        }else{
            //version 1.0
            $pdfm_widget_title = isset( $instance['widget_title'] ) ? $instance['widget_title']  : '';
            $pdfm_specific_ids = isset( $instance['bsk_pdf_manager_ids'] ) ? $instance['bsk_pdf_manager_ids']  : '';
            $pdfm_open_in_new = isset( $instance['bsk_pdf_manager_open_in_new'] ) ? $instance['bsk_pdf_manager_open_in_new']  : 'NO';
            $pdfm_ordered_list = isset( $instance['bsk_pdf_manager_show_ordered_list'] ) ? $instance['bsk_pdf_manager_show_ordered_list']  : 'NO';
        }
        
        $widget_pro_tips_array = array( 'Exclude PDF ID',  'Featured image' );
    ?>
    <div class="bsk-pdfm-widget-setting-container bsk-pdfm-pdfs-widget">
        <?php $this->bsk_pdf_manager_show_pro_tip_box( $widget_pro_tips_array ); ?>
        <p>Title:<br />
            <input name="<?php echo $this->get_field_name( 'pdfm_widget_title' ); ?>" value="<?php echo $pdfm_widget_title; ?>" style="width:100%;" />
        </p>
        <?php
        $all_checked = $pdfm_all_or_specific == 'ALL' ? ' checked' : '';
        $specific_checked = $pdfm_all_or_specific == 'SPECIFIC' ? ' checked' : '';
        $specific_ids_input_display = $pdfm_all_or_specific == 'SPECIFIC' ? 'block' : 'none';
        ?>
        <p>Display PDFs of: 
            <label style="margin-left: 10px;">
                <input name="<?php echo $this->get_field_name( 'pdfm_all_or_specific' ); ?>" type="radio" value="ALL" class="bsk-pdfm-show-all-or-specific-pdfs" <?php echo $all_checked; ?> /> All
            </label>
            <label style="margin-left: 10px;">
                <input name="<?php echo $this->get_field_name( 'pdfm_all_or_specific' ); ?>"  type="radio" value="SPECIFIC"  class="bsk-pdfm-show-all-or-specific-pdfs" <?php echo $specific_checked; ?> /> Specific
            </label>
        </p>
        <p class="bsk-pdfm-widget-specific-ids-input-p" style="display:<?php echo $specific_ids_input_display; ?>">PDF IDs:<br />
            <input name="<?php echo $this->get_field_name( 'pdfm_specific_ids' ); ?>" style="width:100%;" value="<?php echo $pdfm_specific_ids; ?>" placeholder="List of PDFs' ID, separated by comma" class="bsk-pdfm-ids-input" />
        </p>
        <p>Exclude PDF of ID in:<br />
            <input name="<?php echo $this->get_field_name( 'pdfm_exclude_ids' ); ?>" style="width:100%;" value="" placeholder="List of PDFs' ID, separated by comma" class="bsk-pdfm-ids-input" disabled />
        </p>
        <p>Order by:&nbsp;
            <select name="<?php echo $this->get_field_name( 'pdfm_order_by' ); ?>" class="bsk-pdfm-order-by">
                <option value="IDS_SEQUENCE" <?php if( $pdfm_order_by == 'IDS_SEQUENCE' ) echo 'selected'; ?>>PDFs ID sequence</option>
                <option value="title" <?php if( $pdfm_order_by == 'title' ) echo 'selected'; ?>>Title</option>
                <option value="last_date" <?php if( $pdfm_order_by == 'last_date' ) echo 'selected'; ?>>Date</option>
                <option value="order_num" <?php if( $pdfm_order_by == 'order_num' ) echo 'selected'; ?>>Custom Order</option>
            </select>
        </p>
        <p>Order:&nbsp;
            <select name="<?php echo $this->get_field_name( 'pdfm_order' ); ?>">
                <option value="ASC" <?php if( $pdfm_order == 'ASC' ) echo 'selected'; ?>>ASC</option>
                <option value="DESC" <?php if( $pdfm_order == 'DESC' ) echo 'selected'; ?>>DESC</option>
            </select>
        </p>
        <p>Most top of <input type="number" name="<?php echo $this->get_field_name( 'pdfm_most_top_of' ); ?>" value="<?php echo $pdfm_most_top_of; ?>" style="width:50px;" min="0" step="1" /> PDFs will be shown
            <span style="display: block;font-style:italic;">0 means not apply</span>
        </p>
        <?php 
        $checked_str = $pdfm_show_date == 'YES' ? ' checked="checked"' : ''; 
        $checked_str_for_date_before = $pdfm_date_before_title == 'YES' ? ' checked="checked"' : '';
        $data_format_str_p_display = $pdfm_show_date == 'YES' ? 'block' : 'none';
        $date_before_title_p_display = $pdfm_show_date == 'YES' ? 'block' : 'none';
        ?>
        <p>
            <label>
                <input type="checkbox" name="<?php echo $this->get_field_name( 'pdfm_show_date' ); ?>" value="YES"<?php echo $checked_str; ?> class="bsk-pdfm-widget-show-date" /> Shows date?
            </label>
        </p>
        <p class="bsk-pdfm-widget-date-format-p" style="display:<?php echo $data_format_str_p_display; ?>">
            Date format:<br />
            <input type="text" name="<?php echo $this->get_field_name( 'pdfm_date_format_str' ); ?>" value="<?php echo $pdfm_date_format_str; ?>" placeholder="eg: --F d, Y, --d/m/Y, Y-m-d_" style="width: 100%;">
            <span style="display: block; font-style: italic;">leave blank to use default format  d/m/Y</span>
        </p>
        <p class="bsk-pdfm-widget-date-before-title-p" style="display:<?php echo $date_before_title_p_display; ?>">
            <label>
                <input type="checkbox" name="<?php echo $this->get_field_name( 'pdfm_date_before_title' ); ?>" value="YES"<?php echo $checked_str_for_date_before; ?> id="<?php echo $this->get_field_id( 'pdfm_date_before_title' ); ?>" /> Shows date before PDF title
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="<?php echo $this->get_field_name( 'pdfm_show_thumbnail' ); ?>" value="YES"<?php echo $checked_str; ?> class="bsk-pdfm-widget-show-thumbnail" disabled /> Shows featured image?
            </label>
        </p>
        <p class="bsk-pdfm-widget-thumbnail-size-p">
            Featured image size:<br />
            <select name="<?php echo $this->get_field_name( 'pdfm_show_thumbnail_size' ); ?>" disabled>
            <?php
            $image_sizes = BSKPDFM_Common_Backend::get_image_sizes();
            foreach ( $image_sizes as $size_name => $size_name_dimission )  {
                if ( $size_name_dimission['width'] < 1 || 
                     $size_name_dimission['height'] < 1 ){
                    continue;
                }
                echo '<option value="'.$size_name.'">'.$size_name.'</option>';
            }
            echo '<option value="full">full</option>';
            ?>
            </select>
        </p>
        <p class="bsk-pdfm-widget-thumbnail-with-pdf-title-p" style="display:<?php echo $show_thumbnail_with_title_p_display; ?>">
            <label>
                <input type="checkbox" name="<?php echo $this->get_field_name( 'pdfm_thumbnail_with_title' ); ?>" value="YES" id="<?php echo $this->get_field_id( 'pdfm_thumbnail_with_title' ); ?>" disabled /> Shows PDF title with featured image?
            </label>
        </p>
        <?php

        $checked_str = $pdfm_ordered_list == 'YES' ? ' checked="checked"' : '';
        ?>
        <p>
            <label>
                <input type="checkbox" name="<?php echo $this->get_field_name( 'pdfm_ordered_list' ); ?>" value="YES"<?php echo $checked_str; ?> /> Shows PDF as ordered list?
            </label>
        </p>
        <?php $checked_str = $pdfm_open_in_new == 'YES' ? ' checked="checked"' : ''; ?>
        <p>
            <label>
                <input type="checkbox" name="<?php echo $this->get_field_name( 'pdfm_open_in_new' ); ?>" value="YES"<?php echo $checked_str; ?> /> Opens PDF in a new window or tab?
            </label>
        </p>
        <?php $checked_str = $pdfm_no_follow_tag == 'YES' ? ' checked="checked"' : ''; ?>
        <p>
            <label>
                <input type="checkbox" name="<?php echo $this->get_field_name( 'pdfm_no_follow_tag' ); ?>" value="YES"<?php echo $checked_str; ?> /> Add rel="nofollow" tag to PDF link?
            </label>
        </p>
    </div>
    <?php
    } //end of function
    
    function bsk_pdf_manager_show_pro_tip_box( $tips_array ){
        $tips = implode( ', ', $tips_array );
		$str = 
        '<div class="bsk-pro-tips-box">
			<b>Pro Tip: </b><span class="bsk-pro-tips-box-tip">'.$tips.' only supported in Pro version</span>
			<a href="'.BSKPDFManager::$url_to_upgrade.'" target="_blank">Upgrade to Pro</a>
		</div>';
		
		echo $str;
	}
    
} // class