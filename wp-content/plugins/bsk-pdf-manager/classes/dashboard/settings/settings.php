<?php

class BSKPDFM_Dashboard_Settings {
	
	private static $_bsk_pdf_settings_page_url = '';
    
    private static $_bsk_pdfm_OBJ_settings_general = NULL;
	private static $_bsk_pdfm_OBJ_settings_featured_image = NULL;
	private static $_bsk_pdfm_OBJ_settings_styles = NULL;
	   
	public function __construct() {
		require_once( 'settings-general.php' );
		require_once( 'settings-featured-image.php' );
		require_once( 'settings-styles.php' );	
		
		self::$_bsk_pdf_settings_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['setting'] );
        
        self::$_bsk_pdfm_OBJ_settings_general = new BSKPDFM_Dashboard_Settings_General();
        self::$_bsk_pdfm_OBJ_settings_featured_image = new BSKPDFM_Dashboard_Settings_Featured_Image();
        self::$_bsk_pdfm_OBJ_settings_styles = new BSKPDFM_Dashboard_Settings_Styles();
	}
	
	function show_settings(){
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		?>
        <div class="wrap">
            <div style="width: 70%; float: left;">
                <div class="wrap">
                    <h2 class="nav-tab-wrapper">
                        <a class="nav-tab nav-tab-active" href="javascript:void(0);" id="bsk_pdfm_setings_tab-general-settings">General settings</a>
                        <a class="nav-tab" href="javascript:void(0);" id="bsk_pdfm_setings_tab-featured-image">Featured Image</a>
                        <a class="nav-tab" href="javascript:void(0);" id="bsk_pdfm_setings_tab-styles">Styles</a>
                    </h2>
                    <div id="bsk_pdfm_setings_tab_content_wrap_ID">
                        <section><?php self::$_bsk_pdfm_OBJ_settings_general->show_settings( $plugin_settings ); ?></section>
                        <section><?php self::$_bsk_pdfm_OBJ_settings_featured_image->show_settings( $plugin_settings ); ?></section>
                        <section><?php self::$_bsk_pdfm_OBJ_settings_styles->show_settings( $plugin_settings ); ?></section>
                    </div>
                </div>
            <?php
            $target_tab = isset($_REQUEST['target']) ? $_REQUEST['target'] : '';
            echo '<input type="hidden" id="bsk_pdfm_settings_target_tab_ID" value="'.$target_tab.'" />';
            $ajax_nonce = wp_create_nonce( 'bsk_pdf_manager_settings_page_ajax-oper-nonce' );
            echo '<input type="hidden" id="bsk_pdf_manager_settings_page_ajax_nonce_ID" value="'.$ajax_nonce.'" />';
            ?>
            </div>
            <div style="width: 28%; float: left;">
                <div class="wrap" id="bsk_pdfm_help_other_product_wrap_ID">
                    <h2>&nbsp;</h2>
                    <div>
                        <?php BSKPDFM_Dashboard_Ads::show_other_plugin_of_cf7_black_list(); ?>
                    </div>
                    <div style="margin-top: 20px;">
                        <?php BSKPDFM_Dashboard_Ads::show_other_plugin_of_gravity_forms_black_list(); ?>
                    </div>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
    <?php
	}
    
}