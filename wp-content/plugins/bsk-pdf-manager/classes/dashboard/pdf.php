<?php

class BSKPDFM_Dashboard_PDF {

    private $_file_upload_message = array();

    public function __construct() {
		global $wpdb;
		
		$this->bsk_pdf_manager_init_message();

		add_action( 'admin_notices', array($this, 'bsk_pdf_manager_admin_notice') );
		add_action( 'bsk_pdf_manager_pdf_save', array($this, 'bsk_pdf_manager_pdf_save_fun') );
	}
	
	function bsk_pdf_manager_init_message(){
	
		$this->_file_upload_message[1] = array( 'message' => 'The uploaded file exceeds the maximum file size allowed.', 
												'type' => 'ERROR');
		$this->_file_upload_message[2] = array( 'message' => 'The uploaded file exceeds the maximum file size allowed.', 
												'type' => 'ERROR');
		$this->_file_upload_message[3] = array( 'message' => 'The uploaded file was only partially uploaded. Please try again in a few minutes.', 
												'type' => 'ERROR');
		$this->_file_upload_message[4] = array( 'message' => 'No file was uploaded. Please try again in a few minutes.', 
												'type' => 'ERROR');
		$this->_file_upload_message[5] = array( 'message' => 'File size is 0 please check and try again in a few minutes.', 
												'type' => 'ERROR');
		$this->_file_upload_message[6] = array( 'message' => 'Failed, seems there is no temporary folder. Please try again in a few minutes.', 
												'type' => 'ERROR');
		$this->_file_upload_message[7] = array( 'message' => 'Failed to write file to disk. Please try again in a few minutes.', 
												'type' => 'ERROR');
		$this->_file_upload_message[8] = array( 'message' => 'A PHP extension stopped the file upload. Please try again in a few minutes.', 
												'type' => 'ERROR');
		$this->_file_upload_message[15] = array( 'message' => 'Invalid file type, the file you uploaded is not allowed.', 
												 'type' => 'ERROR');
		$this->_file_upload_message[16] = array( 'message' => 'Faild to write file to destination folder.', 
												 'type' => 'ERROR');
		$this->_file_upload_message[17] = array( 'message' => 'Invalid file. Please try again.', 
												 'type' => 'ERROR');
		
		$this->_file_upload_message[20] = array( 'message' => 'Your file uploaded successfully.', 
												 'type' => 'SUCCESS');
		$this->_file_upload_message[21] = array( 'message' => 'Insert file record into database failed.', 
												 'type' => 'ERROR');	
												 
		$this->_file_upload_message[31] = array( 'message' => 'Upload file failed.', 
												 'type' => 'ERROR');					
		$this->_file_upload_message[32] = array( 'message' => 'Upload file failed.', 
												 'type' => 'ERROR');					
		$this->_file_upload_message[33] = array( 'message' => 'Upload file failed.', 
												 'type' => 'ERROR');																 
		$this->_file_upload_message[34] = array( 'message' => 'Upload file failed.', 
												 'type' => 'ERROR');
	}
	
