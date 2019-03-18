<?php

class BSKPDFM_Dashboard_FTP {

	public function __construct() {
		global $wpdb;
	}
	
	function bsk_pdf_manager_pdfs_add_by_ftp(){
		global $current_user, $wpdb;
		
		//get all categories
		$sql = 'SELECT COUNT(*) FROM '.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name;
		if( $wpdb->get_var( $sql ) < 1 ){
			$create_category_url = add_query_arg( 'page', BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['category'], admin_url() );
			$create_category_url = add_query_arg( 'view', 'addnew', $create_category_url );
			echo 'Please <a href="'.$create_category_url.'">create category</a> first';
			
			return;
		}
						
		$maximum_of_list = 50;
		$pdf_files_under_ftp_folder = array();
		$ftp_folder  = opendir( BSKPDFManager::$_upload_path.BSKPDFManager::$_upload_folder_4_ftp );
		$item_filename = 0;
		while (false !== ($filename = readdir($ftp_folder))) {
			if( $filename == '.' ||
			    $filename == '..' ||
				$filename == 'index.php' ){
				
				continue;
			}
			//check type
			$wp_filetype = wp_check_filetype( $filename, array( 'pdf' => 'application/pdf' ) );
			extract( $wp_filetype );
			if ( !$type || !$ext ){
				
				//not pdf
				continue;
			}
			$item_filename++;
            $timestamp_of_last_modify = filemtime( BSKPDFManager::$_upload_path.BSKPDFManager::$_upload_folder_4_ftp.$filename );
			$pdf_files_under_ftp_folder[] = array( 'name' => $filename, 'date' => date( 'Y-m-d', $timestamp_of_last_modify ) );
			if( $maximum_of_list <= $item_filename ){
				break;
			}
		}
		$upload_folder_4_ftp_display = str_replace( ABSPATH, '', BSKPDFManager::$_upload_path.BSKPDFManager::$_upload_folder_4_ftp );
		echo '  <p style="margin-top:30px;">Please uplaod all you PDF files to the folder <b>'.$upload_folder_4_ftp_display.'</b> first.</p>';
		echo '  <p>After upload, your PDF will be moved out from this folder.<p>';
		echo '  <p>To avoid time out error on your server the maximum of PDF fiels that can be listed here is '.$maximum_of_list.'. It means you may import max 50 PDFs every time but you may come to here any time.</p>';
		?>
        <table class="widefat" style="width:60%;">
		<thead>
			<tr>
				<td class="check-column" style="width:15%; padding-left:10px;"><input type='checkbox' /></td>
				<td style="width:55%;">File</td>
                <td style="width:30%;">Last Modify</td>
			</tr>
		</thead>
		<tbody>
        	<?php
			if( count($pdf_files_under_ftp_folder) < 1 ){
			?>
            <tr class="alternate">
				<td colspan="3">No valid PDF files found</td>
			</tr>
			<?php
            }else{
				$i = 1;
				foreach( $pdf_files_under_ftp_folder as $file_obj ){
					$class_str = '';
					if( $i % 2 == 1 ){
						$class_str = ' class="alternate"';
					}
			?>
                <tr<?php echo $class_str; ?>>
                    <th class='check-column' style="padding-left:10px;"><input type='checkbox' name='bsk_pdf_manager_ftp_files[]' value='<?php echo esc_attr($file_obj['name']) ?>' style="padding:0; margin:0;" /></th>
                    <td><label><?php echo esc_html($file_obj['name']) ?></label></td>
                    <td><label><?php echo esc_html($file_obj['date']) ?></label></td>
                </tr>
        	<?php
					$i++;
				}
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<td class="check-column" style="padding-left:10px;"><input type='checkbox' /></td>
				<td>File</td>
                <td>Last Modify</td>
			</tr>
		</tfoot>
		</table>
		<?php
		if( count($pdf_files_under_ftp_folder) > 0 ){
		?>
        <h3>Category</h3>
		<?php
        echo BSKPDFM_Common_Backend::get_category_hierarchy_checkbox( 'bsk_pdf_manager_ftp_categories[]', 'bsk-pdfm-ftp-category-checkbox', NULL );
		$nonce = wp_create_nonce( 'bsk_pdf_manager_pdf_upload_by_ftp_nonce' );
        ?>
        <hr />
        <h3>Settings</h3>
        <p>
        	<label>
            	<input type="checkbox" name="bsk_pdf_manager_ftp_use_file_name_as_title" id="bsk_pdf_manager_ftp_use_file_name_as_title_ID" />Use file name as title
            </label>
        </p>
        <p id="bsk_pdf_manager_ftp_pdf_title_exclude_extension_container_ID" style="display:none;">
            	<span class="bsk-pdfm-field-label">Exclude extension(.pdf) from title:</span>
                <span class="bsk-pdf-field">
                    <label><input type="radio" name="bsk_pdf_manager_ftp_pdf_exclude_extension_from_title" value="YES" /> Yes</label>
                    <label style="margin-left:20px;"><input type="radio" name="bsk_pdf_manager_ftp_pdf_exclude_extension_from_title" value="NO" checked="checked" /> No</label>
                </span>
        </p>
        <p>
        	<label>
            	<input type="checkbox" name="bsk_pdf_manager_ftp_use_last_modify_as_date" />Use file last modify as date
            </label>
        </p>
		<p style="margin-top:20px;">
        	<input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
        	<input type="hidden" name="bsk_pdf_manager_action" value="pdf_upload_by_ftp" />
        	<input type="button" id="bsk_pdf_manager_add_by_ftp_save_button_ID" class="button-primary" value="Upload..."  disabled />
        </p>
        <?php
		}
	}//end of function
}