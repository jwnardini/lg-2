<?php

/*
Plugin Name: BSK PDF Manager
Plugin URI: http://www.bannersky.com/bsk-pdf-manager-pro/
Description: Help you manage your PDF documents. PDF documents can be filter by category. Support short code to show special PDF documents or all PDF documents under  category. Widget supported.
Version: 2.2.1
Author: BannerSky.com
Author URI: http://www.bannersky.com/
*/
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class BSKPDFManager {
	
    private static $instance;
    public static $_cats_tbl_name = 'bsk_pdf_manager_cats';
	public static $_pdfs_tbl_name = 'bsk_pdf_manager_pdfs';
    public static $_rels_tbl_name = 'bsk_pdf_manager_relationships';
    
	public static $_PLUGIN_VERSION_ = '2.2.1';
	private static $_plugin_db_version = '2.2';
	private static $_plugin_saved_db_version_option = '_bsk_pdf_manager_db_ver_';
    private static $_plugin_db_rels_done_option = '_bsk_pdf_manager_rels_done_';
	public static $_upload_path = '';
	public static $_upload_folder = 'bsk-pdf-manager/';
	public static $_upload_folder_4_ftp = 'bsk-pdf-manager/ftp/';
    public static $_upload_url = '';
    public static $_custom_upload_folder = '';
	public static $_custom_upload_folder_path = '';
    
	private static $_plugin_admin_notice_message = array();

	public static $_plugin_settings_option = '_bsk_pdf_manager_pro_settings_';
	public static $_plugin_temp_option_prefix = '_bsk_pdf_manager_pro_temp_';

    public static $_default_pdf_icon_url = '';
	public static $_ajax_loader_img_url = '';
    public static $_delete_cat_icon_url = '';
    
    public static $url_to_upgrade = 'https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/how-to-upgrade-to-pro-version/';
    
    public static $_category_max_depth = 3;
	
	//objects
	public $_bsk_pdfm_pro_OBJ_dashboard = NULL;
	public $_bsk_pdfm_pro_OBJ_front_pdfs_deprecated = NULL;
    public $_bsk_pdfm_pro_OBJ_front_pdfs = NULL;
	public $_bsk_pdfm_pro_OBJ_front_category_deprecated = NULL;
    public $_bsk_pdfm_pro_OBJ_front_category = NULL;
	public $_bsk_pdfm_pro_OBJ_front_selector_deprecated = NULL;
    public $_bsk_pdfm_pro_OBJ_front_selector = NULL;
	
	public static function instance() {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof BSKPDFManager ) ) {
			self::$instance = new BSKPDFManager;
            
            // Plugin Folder Path.
            if ( ! defined( 'BSK_PDFM_PLUGIN_DIR' ) ) {
                define( 'BSK_PDFM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            }
            // Plugin Folder URL.
            if ( ! defined( 'BSK_PDFM_PLUGIN_URL' ) ) {
                define( 'BSK_PDFM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            }
		  
            //default upload folder, path, url
            $uploads = wp_upload_dir();
            self::$_upload_path = $uploads['basedir'].'/';
            self::$_upload_url = $uploads['baseurl'].'/';
            self::$_ajax_loader_img_url = BSK_PDFM_PLUGIN_URL.'images/ajax-loader.gif';
            self::$_default_pdf_icon_url = BSK_PDFM_PLUGIN_URL.'images/default_PDF_icon.png';
            self::$_delete_cat_icon_url = BSK_PDFM_PLUGIN_URL.'images/delete-2.png';
            
            
            //read plugin setting to set custom upload folder
            $plugin_settings = get_option( self::$_plugin_settings_option, false );
            if( !$plugin_settings || !is_array($plugin_settings) || count($plugin_settings) < 1 ){
                $plugin_settings = array();
            }

            add_action( 'admin_notices', array(self::$instance, 'bsk_pdf_manager_admin_notice') );
            add_action( 'admin_enqueue_scripts', array(self::$instance, 'bsk_pdf_manager_enqueue_scripts_n_css') );
            add_action( 'wp_enqueue_scripts', array(self::$instance, 'bsk_pdf_manager_enqueue_scripts_n_css') );
                
            //include others class
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/common/backend.php' );
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/common/display.php' );
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/common/pagination.php' );
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/common/filter.php' );
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/common/search.php' );
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/common/data-source.php' );
            
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/dashboard/dashboard.php' );
            
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/shortcodes/pdfs/pdfs-deprecated.php' );
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/shortcodes/pdfs/pdfs.php' );
            
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/shortcodes/category/category.php' );
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/shortcodes/category/category-deprecated.php' );
            
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/widgets/widget.php' );
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/widgets/widget-category.php' );

            self::$instance->_bsk_pdfm_pro_OBJ_dashboard = new BSKPDFM_Dashboard();
            self::$instance->_bsk_pdfm_pro_OBJ_front_pdfs_deprecated = new BSKPDFM_Shortcodes_PDFs_Deprecated();
            self::$instance->_bsk_pdfm_pro_OBJ_front_pdfs = new BSKPDFM_Shortcodes_PDFs();
            self::$instance->_bsk_pdfm_pro_OBJ_front_category_deprecated = new BSKPDFM_Shortcodes_Category_Deprecated();
            self::$instance->_bsk_pdfm_pro_OBJ_front_category = new BSKPDFM_Shortcodes_Category();

            //hooks
            register_activation_hook(__FILE__, array(self::$instance, 'bsk_pdf_manager_activate') );
            register_deactivation_hook( __FILE__, array(self::$instance, 'bsk_pdf_manager_deactivate') );
            register_uninstall_hook( __FILE__, 'BSKPDFManager::bsk_pdf_manager_uninstall' );

            add_action( 'widgets_init', array(self::$instance, 'bsk_pdf_manager_pro_register_widgets'));

            add_action( 'init', array(self::$instance, 'bsk_pdf_manager_post_action') );

            self::$instance->bsk_pdf_create_upload_folder_and_set_secure();

            add_action( 'plugins_loaded', array(self::$instance, 'bsk_pdf_manager_update_database'), 10 );
            add_action( 'plugins_loaded', array(self::$instance, 'bsk_pdf_manager_build_relationships'), 12 );
        }
        
		return self::$instance;
	}
    
    private function __construct() {
        //
    }
    
    public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__,  'Cheatin&#8217;', '1.0' );
	}
    
    public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__,  'Cheatin&#8217;', '1.0' );
	}
    
	function bsk_pdf_manager_activate( $network_wide ){
		//create or update table
        self::$instance->bsk_pdf_manager_pro_create_table();
	}
	
	function bsk_pdf_manager_deactivate(){
        
	}
	
	function bsk_pdf_manager_pro_remove_tables_n_options(){
		global $wpdb;
		
		delete_option( '_bsk_pdf_manager_open_target' );
		delete_option( '_bsk_pdf_manager_category_list_has_title' );
		delete_option( '_bsk_pdf_manager_pdf_order_by_' );
		delete_option( '_bsk_pdf_manager_pdf_order_' );
		delete_option( '_bsk_pdf_manager_db_ver_');
		delete_option( '_bsk_pdf_manager_rels_done_');
		delete_option( '_bsk_pdf_manager_free_to_pro_done_');
		
        $table_cats = $wpdb->prefix."bsk_pdf_manager_cats";
		$table_pdfs = $wpdb->prefix."bsk_pdf_manager_pdfs";
        $table_rels = $wpdb->prefix."bsk_pdf_manager_relationships";
		
		$wpdb->query("DROP TABLE IF EXISTS $table_cats");
		$wpdb->query("DROP TABLE IF EXISTS $table_pdfs");
        $wpdb->query("DROP TABLE IF EXISTS $table_rels");
		
		delete_option( '_bsk_pdf_manager_pro_settings_' );
		$sql = 'DELETE FROM `'.$wpdb->options.'` WHERE `option_name` LIKE "_bsk_pdf_manager_pro_temp_%"';
		$wpdb->query( $sql );
	}
	
	function bsk_pdf_manager_uninstall(){
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $has_active_pro_verison = false;
        $plugins = get_plugins();
        foreach( $plugins as $plugin_key => $data ){
            if( 'bsk-pdf-manager-pro/bsk-pdf-manager-pro.php' == $plugin_key && 
                is_plugin_active( $plugin_key ) ){
                $has_active_pro_verison = true;
                break;
            }
        }
        if( $has_active_pro_verison == true ){
            return;
        }
        
		//create or update table
        self::bsk_pdf_manager_pro_remove_tables_n_options();
	}
    
    function bsk_pdf_manager_pro_register_widgets(){
        register_widget( "BSKPDFManagerWidget" );
        register_widget( "BSKPDFManagerWidget_Category" );
    }
	
	function bsk_pdf_manager_enqueue_scripts_n_css(){
		global $wp_version;
		
		wp_enqueue_script('jquery');
		if( is_admin() ){
			if( function_exists( 'wp_enqueue_media' ) && version_compare( $wp_version, '3.5', '>=' ) ) {
				wp_enqueue_media();
			}
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_style( 'jquery-ui', 'https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css' );
			wp_enqueue_script( 'jstree', 
                                          BSK_PDFM_PLUGIN_URL.'classes/dashboard/settings/jstree/dist/jstree.min.js', 
                                          array('jquery'), 
                                          filemtime(BSK_PDFM_PLUGIN_DIR.'classes/dashboard/settings/jstree/dist/jstree.min.js') );
            wp_enqueue_script( 'dateformat', 
                                          BSK_PDFM_PLUGIN_URL.'js/date.format.js', 
                                          array('jquery'), 
                                          filemtime(BSK_PDFM_PLUGIN_DIR.'js/date.format.js') );
			wp_enqueue_script( 'bsk-pdfm-pro-admin', 
                                          BSK_PDFM_PLUGIN_URL.'js/bsk_pdfm_pro_admin.js', 
                                          array('jquery', 'jquery-ui-datepicker', 'jstree', 'dateformat'), 
                                          filemtime(BSK_PDFM_PLUGIN_DIR.'js/bsk_pdfm_pro_admin.js') );
            $supported_extension_and_mime_type = BSKPDFM_Common_Backend::get_supported_extension_with_mime_type();
            wp_localize_script( 'bsk-pdfm-pro-admin', 
                                        'bsk_pdfm_admin', 
                                        array('extension_and_mime' => $supported_extension_and_mime_type ) );
            
            wp_enqueue_style( 'jstree', 
                                        BSK_PDFM_PLUGIN_URL.'classes/dashboard/settings/jstree/dist/themes/default/style.min.css', 
                                        array(), 
                                        filemtime(BSK_PDFM_PLUGIN_DIR.'classes/dashboard/settings/jstree/dist/themes/default/style.min.css') );
			wp_enqueue_style( 'bsk-pdf-manager-pro-admin', 
                                        BSK_PDFM_PLUGIN_URL.'css/bsk-pdf-manager-pro-admin.css', 
                                        array('jstree'), 
                                        filemtime(BSK_PDFM_PLUGIN_DIR.'css/bsk-pdf-manager-pro-admin.css') );
		}else{
            $default_styles_version = '2.0';
            $plugin_settings = get_option( self::$_plugin_settings_option, '' );
            if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
                if( isset($plugin_settings['default_styles_version']) ){
                    $default_styles_version = $plugin_settings['default_styles_version'];
                }
            }
            
            wp_enqueue_style( 'bsk-pdf-manager-pro-deprecated-css', 
                                        BSK_PDFM_PLUGIN_URL.'css/bsk-pdf-manager-pro-deprecated.css', 
                                        array(), 
                                        filemtime(BSK_PDFM_PLUGIN_DIR.'css/bsk-pdf-manager-pro-deprecated.css') );
            
            if( $default_styles_version != '2.0' ){
                wp_enqueue_style( 'bsk-pdf-manager-pro-css', 
                                        BSK_PDFM_PLUGIN_URL.'css/bsk-pdf-manager-pro-v_'.str_replace('.', '_', $default_styles_version).'.css', 
                                        array(), 
                                        filemtime(BSK_PDFM_PLUGIN_DIR.'css/bsk-pdf-manager-pro-v_'.str_replace('.', '_', $default_styles_version).'.css') );
            }else{
                wp_enqueue_style( 'bsk-pdf-manager-pro-css', 
                                        BSK_PDFM_PLUGIN_URL.'css/bsk-pdf-manager-pro.css', 
                                        array(), 
                                        filemtime(BSK_PDFM_PLUGIN_DIR.'css/bsk-pdf-manager-pro.css') );
            }

			wp_enqueue_script( 'bsk-pdf-manager-pro-deprecated', 
                                          BSK_PDFM_PLUGIN_URL.'js/bsk_pdf_manager_pro_deprecated.js', 
                                          array('jquery'), 
                                          filemtime(BSK_PDFM_PLUGIN_DIR.'js/bsk_pdf_manager_pro_deprecated.js') );
            wp_localize_script( 'bsk-pdf-manager-pro-deprecated', 'bsk_pdf_pro', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
            
            wp_enqueue_script( 'bsk-pdf-manager-pro', 
                                          BSK_PDFM_PLUGIN_URL.'js/bsk_pdf_manager_pro.js', 
                                          array('jquery'), 
                                          filemtime(BSK_PDFM_PLUGIN_DIR.'js/bsk_pdf_manager_pro.js') );
			
            wp_localize_script( 'bsk-pdf-manager-pro', 'bsk_pdf_pro', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		}
	}
	
	function bsk_pdf_manager_admin_notice(){
		$warning_message = array();
		$error_message = array();
		
		//admin message
		if (count(self::$_plugin_admin_notice_message) > 0){
			foreach(self::$_plugin_admin_notice_message as $msg){
				if($msg['type'] == 'ERROR'){
					$error_message[] = $msg['message'];
				}
				if($msg['type'] == 'WARNING'){
					$warning_message[] = $msg['message'];
				}
			}
		}
		
		//show error message
		if(count($warning_message) > 0){
			echo '<div class="update-nag">';
			foreach($warning_message as $msg_to_show){
				echo '<p>'.$msg_to_show.'</p>';
			}
			echo '</div>';
		}
		
		//show error message
		if(count($error_message) > 0){
			echo '<div class="error">';
			foreach($error_message as $msg_to_show){
				echo '<p>'.$msg_to_show.'</p>';
			}
			echo '</div>';
		}
	}

	function bsk_pdf_manager_pro_create_table(){
		global $wpdb;
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$charset_collate = $wpdb->get_charset_collate();
		
		$table_name = $wpdb->prefix . self::$_cats_tbl_name;
		$sql = "CREATE TABLE $table_name (
                                                              `id` int(11) NOT NULL AUTO_INCREMENT,
                                                              `parent` int(11) DEFAULT 0,
                                                              `title` varchar(512) NOT NULL,
                                                              `description` varchar(512) DEFAULT NULL,
                                                              `password` varchar(32) DEFAULT NULL,
                                                              `empty_message` varchar(512) DEFAULT NULL,
                                                              `last_date` datetime DEFAULT NULL,
                                                              UNIQUE KEY id (id)
                                                            ) $charset_collate;";
        dbDelta( $sql );
		
		$table_name = $wpdb->prefix . self::$_pdfs_tbl_name;
		$sql = "CREATE TABLE $table_name (
                                                              `id` int(11) NOT NULL AUTO_INCREMENT,
                                                              `cat_id` varchar(32) NOT NULL,
                                                              `order_num` int(11) DEFAULT NULL,
                                                              `thumbnail_id` int(11) DEFAULT NULL,
                                                              `title` varchar(512) DEFAULT NULL,
                                                              `file_name` varchar(512) NOT NULL,
                                                              `description` varchar(512) DEFAULT NULL,
                                                              `by_media_uploader` int(11) DEFAULT 0,
                                                              `last_date` datetime DEFAULT NULL,
                                                              `weekday` varchar(8) DEFAULT NULL,
                                                              `download_count` int(11) DEFAULT 0,
                                                              `publish_date` datetime DEFAULT NULL,
                                                              `expiry_date` datetime DEFAULT NULL,
                                                              UNIQUE KEY id (id)
                                                            ) $charset_collate;";
		dbDelta($sql);
        
        $table_name = $wpdb->prefix . self::$_rels_tbl_name;
		$sql = "CREATE TABLE $table_name (
                                                              `id` int(11) NOT NULL AUTO_INCREMENT,
                                                              `pdf_id` int(11) NOT NULL,
                                                              `cat_id` int(11) NOT NULL,
                                                              UNIQUE KEY id (id)
                                                            ) $charset_collate;";
		dbDelta($sql);
		
		update_option( self::$_plugin_saved_db_version_option, self::$_plugin_db_version );
        //for new install, doesn't need to build relationships
        update_option( self::$_plugin_db_rels_done_option, 'YES' );
	}
	
	function bsk_pdf_manager_update_database(){
		
		$db_version = get_option( self::$_plugin_saved_db_version_option );
		if ( version_compare( $db_version, self::$_plugin_db_version, '>=' ) ) {
			return;
		}
		
		global $wpdb;
					
        //upgrade db version to 2.0
		if ( version_compare( $db_version, '2.0', '<' ) ) {
            $table_name = $wpdb->prefix . self::$_pdfs_tbl_name;

            //add new field
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `by_media_uploader` INT(11) NULL DEFAULT \'0\' AFTER `file_name`';
            $wpdb->query( $sql );
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `publish_date`  DATETIME NULL AFTER `last_date`';
            $wpdb->query( $sql );
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `expiry_date`  DATETIME NULL AFTER `publish_date`';
            $wpdb->query( $sql );

            //add new field
            $table_name = $wpdb->prefix . self::$_cats_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `password` VARCHAR(32) NULL , ADD `empty_message` VARCHAR(512) NULL;';
            $wpdb->query( $sql );

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $wpdb->prefix . self::$_rels_tbl_name;
            $sql = "CREATE TABLE $table_name (
                                                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                                                  `pdf_id` int(11) NOT NULL,
                                                                  `cat_id` int(11) NOT NULL,
                                                                  UNIQUE KEY id (id)
                                                                ) $charset_collate;";
            dbDelta($sql);

            $upload_folder_prefix = 'wp-content/uploads/';
            if ( function_exists('is_multisite') && is_multisite() ){
                $uploads = wp_upload_dir();
                $base_dir_path = $uploads['basedir'];
                $upload_folder_prefix = str_replace( ABSPATH, '', $base_dir_path ).'/';
            }

            //update pdf table
            $table_name = $wpdb->prefix . self::$_pdfs_tbl_name;
            $sql = 'UPDATE `'.$table_name.'` SET `file_name` = CONCAT("'.$upload_folder_prefix.self::$_upload_folder.'", `file_name`) '.
                     'WHERE `by_media_uploader` < 1';
            $wpdb->query( $sql );
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `description` VARCHAR(512) NULL AFTER `file_name`;';
            $wpdb->query( $sql );

            //add new field to cat table
            $table_name = $wpdb->prefix . self::$_cats_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `description` VARCHAR(512) NULL AFTER `cat_title`;';
            $wpdb->query( $sql );

            $sql = 'ALTER TABLE `'.$table_name.'` ADD `parent` int(11) NOT NULL DEFAULT \'0\'  AFTER `id`;';
            $wpdb->query( $sql );

            $sql = 'ALTER TABLE `'.$table_name.'` CHANGE `cat_title` `title` VARCHAR(512) NOT NULL;';
            $wpdb->query( $sql );
        }
        
            $table_name = $wpdb->prefix . self::$_pdfs_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `download_count` INT DEFAULT 0 AFTER `weekday`;';
            $wpdb->query( $sql );
        
        //upgrade to 2.2
		update_option( self::$_plugin_saved_db_version_option, self::$_plugin_db_version );
	}
    
    function bsk_pdf_manager_build_relationships(){
        
        if( get_option( self::$_plugin_db_rels_done_option ) == 'YES' ){
            return;
        }
        
        set_time_limit( 0 );

        global $wpdb;
        
        //query if under building
        $sql = 'SELECT COUNT(*) FROM `'.$wpdb->prefix . self::$_rels_tbl_name.'` WHERE 1';
        if( $wpdb->get_var( $sql ) ){
            return;
        }

        $sql = 'SELECT * FROM `'.$wpdb->prefix . self::$_pdfs_tbl_name.'` WHERE `cat_id` != "999999"';
        $results = $wpdb->get_results( $sql );
        if( !$results || !is_array($results) || count($results) < 1 ){
            update_option( self::$_plugin_db_rels_done_option, 'YES' );
            return;
        }

        foreach( $results as $pdf_obj ){
            if( $pdf_obj->cat_id < 1 ){
                continue;
            }
            set_time_limit( 0 );
            
            //query if exist
            $sql = 'SELECT COUNT(*) FROM `'.$wpdb->prefix . self::$_rels_tbl_name.'` WHERE `pdf_id` = '.$pdf_obj->id.' AND `cat_id` = '.$cat_id_int;
            if( $wpdb->get_var( $sql ) ){
                continue;
            }

            $wpdb->insert( $wpdb->prefix . self::$_rels_tbl_name, 
                                   array( 'pdf_id' => $pdf_obj->id, 'cat_id' => $pdf_obj->cat_id ), 
                                   array( '%d', '%d') );
            
            $wpdb->update( $wpdb->prefix . self::$_pdfs_tbl_name, array('cat_id' => "999999"), array('id' => $pdf_obj->id) );
        }
        
        update_option( self::$_plugin_db_rels_done_option, 'YES' );
    }
    

	function bsk_pdf_manager_post_action(){
		if( isset( $_POST['bsk_pdf_manager_action'] ) && strlen($_POST['bsk_pdf_manager_action']) > 0 ) {
			do_action( 'bsk_pdf_manager_' . $_POST['bsk_pdf_manager_action'], $_POST );
		}
	}
	
	function bsk_pdf_create_upload_folder_and_set_secure(){
		//create folder to upload 
		$_upload_folder_path = self::$_upload_path.self::$_upload_folder;
		if ( !is_dir($_upload_folder_path) ) {
			if ( !wp_mkdir_p( $_upload_folder_path ) ) {
				self::$_plugin_admin_notice_message['upload_folder_missing']  = array( 'message' => 'Directory <strong>' . $_upload_folder_path . '</strong> can not be created. Please create it first yourself.',
				                                                                                	'type' => 'ERROR');
			}
		}
		
		if ( !is_writeable( $_upload_folder_path ) ) {
			$msg  = 'Directory <strong>' . $_upload_folder_path . '</strong> is not writeable ! ';
			$msg .= 'Check <a href="http://codex.wordpress.org/Changing_File_Permissions">http://codex.wordpress.org/Changing_File_Permissions</a> for how to set the permission.';

			self::$_plugin_admin_notice_message['upload_folder_not_writeable']  = array( 'message' => $msg,
			                                                                                                                 'type' => 'ERROR');
		}

		//copy file to upload foloder
		if( !file_exists($_upload_folder_path.'/index.php') ){
			copy( dirname(__FILE__).'/assets/index.php', $_upload_folder_path.'/index.php' );
		}
	}
    
}//end of class

BSKPDFManager::instance();
