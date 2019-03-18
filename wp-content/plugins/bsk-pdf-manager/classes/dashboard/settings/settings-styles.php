<?php

class BSKPDFM_Dashboard_Settings_Styles {
	
	private static $_bsk_pdf_settings_page_url = '';
	   
	public function __construct() {
		global $wpdb;
		
		self::$_bsk_pdf_settings_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['setting'] );
		
		add_action( 'bsk_pdf_manager_styles_settings_save', array($this, 'bsk_pdf_manager_settings_styles_tab_save_fun') );
	}
	
	
	function show_settings( $plugin_settings ){

		$default_styles_version = '2.0';
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
			if( isset($plugin_settings['default_styles_version']) ){
				$default_styles_version = $plugin_settings['default_styles_version'];
			}
		}
        
        $styles_version = array( '2.0' => 'Version 2.0', '1.0' => 'Version 1.0' );
	?>
    <form action="<?php echo self::$_bsk_pdf_settings_page_url; ?>" method="POST" id="bsk_pdfm_styles_form_ID">
    <div class="bsk_pdf_manager_settings">
    	<h3>Default styles version</h3>
        <p>
            <select name="bsk_pdfm_settings_default_style_version" style="width: 250px;">
                <?php
                foreach( $styles_version as $ver => $label ){
                    $selected = $ver == $default_styles_version ? ' selected' : '';
                    echo '<option value="'.$ver.'"'.$selected.'>'.$label.'</option>';
                }
                ?>
            </select>
        </p>
        <p style="margin-top:20px;">
        	<input type="button" id="bsk_pdf_manager_settings_styles_save_form_ID" class="button-primary" value="Save Style Settings" />
            <input type="hidden" name="bsk_pdf_manager_action" value="styles_settings_save" />
        </p>
        <?php echo wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_pdf_manager_settings_styles_tab_save_oper_nonce', true, false ); ?>
    </div>
    </form>
    <?php
	}
	
	function bsk_pdf_manager_settings_styles_tab_save_fun( $data ) {
		global $wpdb, $current_user;
		//check nonce field
		if( !wp_verify_nonce( $data['bsk_pdf_manager_settings_styles_tab_save_oper_nonce'], plugin_basename( __FILE__ ) ) ){
			return;
		}
		
		if( !current_user_can('moderate_comments') ){
			return;
		}
		
		$default_styles_version = $data['bsk_pdfm_settings_default_style_version'];
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) || count($plugin_settings) < 1 ){
			$plugin_settings = array();
		}
		$plugin_settings['default_styles_version'] = $default_styles_version;

		update_option( BSKPDFManager::$_plugin_settings_option, $plugin_settings );
		$redirect_url = add_query_arg( 'target', 'styles', self::$_bsk_pdf_settings_page_url );
		wp_redirect( $redirect_url );
	}
    
}