	function bsk_pdf_manager_admin_notice(){
		$current_page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
		if( !$current_page || !in_array($current_page, BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages) ){
			return;
		}
		
		$message_id = isset($_REQUEST['message']) ? $_REQUEST['message'] : 0;
		if( !$message_id ){
			return;
		}
		if( !isset($this->_file_upload_message[ $message_id ]) ){
			return;
		}
		
		$type = $this->_file_upload_message[ $message_id ]['type'];
		$msg_to_show = $this->_file_upload_message[ $message_id ]['message'];
		if( !$msg_to_show ){
			return;
		}
		$msg_to_show = '<p>'.$msg_to_show.'</p>';
		if( in_array( $message_id, array(16, 31, 32, 33, 34) ) ){
			$msg_to_show .= '<p>'.get_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_'.$message_id ).'</p>';
		}
		
		//admin message
		if( $type == 'SUCCESS' ){
			echo '<div class="notice notice-success is-dismissible">';
			echo $msg_to_show;
			echo '</div>';
		}else if( $type == 'ERROR' ){
			echo '<div class="notice notice-error is-dismissible">';
			echo $msg_to_show;
			echo '</div>';
		}
	}

	
	function pdf_edit( $pdf_id = -1 ){
		global $wpdb;
		
		//get all categories
		$sql = 'SELECT * FROM '.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name;
		$categories = $wpdb->get_results( $sql );
		
		$pdf_date = date('Y-m-d', current_time('timestamp'));
		$pdf_obj_array = array();
        $pdf_categories_array = array();
		if ($pdf_id > 0){
			$sql = 'SELECT * FROM '.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.' WHERE id = %d';
			$sql = $wpdb->prepare( $sql, $pdf_id );
			$pdfs_obj_array = $wpdb->get_results( $sql );
			if (count($pdfs_obj_array) > 0){
				$pdf_obj_array = (array)$pdfs_obj_array[0];
				$pdf_date = date('Y-m-d', strtotime($pdf_obj_array['last_date']));
			}
		}
		
		if( isset($pdf_obj_array['id']) && $pdf_obj_array['id'] ){
			//get all categories which the PDF associated
            $sql = 'SELECT * FROM `'.$wpdb->prefix.BSKPDFManager::$_rels_tbl_name.'` WHERE `pdf_id` = '.$pdf_obj_array['id'];
            $results = $wpdb->get_results( $sql );
            if( $results && is_array($results) && count($results) > 0 ){
                foreach( $results as $rel_obj ){
                    $pdf_categories_array[] = $rel_obj->cat_id;
                }
            }
		}
		
		$default_enable_featured_image = true;
        $organise_directory_strucutre_with_year_month = true;
        $supported_extension = false;
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
			if( isset($plugin_settings['enable_featured_image']) ){
				$default_enable_featured_image = $plugin_settings['enable_featured_image'];
			}
            if( isset($plugin_settings['directory_with_year_month']) ){
                $organise_directory_strucutre_with_year_month = $plugin_settings['directory_with_year_month'];
			}
            if( isset($plugin_settings['supported_extension']) ){
                $supported_extension = $plugin_settings['supported_extension'];
			}
		}
		?>
        <div class="bsk_pdf_manager_pdf_edit">
        <?php
			$is_by_media_uploader = false;
			$file_url = '';
			if( $pdf_obj_array ){
				if( isset($pdf_obj_array['by_media_uploader']) && $pdf_obj_array['by_media_uploader'] > 1 ){
					$file_url = wp_get_attachment_url( $pdf_obj_array['by_media_uploader'] ); 
					$is_by_media_uploader = true;
				}else{
					if( file_exists(ABSPATH.$pdf_obj_array['file_name']) ){
						$file_url = site_url().'/'.$pdf_obj_array['file_name'];
					}
				}
			}
		?>
            <p id="bsk_pdfm_category_container_ID">
                <span class="bsk-pdfm-field-label">Category:</span>
                <div class="bsk-pdf-field">
                    <?php
                    $categories_with_data = array();
                    if( $categories && is_array($categories) && count($categories) > 0 && !is_wp_error($categories) ){
                        $dropdown_str = BSKPDFM_Common_Backend::get_category_dropdown( 'bsk_pdf_edit_cat', 'bsk_pdf_edit_cat_ID', 'Please select category...', array() );
                        echo $dropdown_str;
                        foreach( $categories as $cat_obj ){
                            $categories_with_data[$cat_obj->id] = $cat_obj->title;
                        }
                    }else{
                        $create_category_url = add_query_arg( 'page', 
                                                                                BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['base'], 
                                                                                admin_url('admin.php') );
                        $create_category_url = add_query_arg( 'view', 'addnew', $create_category_url );
                        echo 'Please <a href="'.$create_category_url.'">create category</a> first';
                    }
                    ?>
                </div>
                <div>
                    <span class="bsk-pdfm-field-label">&nbsp;</span>
                    <div id="bsk_pdf_edit_selected_cat_container_ID" style="display: inline-block; width: 78%;padding-top: 15px;">
                        <?php
                        if( $pdf_categories_array && is_array($pdf_categories_array) && count($pdf_categories_array) > 0 ){
                            foreach( $pdf_categories_array as $cat_id ){
                                $cat_title = isset($categories_with_data[$cat_id]) ? $categories_with_data[$cat_id] : '';
                                echo '<span style="display: inline-block; padding-right:10px;"><a href="javascript:void(0);" class="bsk-pdf-edit-delete-cat-anchor" data-catid="'.$cat_id.'"><img src="'.BSKPDFManager::$_delete_cat_icon_url.'" style="width:12px;height:12px;" /></a>&nbsp;'.$cat_title.'</span>';
                            }
                        }
                        ?>
                    </div>
                    <div style="clear:both;"></div>
                </div>
                <div style="clear:both;"></div>
            </p>
            <input type="hidden" id="bsk_pdf_edit_delete_cat_icon_ID" value="<?php echo BSKPDFManager::$_delete_cat_icon_url; ?>" />
            <input type="hidden" name="bsk_pdf_edit_cat_ids" id="bsk_pdf_edit_cat_ids_ID" value="<?php echo implode(',', $pdf_categories_array); ?>" />
            <hr />
            <?php
                $title_value = '';
                $description = '';
                if( $pdf_obj_array && isset($pdf_obj_array['title']) ){
                    $title_value = $pdf_obj_array['title'];
                }
                if( $pdf_obj_array && isset($pdf_obj_array['description']) ){
                    $description = $pdf_obj_array['description'];
                }
            ?>
            <p>
                <span class="bsk-pdfm-field-label">Title:</span>
                <span class="bsk-pdf-field">
                    <input type="text" name="bsk_pdf_manager_pdf_titile" id="bsk_pdf_manager_pdf_titile_id" value="<?php echo $title_value; ?>" maxlength="510" style="width:350px;"/>
                </span>
            </p>
            <div style="width: 100%;">
                <div style="width: 20%; height: 160px; float: left; vertical-align: middle; display: table;">
                    <span style="vertical-align:middle; display: table-cell;">Description:</span>
                </div>
                <div style="width: 75%; height: 160px; float: left; padding-left: 5px;">
                    <textarea name="pdf_description" id="pdf_description_id" maxlength="512" style="width:85%; height: 150px;" disabled /><?php echo $description ?></textarea>
                </div>
                <div style="clear: both;"></div>
            </div>
            <?php if ($pdf_id < 1 || $file_url == "" ){ ?>
			<p>
                <span class="bsk-pdfm-field-label">Use file name as title:</span>
                <span class="bsk-pdf-field">
                    <input type="checkbox" name="bsk_pdf_manager_pdf_file_new_use_name_as_title" id="bsk_pdf_manager_pdf_file_new_use_name_as_title_ID" disabled />
                </span>
            </p>
            <p id="bsk_pdf_manager_pdf_title_exclude_extension_container_ID">
            	<span class="bsk-pdfm-field-label">Exclude extension(.pdf) from title:</span>
                <span class="bsk-pdf-field">
                    <label>
                        <input type="radio" name="bsk_pdf_manager_pdf_exclude_extension_from_title" value="YES" disabled /> Yes
                    </label>
                    <label style="margin-left:20px;">
                        <input type="radio" name="bsk_pdf_manager_pdf_exclude_extension_from_title" value="NO" checked="checked" disabled /> No
                    </label>
                </span>
            </p>
            <?php }  ?>
            <hr />
            <?php
			$use_media_upload_checked_str = $is_by_media_uploader ? ' checked="checked"' : '';
			$media_upload_container_display = $is_by_media_uploader ? 'block' : 'none';
			$pdfm_php_upload_container_display = $is_by_media_uploader ? 'none' : 'block';
			?>
            <p>
                <span class="bsk-pdfm-field-label">Use WordPress Media Uploader:</span>
                <span class="bsk-pdf-field">
                    <input type="checkbox" name="bsk_pdf_manager_pdf_use_media_uploader" id="bsk_pdf_manager_pdf_use_media_uploader_ID" <?php echo $use_media_upload_checked_str; ?> disabled />
                </span>
            </p>
            <div id="bsk_pdfm_wordpress_uploader_container_ID" style="margin-top:20px;display:block;">
                <span class="bsk-pdfm-field-label" id="bsk_pdfm_wordpress_uploader_label_ID">Upload new:</span>
                <div id="bsk_pdfm_wordpress_uploader_ID">
                    <div class="inside">
                        <p class="hide-if-no-js" style="background-color:#f1f1f1;">
                            <a title="Upload PDF Document" href="javascript:void(0);" id="bsk_pdf_manager_upload_pdf_anchor_ID">Upload PDF Document</a>
                        </p>
                    </div>
                </div>
                <div style="clear:both;"></div>
            </div>
            <hr />
            <div id="bsk_pdfm_php_uploader_container_ID" style="margin-top:20px;display:block;">
            	<?php if ($pdf_id > 0 && $file_url){ ?>
                <p>
                    <span class="bsk-pdfm-field-label">Old File:</span>
                    <span style="width: 70%; padding-left: 5px;">
                        <?php
                        $file_name_with_dir_structure = $pdf_obj_array['file_name'];
                        ?>
                        <a href="<?php echo $file_url; ?>" target="_blank"><?php echo $file_name_with_dir_structure; ?></a>
                        <input type="hidden" name="bsk_pdf_manager_pdf_file_old" id="bsk_pdf_manager_pdf_file_old_id" value="<?php echo $pdf_obj_array['file_name']; ?>" />
                    </span>
                </p>
                <p>&nbsp;</p>
                <div style="clear:both;"></div>
            	<?php }?>
                <span class="bsk-pdfm-field-label">Upload new:</span>
                <?php
                $u_bytes = BSKPDFM_Common_Backend::bsk_pdf_manager_pdf_convert_hr_to_bytes( ini_get( 'upload_max_filesize' ) );
                $p_bytes = BSKPDFM_Common_Backend::bsk_pdf_manager_pdf_convert_hr_to_bytes( ini_get( 'post_max_size' ) );
                $maximum_uploaded_numeric = floor(min($u_bytes, $p_bytes) / 1024);
                $maximum_uploaded_numeric_str = floor(min($u_bytes, $p_bytes) / 1024).' K bytes.';
                if ($maximum_uploaded_numeric > 1024){
                    $maximum_uploaded_numeric_str = floor( $maximum_uploaded_numeric / 1024).' M bytes.';
                }
                ?>
                <div class="bsk-pdf-field" style="padding-left: 5px;">
                    <input type="file" name="bsk_pdf_file" id="bsk_pdf_file_id" value="Browse" />
                    <p style="font-style:italic;">
                    	Maximum file size: <?php echo $maximum_uploaded_numeric_str; ?> To change this please modify your hosting configuration in php.ini or .htaccess file.
                    </p>
                    <?php
                    $other_extension = '';
                    if( $supported_extension && is_array( $supported_extension ) && count( $supported_extension ) > 0 ){
                        foreach( $supported_extension as $ext ){
                            $other_extension .= ', <b>.'.$ext.'</b>';
                        }
                    }
                    ?>
                    <p style="font-style:italic;">
                    	Only <b>.pdf</b><?php echo $other_extension; ?> allowed.
                    </p>
                </div>
                <?php
                $default_upload_path = BSKPDFManager::$_upload_path.BSKPDFManager::$_upload_folder;
                $custom_upload_path = BSKPDFManager::$_custom_upload_folder_path;
                $current_upload_path = $custom_upload_path ? $custom_upload_path : $default_upload_path;
                if( $organise_directory_strucutre_with_year_month ){
                    $current_upload_path .= date('Y/m/', current_time("timestamp"));
                }
                ?>
                <p>
                    <span class="bsk-pdfm-field-label">Current upload folder:</span>
                    <span style="font-weight: bold; padding-left: 5px; width: 70%;"><?php echo str_replace( ABSPATH, '', $current_upload_path ); ?></span>
                </p>
                <div style="clear:both;"></div>
            </div>
            <div style="clear:both;"></div>
            
