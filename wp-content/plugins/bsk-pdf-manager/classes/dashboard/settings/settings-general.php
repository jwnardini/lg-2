<?php

class BSKPDFM_Dashboard_Settings_General {
	
	private static $_bsk_pdf_settings_page_url = '';
	   
	public function __construct() {
		global $wpdb;
		
		self::$_bsk_pdf_settings_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['setting'] );
		
		add_action( 'bsk_pdf_manager_general_settings_save', array($this, 'bsk_pdf_manager_settings_general_settings_tab_save_fun') );
	}
	
	
	function show_settings( $plugin_settings ){
        //scan folders
        $default_upload_path = BSKPDFManager::$_upload_path.BSKPDFManager::$_upload_folder;
        $custom_upload_path = BSKPDFManager::$_custom_upload_folder_path;
        $current_upload_path = $custom_upload_path ? $custom_upload_path : $default_upload_path; 
        
        //scan all subfolder to granise directory tree
        //for not superadmin user, only can set on /wp-content/uploads/sites/{blog_id}/
        $root_path_to_scan = ABSPATH;
        if( is_multisite() && !is_super_admin() ){
            $root_path_to_scan = BSKPDFManager::$_upload_path;
        }
        $site_directory_structure = $this->bsk_pdfm_scan_all_subfolders( $root_path_to_scan, $default_upload_path, $custom_upload_path);
		$author_access_pdf_category = false;
        $editor_access_all = false;
        $organise_directory_strucutre_with_year_month = true;
        $supported_extension = false;
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
			if( isset($plugin_settings['author_access_pdf_category']) ){
				$author_access_pdf_category = $plugin_settings['author_access_pdf_category'];
			}
            if( isset($plugin_settings['editor_access_all']) ){
				$editor_access_all = $plugin_settings['editor_access_all'];
			}
            if( isset($plugin_settings['directory_with_year_month']) ){
                $organise_directory_strucutre_with_year_month = $plugin_settings['directory_with_year_month'];
			}
            if( isset($plugin_settings['supported_extension']) ){
                $supported_extension = $plugin_settings['supported_extension'];
			}
		}
		
		$author_access_pdf_category_checked = $author_access_pdf_category ? ' checked="checked"' : '';
        $editor_access_all_checked = $editor_access_all ? ' checked="checked"' : '';
	?>
    <form action="<?php echo self::$_bsk_pdf_settings_page_url; ?>" method="POST" id="bsk_pdfm_general_settings_form_ID">
    <div class="bsk_pdf_manager_settings">
        <h3>Supported Documents Extension</h3>
        <p>Even the plugin is called "BSK PDF Manager" but it's not only support PDF documents, you may check the following extension to support them</p>
        <?php
        $all_extension = BSKPDFM_Common_Backend::get_available_extension_with_mime_type();
        ?>
        <p class="bsk-pdfm-supported-extension">
            <?php
            if( !$supported_extension || !is_array($supported_extension) || !in_array( 'pdf', $supported_extension ) ){
                $supported_extension = array( 'pdf' );
            }
            foreach( $all_extension as $extension => $mime_type_array ){
                $checked_str = is_array($supported_extension) && in_array($extension, $supported_extension) ? ' checked="checked"' : '';
                $disabled_str = $extension == 'pdf' ? ' disabled="disabled"' : '';
            ?>
            <label>
                <input type="checkbox" name="bsk_pdf_supported_extension[]" value="<?php echo $extension; ?>"<?php echo $checked_str.$disabled_str; ?>/><?php echo $extension; ?>
            </label>
            <?php
            }
            ?>
        </p>
        <p><span style="font-weight: bold;font-size: 1.2em;color: #ff5b00;">***</span>You should ensure no infected file upload to your server as the plugin only check file extension. </p>
        <hr />
        <h3>Access</h3>
    	<p>
        	<label>
            	<input type="checkbox" name="bsk_pdfm_general_setting_allow_author_access_pdf_category" value="1" <?php echo $author_access_pdf_category_checked; ?> disabled /> Allows a user with Author capabilities to access Categories & PDF Documents menu
            </label>
        </p>
        <?php
        $current_user_can_edit = '';
        if( !current_user_can('manage_options') ){
            $current_user_can_edit = ' disabled';
        }
        ?>
        <p>
        	<label>
            	<input type="checkbox" name="bsk_pdfm_general_setting_allow_editor_access_all" value="1" <?php echo $editor_access_all_checked.$current_user_can_edit; ?> disabled /> Allows a user with Editor capabilities to access all menu
            </label>
        </p>
        <p><i>By default, only a user with Administrator role can access all menus of BSK PDF Manager. With above settings you may allow Author users to manage category &amp; PDF without other settings visible. And allow Editor users access all menus.</i> </p>
        <hr />
        <h3 style="margin-top: 40px;">Upload</h3>
        <p>
            <label>Current upload folder: </label>
            <span style="font-size: 14px; font-weight: bold;"><?php echo str_replace( ABSPATH, '', $current_upload_path ); ?></span>
        </p>
        <?php
        $checked_str = $organise_directory_strucutre_with_year_month ? ' checked="checked"' : '';
        $hint_display = $organise_directory_strucutre_with_year_month ? 'none' : 'block';
        ?>
        <p>
            <label>
                <input type="checkbox" name="bsk_pdfm_organise_by_month_year" id="bsk_pdfm_organise_by_month_year_ID" value="Yes" <?php echo $checked_str; ?> disabled /> Organise directory structure with year / month
            </label>
        </p>
        <p id="bsk_pdfm_organise_by_month_year_hint_text_ID" style="display: <?php echo $hint_display; ?>;">
            <span style="display: block; font-style: italic;">To prevents your files from taxing the server's resources and negatively affect its load time. It's better to limit your directories to no more than 1,024 files/inodes</span>
        </p>
        <p style="margin-top:  20px;">
            <label>
            	<input type="checkbox" name="bsk_pdfm_set_upload_folder" id="bsk_pdfm_set_upload_folder_ID" value="Yes"  disabled /> Change upload folder to: 
            </label>
        </p>
        <p id="bsk_pdfm_set_upload_folder_input_ID">
            <span style="font-size: 14px; font-weight: bold; color: #dedddd; " id="bsk_pdfm_set_upload_folder_path_ID">
                    <?php echo str_replace( ABSPATH, '', $current_upload_path ); ?>
            </span>
            <input type="text" name="bsk_pdfm_set_upload_folder_sub" id="bsk_pdfm_set_upload_folder_sub_ID" value="" placeholder="create sub folder if not blank" style="width: 200px;" disabled />
            <input type="hidden" name="bsk_pdfm_set_upload_folder_path_val" id="bsk_pdfm_set_upload_folder_path_val_ID" value="<?php echo str_replace( ABSPATH, '', $current_upload_path ); ?>" placeholder="create sub folder if not blank" style="width: 200px;" disabled />
        </p>
        <p id="bsk_pdfm_set_upload_folder_hint_text_ID">
            <span style="display: block; font-style: italic;">Select destination path in the below diretory tree</span>
            <?php if( is_multisite() && !is_super_admin() ){ ?>
            <span style="display: block; font-style: italic;"><span style="font-weight: bold;font-size: 1.2em;color: #ff5b00;">*</span>Only Super Admin can visit full directory structure</span>
            <?php } ?>
            <span style="display: block; font-style: italic;"><span style="font-weight: bold;font-size: 1.2em;color: #ff5b00;">*</span>Removing previous upload folder may cause PDFs link broken</span>
        </p>
        <div id="bsk_pdf_upload_folder_tree" style="overflow:auto; border:1px solid silver; min-height:100px;">
            <ul>
                <li data-jstree='{ "opened" : true }' relative_path="<?php echo DIRECTORY_SEPARATOR; ?>"><?php echo DIRECTORY_SEPARATOR; ?>
                    <ul>
                        <?php $this->bsk_pdfm_display_all_subfolders( $site_directory_structure, $default_upload_path, $custom_upload_path ); ?>
                    </ul>
                </li>
            </ul>
        </div>
        <?php
        if( is_multisite() && !is_super_admin() ){
            $root_path_to_scan = BSKPDFManager::$_upload_path;
            $label_to_set = str_replace( ABSPATH, '', $root_path_to_scan );
            $relative_path_to_set = str_replace( ABSPATH, '', $root_path_to_scan );
            
            $this->bsk_pdfm_rename_jstree_root_node_label( $label_to_set, $relative_path_to_set );
        }
        ?>
        <hr />
        <h3 style="margin-top:40px;">Statistics Setting</h3>
        <?php $download_count_label = ' ( Downloads: #COUNT# )'; ?>
        <p>
            <label>
            	<input type="checkbox" name="bsk_pdf_manager_statistics_enable" id="bsk_pdf_manager_statistics_enable_ID" value="Yes" disabled /> Enable Download Statistics
            </label>
        </p>
        <p id="bsk_pdf_manager_download_count_label_block_ID">
            <label>
            	<input type="checkbox" name="bsk_pdf_manager_download_count_front_enable" id="bsk_pdf_manager_download_count_front_enable_ID" value="Yes" disabled /> Display download count after title in front
            </label>
            <span id="bsk_pdf_manager_download_count_label_in_front_ID" style="display: block; margin-top: 10px;">
                <label style="display: inline-block; width: 150px;">Download count text: </label>
                <input type="text" name="bsk_pdf_manager_download_count_label" id="bsk_pdf_manager_download_count_label_ID" value="<?php echo $download_count_label; ?>" placeholder="<?php echo $download_count_label; ?>" style="width: 25%;" disabled />
                <span style="font-style: italic;">#COUNT# must be kept in text to show count</span>
            </span>
        </p>
        <hr />
        <?php
        $credit_checked = '';
        $link_text_display = 'none';
        $credit_text = 'PDFs powered by PDF Manager Pro';
        if( isset( $plugin_settings['enable_credit'] ) && $plugin_settings['enable_credit'] == 'Yes' ){
            $credit_checked = ' checked="checked"';
            $link_text_display = 'block';
            if( isset( $plugin_settings['credit_text'] ) && $plugin_settings['credit_text'] ){
                $credit_text = $plugin_settings['credit_text'];
            }
        }
        ?>
        <h3 style="margin-top:40px;">Credit Setting</h3>
        <p>
            <label>
            	<input type="checkbox" name="bsk_pdf_manaer_credit_link_enable" id="bsk_pdf_manaer_credit_link_enable_ID" value="Yes"<?php echo $credit_checked ?>/> Give us credit
            </label>
        </p>
        <p id="bsk_pdf_manager_credit_text_ID" style="display: <?php echo $link_text_display; ?>">
            <label style="display: inline-block; width: 100px;">Link text: </label>
            <input type="text" name="bsk_pdf_manaer_credit_link_text" id="bsk_pdf_manaer_credit_link_text_ID" value="<?php echo $credit_text; ?>" placeholder="PDFs powered by PDF Manager Pro" style="width: 85%;" />
        </p>
        <p id="bsk_pdf_manager_credit_example_ID" style="display: <?php echo $link_text_display; ?>">
            The link: <a href="https://www.bannersky.com/bsk-pdf-manager/" target="_blank" id="bsk_pdfm_credit_link_ID"><?php echo $credit_text ?></a> will be shown under your PDFs
        </p>
        <p style="margin-top:20px;">
        	<input type="button" id="bsk_pdf_manager_settings_general_tab_save_form_ID" class="button-primary" value="Save General Settings" />
            <input type="hidden" name="bsk_pdf_manager_action" value="general_settings_save" />
        </p>
        <?php echo wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_pdf_manager_settings_general_tab_save_oper_nonce', true, false ); ?>
    </div>
    </form>
    <?php
	}
	
	function bsk_pdf_manager_settings_general_settings_tab_save_fun( $data ) {
		global $wpdb, $current_user;
		//check nonce field
		if( !wp_verify_nonce( $data['bsk_pdf_manager_settings_general_tab_save_oper_nonce'], plugin_basename( __FILE__ ) ) ){
			return;
		}
		
		if( !current_user_can( 'moderate_comments' ) ){
			return;
		}

        $author_access_pdf_category = false;
        $editor_access_all = false;
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) || count($plugin_settings) < 1 ){
			$plugin_settings = array();
		}
        
		$plugin_settings['author_access_pdf_category'] = $author_access_pdf_category;
        
        if( current_user_can('manage_options') ){
            $plugin_settings['editor_access_all'] = $editor_access_all;
        }
        $plugin_settings['directory_with_year_month'] = true;
        $plugin_settings['supported_extension'] = isset( $data['bsk_pdf_supported_extension'] ) ?  $data['bsk_pdf_supported_extension'] : '';
        if( !is_array( $plugin_settings['supported_extension'] ) ){
            $plugin_settings['supported_extension'] = array();
        } 
        if( !in_array( 'pdf', $plugin_settings['supported_extension'] ) ){
            $plugin_settings['supported_extension'][] = 'pdf';
        }

        //credti
        $plugin_settings['enable_credit'] = isset( $data['bsk_pdf_manaer_credit_link_enable'] ) ? $data['bsk_pdf_manaer_credit_link_enable'] : '';
        $plugin_settings['credit_text'] = isset( $data['bsk_pdf_manaer_credit_link_text'] ) ? trim($data['bsk_pdf_manaer_credit_link_text']) : '';

		update_option( BSKPDFManager::$_plugin_settings_option, $plugin_settings );
	}
    
    function bsk_pdfm_scan_all_subfolders( $path, $default_uploader_path, $custom_upload_path ){
        $result = array(); 

        $scaned_results = scandir( $path ); 
        foreach ( $scaned_results as $key => $value ) { 
            $current_full_path = $path.$value.DIRECTORY_SEPARATOR;
            if (!in_array($value,array(".",".."))) { 
                if (!is_dir($current_full_path)  ) { 
                   continue;
                }
                if( $current_full_path == $default_uploader_path ||
                    $current_full_path == $custom_upload_path ){
                    $result[$current_full_path] = $current_full_path;
                    continue;
                }
                $result[$current_full_path] = $this->bsk_pdfm_scan_all_subfolders( $current_full_path, 
                                                                                                                $default_uploader_path, 
                                                                                                                $custom_upload_path );
            } 
        } 

        return $result; 
    }
    
    function bsk_pdfm_display_all_subfolders( $folder_name_array, $default_upload_path, $custom_upload_path ){
        $upload_path_to_set = $custom_upload_path ? $custom_upload_path : $default_upload_path;
        foreach( $folder_name_array as $key => $sub_folders ) {
            $li_data = '';
            if( $upload_path_to_set && $key == $upload_path_to_set ){
                $li_data = ' data-jstree=\'{ "selected" : true }\'';
            }else if( strpos( $upload_path_to_set, $key ) === 0 ){
                $li_data = ' data-jstree=\'{ "opened" : true }\'';
            }
            $folder_name_to_show_array = explode(DIRECTORY_SEPARATOR, $key );
            $tree_node_label = $folder_name_to_show_array[count($folder_name_to_show_array) - 2];
            $relative_path = str_replace(ABSPATH, '', $key );
            echo '<li'.$li_data.' relative_path="'.$relative_path.'">'.$tree_node_label;
            if( is_array( $sub_folders ) ){
                echo '<ul>';
                $this->bsk_pdfm_display_all_subfolders( $sub_folders, $default_upload_path, $upload_path_to_set );
                echo '</ul>';
            }
            echo '</li>';
        }
    }
    
    function bsk_pdfm_rename_jstree_root_node_label( $label_to_set, $relative_path ){
        ?>
        <input type="hidden" id="bsk_pdf_upload_folder_tree_root_label_ID" value="<?php echo $label_to_set; ?>" />
        <input type="hidden" id="bsk_pdf_upload_folder_tree_root_relative_path" value="<?php echo $relative_path; ?>" />
        <?php
    }
    
    function bsk_pdfm_create_custom_upload_folder_fialed_notice(){
        $class = 'notice notice-error';
        $msg = 'Directory <strong>' . BSKPDFManager::$_custom_upload_folder_path . '</strong> can not be created. Please create it first yourself.';

        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $msg ) ); 
    }
    
    function bsk_pdfm_set_custom_upload_folder_writable_fialed_notice(){
        $class = 'notice notice-error';
        $msg  = 'Directory <strong>' . BSKPDFManager::$_custom_upload_folder_path . '</strong> is not writeable ! ';
        $msg .= 'Check <a href="http://codex.wordpress.org/Changing_File_Permissions">http://codex.wordpress.org/Changing_File_Permissions</a> for how to set the permission.';


        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $msg ) ); 
    }
}