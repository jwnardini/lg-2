<?php

class BSKPDFM_Dashboard_Settings_Featured_Image {
	
	private static $_bsk_pdf_settings_page_url = '';
	   
	public function __construct() {
		
		self::$_bsk_pdf_settings_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['setting'] );
	}

	function show_settings( $plugin_settings ){
		$default_enable_featured_image = false;
		$default_thumbnail_id = 0;
		$register_image_size_1 = array();
		$register_image_size_2 = array();
		$default_thumbnail_size = 'thumbnail';
        $default_feated_image_size_dimission = BSKPDFM_Common_Backend::get_image_size_dimission( $default_thumbnail_size );
	?>
    <form action="<?php echo self::$_bsk_pdf_settings_page_url ?>" method="POST" id="bsk_pdfm_featured_image_settings_form_ID">
    <div class="bsk_pdf_manager_settings_featured_image_tab" style="width:80%;">
        <p>
        	<label><input type="checkbox" name="bsk_pdf_manager_enable_featured_image" id="bsk_pdf_manager_enable_featured_image_ID" value="1" <?php echo $default_enable_featured_image ? 'checked="checked"' : '' ?> /> Enable featured image</label>
        </p>
        <div id="bsk_pdf_manager_featured_image_settings_containder_ID" style="display:<?php echo $default_enable_featured_image ? 'block' : 'none'; ?>">
            <h4>PDF Default Featured Image</h4>
            <p>
                <div id="postimagediv" class="postbox" style="width:95%;" >
                    <div class="inside" style="width: <?php echo $default_feated_image_size_dimission['width']; ?>px;">
                    <?php
                    
                    $ajax_loader_img_url = BSKPDFManager::$_ajax_loader_img_url;
                    $default_pdf_icon_url = BSKPDFManager::$_default_pdf_icon_url;
                    ?>
                    <p class="hide-if-no-js">
                        <span id="bsk_pdf_manger_default_image_icon_container_ID" style="display:block"><img src="<?php echo $default_pdf_icon_url; ?>" /></span>
                        <a title="Set default featured image" href="javascript:void(0);" id="bsk_pdf_manager_set_default_featured_image_anchor_ID" >Change default featured image</a>
                    </p>
                    </div>
                </div>
            </p>
            <h4>Register Featured Image Size</h4>
            <?php
            $size_name = '';
            $size_width = '';
            $size_height = '';
            $size_crop_str = '';
            if( is_array($register_image_size_1) && count($register_image_size_1) > 0 ){
            $size_name = $register_image_size_1['name'];
            $size_width = $register_image_size_1['width'];
            $size_height = $register_image_size_1['height'];
            $size_crop_str = $register_image_size_1['crop'] ? ' checked="checked"' : '';
            }
            ?>
            <p>
                <span style="display:inline-bloc;">Name: <input type="text" name="bsk_pdf_manager_register_image_size_name_1" id="bsk_pdf_manager_register_image_size_name_1_ID" value="<?php echo $size_name; ?>" style="width:150px;" /> Width: <input type="number" name="bsk_pdf_manager_register_image_size_width_1" id="bsk_pdf_manager_register_image_size_width_1_ID" value="<?php echo $size_width; ?>" style="width:80px;" />px Height: <input type="number" name="bsk_pdf_manager_register_image_size_height_1"  id="bsk_pdf_manager_register_image_size_height_1_ID" value="<?php echo $size_height; ?>" style="width:80px;" />px
                </span>
                <span style="display:inline-block; margin-left:15px;"><label><input type="checkbox" name="bsk_pdf_manager_register_image_size_crop_1" id="bsk_pdf_manager_register_image_size_crop_1_ID" value="Yes"<?php echo $size_crop_str; ?> />Crop thumbnail to exact dimensions ?</label></span>
            </p>
            <?php
            $size_name = '';
            $size_width = '';
            $size_height = '';
            $size_crop_str = '';
            if( is_array($register_image_size_2) && count($register_image_size_2) > 0 ){
            $size_name = $register_image_size_2['name'];
            $size_width = $register_image_size_2['width'];
            $size_height = $register_image_size_2['height'];
            $size_crop_str = $register_image_size_2['crop'] ? ' checked="checked"' : '';
            }
            ?>
            <p>
                <span style="display:inline-bloc;">Name: <input type="text" name="bsk_pdf_manager_register_image_size_name_2" id="bsk_pdf_manager_register_image_size_name_2_ID" value="<?php echo $size_name; ?>" style="width:150px;" /> Width: <input type="number" name="bsk_pdf_manager_register_image_size_width_2" id="bsk_pdf_manager_register_image_size_width_2_ID" value="<?php echo $size_width; ?>" style="width:80px;" />px Height: <input type="number" name="bsk_pdf_manager_register_image_size_height_2" id="bsk_pdf_manager_register_image_size_height_2_ID" value="<?php echo $size_height; ?>" style="width:80px;" />px</span>
                <span style="display:inline-block; margin-left:15px;"><label><input type="checkbox" name="bsk_pdf_manager_register_image_size_crop_2" value="Yes"<?php echo $size_crop_str; ?> />Crop thumbnail to exact dimensions ?</label></span>
            </p>
            <h4>Default Featured Image Size</h4>
            <p>
                <select name="bsk_pdf_manager_default_thumbnail_size" id="bsk_pdf_manager_default_thumbnail_size_ID">
                <?php
                    $image_sizes = BSKPDFM_Common_Backend::get_image_sizes();
                    $hidden_dimission_str = '';
                    $hidden_registers_size_names_array = array();
                    $selected_width_str = '';
                    $selected_height_str = '';
                    $selected_crop_ste = 'Crop: No';
                    foreach ( $image_sizes as $size_name => $size_name_dimission )  {
                        if ( $size_name_dimission['width'] < 1 || 
                            $size_name_dimission['height'] < 1 || 
                            $size_name == 'bsk-pdf-dashboard-list-thumbnail' ){
                            continue;
                        }
                        $selected_str = '';
						$selected_crop_str = '';
                        if ( $default_thumbnail_size == $size_name ) {
                            $selected_str = 'selected="selected"';
                            $selected_width_str = 'Width: '.$size_name_dimission['width'].'px';
                            $selected_height_str = 'Height: '.$size_name_dimission['height'].'px';
                            $selected_crop_str = $size_name_dimission['crop'] ? 'Crop: Yes' : 'Crop: No';
                        }
                        echo '<option value="'.$size_name.'" '.$selected_str.'>'.$size_name.'</option>';
                        $crop = $size_name_dimission['crop'] ? 'Yes' : 'No';
                        $hidden_value = $size_name_dimission['width'].'_'.$size_name_dimission['height'].'_'.$crop;
                        $hidden_dimission_str .= '<input type="hidden" id="bsk_pdfm_size_dimission_'.$size_name.'_ID" value="'.$hidden_value.'" />';
                
                        //organise register sizes array, excude regsiter size by me
                        if( is_array($register_image_size_2) && count($register_image_size_2) > 0 ){
                            if( $size_name == $register_image_size_2['name'] ){
                                continue;
                            }
                        }
                        if( is_array($register_image_size_1) && count($register_image_size_1) > 0 ){
                            if( $size_name == $register_image_size_1['name'] ){
                                continue;
                            }
                        }
                        $hidden_registers_size_names_array[] = $size_name;
                    }
                
                    if( $default_thumbnail_size == 'full' ){
                        echo '<option value="full" selected="selected">full</option>';
                    }else{
                        echo '<option value="full">full</option>';
                    }
                ?>
                </select>
                <span style="display:inline-block; margin-left:20px;" id="bsk_pdf_manager_dft_size_width_span_ID"><?php echo $selected_width_str; ?></span>
                <span style="display:inline-block; margin-left:20px;" id="bsk_pdf_manager_dft_size_height_span_ID"><?php echo $selected_height_str; ?></span>
                <span style="display:inline-block; margin-left:20px;" id="bsk_pdf_manager_dft_size_crop_span_ID"><?php echo $selected_crop_str; ?></span>
                <?php echo $hidden_dimission_str; ?>
                <input type="hidden" id="bsk_pdf_manager_registered_size_names_id" value="<?php echo implode(',', $hidden_registers_size_names_array); ?>" />
            </p>
        </div>
        <p style="margin-top:20px;">
        	<input type="button" id="bsk_pdf_manager_settings_featured_image_tab_save_form_ID" class="button-primary" value="Save Featured Image Settings"  disabled />
            <input type="hidden" name="bsk_pdf_manager_action" value="featured_image_settings_save" />
        </p>
        <?php echo wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_pdf_manager_settings_featured_image_tab_save_oper_nonce', true, false ); ?>
    </div>
    </form>
    <?php
	} //end of function
}