            <?php if ( $default_enable_featured_image ) { ?>
            <hr />
            <div>
                <span class="bsk-pdfm-field-label" id="bsk_pdfm_featured_image_uploader_label_ID">Featured Image:</span>
                <div id="bsk_pdfm_featured_image_uploader_ID">
                    <div class="inside postbox" style="background-color:#f1f1f1;">
                        <p class="hide-if-no-js">
                            <a title="Set featured image" href="javascript:void(0);" id="bsk_pdf_manager_set_featured_image_anchor_ID">Set featured image</a>
                        </p>
                </div>
                <div style="clear:both;"></div>
            </div>
            <?php } ?>
            <p>
                <span class="bsk-pdfm-field-label">Date:</span>
                <span class="bsk-pdf-field">
                    <input type="text" name="pdf_date" id="pdf_date_id" value="<?php echo $pdf_date ?>" class="bsk-date"/>
                    <span id="bsk_pdfm_lastmodified_section_ID" style="display: inline-block;">
                        <label style="display: inline-block; width: auto; margin-left: 20px;">
                            <input type="checkbox" name="pdf_date_use_file_last_modified" id="pdf_date_use_file_last_modify_ID" value="Yes" disabled /> Use file last modified
                        </label>
                        <span id="bsk_pdfm_lastmodified_text_ID"></span>
                        <input type="hidden" name="bsk_pdfm_lastmodified_val" id="bsk_pdfm_lastmodified_val_ID" value="" />
                    </span>
                </span>
            </p>
            <?php if ($pdf_id < 1 || $file_url == "" ){ ?>
            <p id="pdf_date_description_4_media_library_ID" style="display: none;">
                <span class="bsk-pdfm-field-label">&nbsp;</span>
                <span  style="font-style: italic;">In WordPress Meida Library, the last modify is the date when you upload</span>
            </p>
            <?php } ?>
            <?php
			$pdf_publish_date = '';
			if( $pdf_obj_array && isset($pdf_obj_array['publish_date']) && $pdf_obj_array['publish_date'] ){
				$pdf_publish_date = date( 'Y-m-d', strtotime($pdf_obj_array['publish_date']) );
			}
			$pdf_expiry_date = '';
			if( $pdf_obj_array && isset($pdf_obj_array['expiry_date']) && $pdf_obj_array['expiry_date'] ){
				$pdf_expiry_date = date( 'Y-m-d', strtotime($pdf_obj_array['expiry_date']) );
			}
			?>
            <p>
                <span class="bsk-pdfm-field-label">Publish Date:</span>
                <span class="bsk-pdf-field">
                    <input type="text" name="pdf_publish_date" id="pdf_publish_date_id" value="<?php echo $pdf_publish_date ?>" class="bsk-date" disabled  />
                    <span style="display:inline-block; font-style:italic; margin-left: 20px;">Only available <strong>same or after</strong> this date, leave blank to available always</span>
                </span>
            </p>
            <p>
                <span class="bsk-pdfm-field-label">Expiry Date:</span>
                <span class="bsk-pdf-field">
                    <input type="text" name="pdf_expiry_date" id="pdf_expiry_date_id" value="<?php echo $pdf_expiry_date ?>" class="bsk-date" disabled />
                    <span style="display:inline-block; font-style:italic; margin-left: 20px;">Only available <strong>before</strong> this date, leave blank to available always</span>
                </span>
            </p>
            <?php
                $list_cat_id = isset( $_REQUEST['cat'] ) ? intval( $_REQUEST['cat'] ) : 0;
            ?>
            <p>
                <input type="hidden" name="bsk_pdf_manager_action" value="pdf_save" />
                <input type="hidden" name="bsk_pdf_manager_pdf_id" value="<?php echo $pdf_id; ?>" />
                <input type="hidden" name="bsk_pdf_manager_list_cat_id" value="<?php echo $list_cat_id; ?>" />
                <?php 
                    echo wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_pdf_manager_pdf_save_oper_nonce', true, false );
                    $ajax_nonce = wp_create_nonce( 'bsk_pdf_manager_pdf_page_ajax_oper_nonce' );
                ?>
                <input type="hidden" id="bsk_pdf_manager_pdf_page_ajax_oper_nonce_ID" value="<?php echo $ajax_nonce; ?>" />
            </p>
		</div>
		<?php
	}
	
	function bsk_pdf_manager_pdf_save_fun( $data ){
		global $wpdb;
		//check nonce field
		if( !wp_verify_nonce( $data['bsk_pdf_manager_pdf_save_oper_nonce'], plugin_basename( __FILE__ ) ) ){
			
			return;
		}
		if( !isset($data['bsk_pdf_edit_cat_ids']) ||
			$data['bsk_pdf_edit_cat_ids'] == "" ){
				
			return;
		}
        $bsk_pdf_manager_pdf_edit_categories = explode( ',', $data['bsk_pdf_edit_cat_ids'] );
        if( !is_array($bsk_pdf_manager_pdf_edit_categories) || 
            count($bsk_pdf_manager_pdf_edit_categories) < 1 ){
            
            return;
        }
        foreach( $bsk_pdf_manager_pdf_edit_categories as $key => $cat_id ){
            $bsk_pdf_manager_pdf_edit_categories[$key] = intval($cat_id);
        }
		
		$pdf_id = trim($data['bsk_pdf_manager_pdf_id']);
		$pdf_data = array();
        $pdf_data_format = array();
		$pdf_data['title'] = $data['bsk_pdf_manager_pdf_titile'];
        $pdf_data_format['title'] = '%s';
        $pdf_data['cat_id'] = '999999';
        $pdf_data_format['cat_id'] = '%s';
		$pdf_data['last_date'] = isset($data['pdf_date']) && trim($data['pdf_date']) ? trim($data['pdf_date']).' 00:00:00' : date('Y-m-d 00:00:00', current_time('timestamp'));
        $pdf_data_format['last_date'] = '%s';
		$pdf_data['by_media_uploader'] = 0;
        $pdf_data_format['by_media_uploader'] = '%d';

        $pdf_data['description'] = '';
		$pdf_data_format['description'] = '%s';
        
		$pdf_data['publish_date'] = NULL;
		$pdf_data['expiry_date'] = NULL;
        
        $pdf_data_format['publish_date'] = '%s';
        $pdf_data_format['expiry_date'] = '%s';
		
        foreach( $pdf_data as $key => $element ){
            $pdf_data[$key] = wp_unslash( $element );
        }
		
		$message_id = 20;
		if ($pdf_id > 0){
			$return_detinate_name = $this->bsk_pdf_manager_pdf_upload_file(
                                                                                    $_FILES['bsk_pdf_file'], 
                                                                                    $pdf_id, 
                                                                                    $message_id, 
                                                                                    $bsk_pdf_manager_pdf_file_old );
            if ($return_detinate_name){
                $pdf_data['file_name'] = $return_detinate_name;
                $pdf_data_format['file_name'] = '%s';
                //new one uploaded, the old one should be removed
                if ($data['bsk_pdf_manager_pdf_file_old']){
                    unlink(ABSPATH.$data['bsk_pdf_manager_pdf_file_old']);
                }
                if( isset($data['pdf_date_use_file_last_modified']) ){
                    $pdf_data['last_date'] = $data['bsk_pdfm_lastmodified_val'];
                    $pdf_data_format['file_name'] = '%s';
                }
            }
			unset($pdf_data['id']); //for update, dont't chagne id
			
			$wpdb->update( $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name, $pdf_data, array('id' => $pdf_id), $pdf_data_format );
            
            //update pdf's category
            $this->bsk_pdf_manager_update_category( $pdf_id, $bsk_pdf_manager_pdf_edit_categories);
		}else{
			//insert
			$return = $wpdb->insert( $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name, $pdf_data, $pdf_data_format );
			if ( !$return ){
				$message_id = 21;
				
				$redirect_to = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['pdf'] .'&message='.$message_id );
				wp_redirect( $redirect_to );
				exit;
			}
            $new_pdf_id = $wpdb->insert_id;
            $return_detinate_name = $this->bsk_pdf_manager_pdf_upload_file($_FILES['bsk_pdf_file'], $new_pdf_id, $message_id);
            if ( $return_detinate_name ){
                $data_to_update = array( 'file_name' => $return_detinate_name );
                $wpdb->update( $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name, 
                                         $data_to_update, 
                                         array('id' => $new_pdf_id) 
                                        );
                $this->bsk_pdf_manager_update_category( $new_pdf_id, $bsk_pdf_manager_pdf_edit_categories);
            }else{
                $sql = 'DELETE FROM `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` WHERE id ='.$new_pdf_id;
                $wpdb->query( $sql );
            }
		}
		
        $redirect_to = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['pdf'].'&message='.$message_id  );
        if( isset( $data['bsk_pdf_manager_list_cat_id'] ) && $data['bsk_pdf_manager_list_cat_id'] ){
            $redirect_to = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['pdf'].'&message='.$message_id.'&cat='.$data['bsk_pdf_manager_list_cat_id']  );
        }
		
		wp_redirect( $redirect_to );
		exit;
	}
	
    function bsk_pdf_manager_update_category( $pdf_id, $categories_id_array ){
        global $wpdb;
        
        $relationship_tbl_name = $wpdb->prefix.BSKPDFManager::$_rels_tbl_name;
        $sql = 'DELETE FROM `'.$relationship_tbl_name.'` WHERE `pdf_id` = %d';
        $sql = $wpdb->prepare( $sql, $pdf_id );
        $wpdb->query( $sql );
        
        if( !is_array($categories_id_array) || count($categories_id_array) < 1 ){
            return;
        }

        //insert new
        foreach( $categories_id_array as $cat_id ){
            if( $cat_id < 1 ){
                continue;
            }
            $wpdb->insert( $relationship_tbl_name, array( 'pdf_id' => $pdf_id, 'cat_id' => $cat_id ), array( '%d', '%d' ) );
        }
    }
	function bsk_pdf_manager_pdf_upload_file($file, $destination_name_prefix, &$message_id, $old_file = ''){
		if (!$file["name"]){
			if($old_file){
				$message_id = 17;
			}
			return false;
		}				
		if ( $file["error"] != 0 ){
			$message_id = $file["error"];
			return false;
		}
        $file_extension_array = explode('.', $file["name"] );
        if( !is_array( $file_extension_array ) || count($file_extension_array) == 1 ){
            $message_id = 15;
			return false;
        }
        $file_extension = $file_extension_array[count($file_extension_array) - 1];
        
        $supported_extension_and_mime_type = BSKPDFM_Common_Backend::get_supported_extension_with_mime_type();
        if( !array_key_exists( strtolower($file_extension), $supported_extension_and_mime_type) ){
            $message_id = 15;
			return false;
        }
		if( !in_array( $file["type"], $supported_extension_and_mime_type[strtolower($file_extension)] ) ){
            $message_id = 15;
            return false;
        }
		
        $default_upload_path = BSKPDFManager::$_upload_path.BSKPDFManager::$_upload_folder;
        $custom_upload_path = '';
        $current_upload_path = $custom_upload_path ? $custom_upload_path : $default_upload_path; 
		//save pdf by year/month
        $organise_directory_strucutre_with_year_month = true;
        $plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
        if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['directory_with_year_month']) ){
                $organise_directory_strucutre_with_year_month = $plugin_settings['directory_with_year_month'];
			}
		}
        if( $organise_directory_strucutre_with_year_month ){
            $year = date( 'Y', current_time('timestamp') );
            $month = date( 'm', current_time('timestamp') );
            if ( !is_dir($current_upload_path.$year) ) {
                if ( !wp_mkdir_p( $current_upload_path.$year ) ) {
                    $message_id = 31;
                    update_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_31', 'Create folder: '.$current_upload_path.$year.'/ failed.');
                    return false;
                }
            }
            if ( !is_writeable( $current_upload_path.$year ) ) {
                update_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_32', 'Directory: '.$current_upload_path.$year.'/ not writable.');
                $message_id = 32;
                return false;
            }
            if ( !is_dir($current_upload_path.$year.'/'.$month) ) {
                if ( !wp_mkdir_p( $current_upload_path.$year.'/'.$month ) ) {
                    $message_id = 33;
                    update_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_33', 'Create folder: '.$current_upload_path.$year.'/'.$month.'/ failed.');
                    return false;
                }
            }
            if ( !is_writeable( $current_upload_path.$year.'/'.$month ) ) {
                update_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_34', 'Directory: '.$current_upload_path.$year.'/'.$month.'/ not writable.');
                $message_id = 34;
                return false;
            }
            if( !file_exists($current_upload_path.$year.'/'.$month.'/index.php') ){
                copy( BSK_PDFM_PLUGIN_DIR.'/assets/index.php',
                         $current_upload_path.$year.'/'.$month.'/index.php' );
            }
            
            //unique file name
            $upload_pdf_name = $file["name"];
            $destinate_file_name = wp_unique_filename( $current_upload_path.$year.'/'.$month.'/', $upload_pdf_name);

            //move file
            $ret = move_uploaded_file( 
                                         $file["tmp_name"], 
                                         $current_upload_path.$year.'/'.$month.'/'.$destinate_file_name
                                        );
            if( !$ret ){
                update_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_16', 'Upload file to: '.$current_upload_path.$year.'/'.$month.'/ failed.');
                $message_id = 16;
                return false;
            }
            
            return str_replace(ABSPATH, '', $current_upload_path).$year.'/'.$month.'/'.$destinate_file_name;
        }
        //unique file name
        $upload_pdf_name = $file["name"];
        $destinate_file_name = wp_unique_filename( $current_upload_path.'/'.$month.'/', $upload_pdf_name);

        //move file
        $ret = move_uploaded_file( 
                                                 $file["tmp_name"], 
                                                 $current_upload_path.$year.'/'.$month.'/'.$destinate_file_name
                                              );
        if( !$ret ){
            update_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_16', 'Upload file to: '.$current_upload_path.$year.'/'.$month.'/ failed.');
            $message_id = 16;
            return false;
        }
        
        return str_replace(ABSPATH, '', $current_upload_path).$destinate_file_name;
	}
	
	function bsk_pdf_manager_current_user_can(){
		global $current_user;
		
		if ( current_user_can('level_3') ){
			return true;
		}else{
			/*
			//get role;
			$user_roles = $current_user->roles;
			$user_role = array_shift($user_roles);
			
			if ($user_role == 'spcial role'){
				return true;
			}
			*/
		}
		return false;
	}
	
} //end of class