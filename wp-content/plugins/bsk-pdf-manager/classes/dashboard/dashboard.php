<?php
class BSKPDFM_Dashboard {
    
    public static $_bsk_pdfm_pro_pages = array(  
                                                                 'base'		=> 'bsk-pdf-manager',
                                                                 'category' => 'bsk-pdf-manager-category', 
                                                                 'pdf' 		=> 'bsk-pdf-manager-pdfs', 
                                                                 'add_by_ftp' 	=> 'bsk-pdf-manager-add-by-ftp',
                                                                 'setting' 		=> 'bsk-pdf-manager-settings-support', 
                                                                 'help' 		=> 'bsk-pdfm-help',
                                                                 'license_update' => 'bsk-pdf-manager-license-update' );
    
    private static $_pro_tips_for_category = array( 
                                    'Hierarchical Category',
                                    'Description', 
                                    'Password', 
                                    'Empty Message',
                                   );
    private static $_pro_tips_for_pdf = array( 
                                    'Custom order',
                                    'Mulitple Categories',
                                    'File name ( exclude extension ) as title',
                                    'Description', 
                                    'WordPress Media Uploader', 
                                    'Featured Image',
                                    'Date of file last modified',
                                    'Publish Date',
                                    'Expiry Date',
                                    'Download Count',
                                   );
    private static $_pro_tips_for_pdf_bulk_change_category = array( 
                                    'Bulk change category',
                                   );
    private static $_pro_tips_for_add_by_ftp = array( 
                                    'Add by FTP',
                                   );
    private static $_pro_tips_for_settings = array( 
                                    'Disable year/month direcotry strtucutre',
                                    'Change upload folder',
                                    'Featured image',
                                    'Statistics',
                                   );
	private static $_bsk_pdfm_OBJ = NULL;
	private static $_bsk_pdfm_OBJ_category = NULL;
	private static $_bsk_pdfm_OBJ_pdf = NULL;
    private static $_bsk_pdfm_OBJ_ftp = NULL;
	private static $_bsk_pdfm_OBJ_settings = NULL;
    private static $_bsk_pdfm_OBJ_help = NULL;
	
	private static $_bsk_pdfm_OBJ_update_helper = NULL;
    private static $_bsk_pdfm_OBJ_updater = NULL;
			
	public function __construct() {
		
		require_once( 'categories.php' );
		require_once( 'category.php' );
		require_once( 'pdfs.php' );	
		require_once( 'pdf.php' );
        require_once( 'ftp.php' );
		require_once( 'settings/settings.php' );
        require_once( 'ads.php' );
        require_once( 'help.php' );

        self::$_bsk_pdfm_OBJ_category = new BSKPDFM_Dashboard_Category();		
		self::$_bsk_pdfm_OBJ_pdf = new BSKPDFM_Dashboard_PDF();
        self::$_bsk_pdfm_OBJ_ftp = new BSKPDFM_Dashboard_FTP();
		self::$_bsk_pdfm_OBJ_settings = new BSKPDFM_Dashboard_Settings();
        self::$_bsk_pdfm_OBJ_help = new BSKPDFM_Dashboard_Help();
		
		add_action( 'admin_menu', array( $this, 'bsk_pdf_manager_dashboard_menu' ) );
	}
	
	function bsk_pdf_manager_dashboard_menu() {
		
		//read plugin settings
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		$author_access_pdf_category = false;
        $editor_access_all = false;
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
			if( isset($plugin_settings['author_access_pdf_category']) ){
				$author_access_pdf_category = $plugin_settings['author_access_pdf_category'];
			}
            if( isset($plugin_settings['editor_access_all']) ){
				$editor_access_all = $plugin_settings['editor_access_all'];
			}
		}
		
		$category_pdf_authorized_level = 'moderate_comments';
        $other_menus_authorized_level = 'manage_options';
		if( $author_access_pdf_category ){
			$category_pdf_authorized_level = 'publish_posts';
		}
        if( $editor_access_all ){
			$other_menus_authorized_level = 'moderate_comments';
		}
		
		add_menu_page( 
                                 'BSK PDF Manager', 
                                 'BSK PDF Manager', 
                                 $category_pdf_authorized_level, 
                                 self::$_bsk_pdfm_pro_pages['base'], 
                                 '', 
                                 'dashicons-media-document'
                                );

        add_submenu_page( 
                            self::$_bsk_pdfm_pro_pages['base'],
                            'Categories', 
                            'Categories',
                            $category_pdf_authorized_level, 
                            self::$_bsk_pdfm_pro_pages['base'],
                            array($this, 'bsk_pdf_manager_categories') );

        add_submenu_page( self::$_bsk_pdfm_pro_pages['base'],
                          'PDF Documents', 
                          'PDF Documents', 
                          $category_pdf_authorized_level, 
                          self::$_bsk_pdfm_pro_pages['pdf'],
                          array($this, 'bsk_pdf_manager_pdfs') );

        add_submenu_page( self::$_bsk_pdfm_pro_pages['base'],
                          'Add by FTP', 
                          'Add by FTP', 
                          $other_menus_authorized_level, 
                          self::$_bsk_pdfm_pro_pages['add_by_ftp'],
                          array($this, 'bsk_pdf_manager_pdfs_add_by_ftp_interface') );	

        add_submenu_page( self::$_bsk_pdfm_pro_pages['base'],
                          'Settings', 
                          'Settings', 
                          $other_menus_authorized_level, 
                          self::$_bsk_pdfm_pro_pages['setting'],
                          array($this, 'bsk_pdf_manager_settings_support') );

        add_submenu_page( self::$_bsk_pdfm_pro_pages['base'],
                          'Help', 
                          'Help',
                          $category_pdf_authorized_level, 
                          self::$_bsk_pdfm_pro_pages['help'],
                          array($this, 'bsk_pdf_manager_help') );
	}
	
	function bsk_pdf_manager_categories(){
		global $current_user;
		

		$categories_curr_view = 'list';
		if(isset($_GET['view']) && $_GET['view']){
			$categories_curr_view = trim($_GET['view']);
		}
		if(isset($_POST['view']) && $_POST['view']){
			$categories_curr_view = trim($_POST['view']);
		}
		
		$category_base_page = admin_url( 'admin.php?page='.self::$_bsk_pdfm_pro_pages['base'] );
		if ($categories_curr_view == 'list'){
            $_bsk_pdfm_OBJ_categories = new BSKPDFM_Dashboard_Categories();

			//Fetch, prepare, sort, and filter our data...
			$_bsk_pdfm_OBJ_categories->prepare_items();
			
			$category_add_new_page = add_query_arg( 'view', 'addnew', $category_base_page );
	
			echo '<div class="wrap">
					<div id="icon-edit" class="icon32"><br/></div>
					<h2>BSK PDF Categories<a href="'.$category_add_new_page.'" class="add-new-h2">Add New</a></h2>';
            $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_category );
			echo '<form id="bsk_pdf_manager_categories_form_id" method="post" action="'.$category_base_page.'">';
						$_bsk_pdfm_OBJ_categories->views();
						$_bsk_pdfm_OBJ_categories->display();
			echo '</form>
				  </div>';
		}else if ( $categories_curr_view == 'addnew' || $categories_curr_view == 'edit'){
			$category_id = -1;
			if(isset($_GET['categoryid']) && $_GET['categoryid']){
				$category_id = trim($_GET['categoryid']);
				$category_id = intval($category_id);
			}	
			echo '<div class="wrap">
					<div id="icon-edit" class="icon32"><br/></div>
					<h2>BSK PDF Category</h2>';
            $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_category );
			echo '<form id="bsk_pdf_manager_categories_form_id" method="post" action="'.$category_base_page.'">';
					self::$_bsk_pdfm_OBJ_category->bsk_pdf_manager_category_edit( $category_id );
			echo   '<p style="margin-top:20px;"><input type="button" id="bsk_pdf_manager_category_save" class="button-primary" value="Save" /></p>'."\n";
			echo '	</form>
				  </div>';
		}
	}
	
	function bsk_pdf_manager_pdfs(){
		global $current_user;
		
		$lists_curr_view = 'list';
		if(isset($_GET['view']) && $_GET['view']){
			$lists_curr_view = trim($_GET['view']);
		}
		if(isset($_POST['view']) && $_POST['view']){
			$lists_curr_view = trim($_POST['view']);
		}
		
		if ($lists_curr_view == 'list'){
			global $wpdb;
			
			if( (isset($_POST['action']) && $_POST['action'] == 'changecat') || 
			    (isset($_POST['action2']) && $_POST['action2'] == 'changecat') ){
				
				$selected_PDFs = isset($_POST['bsk-pdf-manager-pdfs']) ? $_POST['bsk-pdf-manager-pdfs'] : '';
				$selected_PDFs_to_hidden = '';

				$url_cat_parameter = isset($_REQUEST['cat']) ? '&cat='.$_REQUEST['cat'] : '';
				$action_url = admin_url( 'admin.php?page='.self::$_bsk_pdfm_pro_pages['pdf'].$url_cat_parameter );
				?>
				<div class="wrap">
                <h2>Change Category</h2>
                <?php $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_pdf_bulk_change_category ); ?>
                <form id="bsk_pdf_manager_pdfs_change_category_form_id" method="post" action="<?php echo $action_url; ?>" enctype="multipart/form-data">
                <?php
				if( $selected_PDFs && is_array($selected_PDFs) && count($selected_PDFs) > 0 ){
					$selected_PDFs_to_hidden = implode(',', $selected_PDFs);
				?>
                <div>
                    <h4>Choose action to do</h4>
                    <p>
                    	<label>
                        	<input type="radio" name="bsk_pdf_manager_pdfs_change_category_action_to_do" value="add" checked="checked" class="bsk-pdf-manger-pdfs-change-category-action-radio" />Add
                        </label>
                        <label style="margin-left:10px;">
                        	<input type="radio" name="bsk_pdf_manager_pdfs_change_category_action_to_do" value="remove" class="bsk-pdf-manger-pdfs-change-category-action-radio" />Remove
                        </label>
                        <label style="margin-left:10px;">
                        	<input type="radio" name="bsk_pdf_manager_pdfs_change_category_action_to_do" value="update_to" class="bsk-pdf-manger-pdfs-change-category-action-radio" />Update to
                        </label>
                    </p>
                    <h4>Choose category to be managed</h4>
                    <p id="bsk_pdfm_batch_update_category_choose_error_message_ID" style="color: #FF0000;"></p>
                    <?php
                    echo BSKPDFM_Common_Backend::get_category_hierarchy_checkbox( 'bsk_pdf_manager_pdfs_categories_to_manager[]', 
                                                                                                                         'bsk-pdfm-bactch-update-category-checkbox',
                                                                                                                         NULL);
                    ?>
                </div>
                <?php
				}else{
					echo '<p>No PDF items choosen.</p>';
				}
				$_nonce = wp_create_nonce( 'bsk_pdf_manager_bulk_update_pdf_category_nonce' );
				?>
                <p>
				    <input type="hidden" name="_nonce" value="<?php echo $_nonce; ?>" />
                	<input type="hidden" name="bsk_pdf_manager_action" id="bsk_pdf_manager_action_id" value="" />
                    <input type="hidden" name="bsk_pdf_manager_pdf_items_id_hidden" value="<?php echo $selected_PDFs_to_hidden; ?>" />
                	<input type="button" class="button-primary" value="Cancel" id="bsk_pdf_manager_pdfs_categories_change_cancel_id" />
                    <?php if( $selected_PDFs && count($selected_PDFs) > 0 ){ ?>
                    <input type="button" class="button-primary" value="Submit" id="bsk_pdf_manager_pdfs_categories_change_submit_id" style="margin-left: 15px;" disabled />
					<?php } ?>
                </p>
                </form>
              </div>
              <?php
			}else{
				$bsk_pdfm_pdfs_list_page_url = admin_url( 'admin.php?page='.self::$_bsk_pdfm_pro_pages['pdf'] );
				
				$current_category_id = 0;
				if( isset($_REQUEST['cat']) ){
					$current_category_id = $_REQUEST['cat'];
					$current_category_id = intval( $current_category_id );
				}

				$add_new_page = add_query_arg( 'view', 'addnew', $bsk_pdfm_pdfs_list_page_url );
				if( $current_category_id ){
					$add_new_page = add_query_arg( 'cat', $current_category_id, $add_new_page );
					$bsk_pdfm_pdfs_list_page_url = add_query_arg( 'cat', $current_category_id, $bsk_pdfm_pdfs_list_page_url );
				}
				
                $_bsk_pdfm_OBJ_pdfs = new BSKPDFM_Dashboard_PDFs();
				//Fetch, prepare, sort, and filter our data...
				$_bsk_pdfm_OBJ_pdfs->prepare_items();
				echo '<div class="wrap">
						<div id="icon-edit" class="icon32"><br/></div>
						<h2>BSK PDF Documents<a href="'.$add_new_page.'" class="add-new-h2">Add New</a></h2>';
                $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_pdf );
				echo '<form id="bsk-pdf-manager-pdfs-form-id" method="post" action="'.$bsk_pdfm_pdfs_list_page_url.'">
							<input type="hidden" name="page" value="'.self::$_bsk_pdfm_pro_pages['pdf'].'" />';
							$_bsk_pdfm_OBJ_pdfs->search_box( 'search', 'bsk-pdf-manager-pdfs' );
							$_bsk_pdfm_OBJ_pdfs->views();
							$_bsk_pdfm_OBJ_pdfs->display();
				
                if( $current_category_id ){
                ?>
                    <p>Shortocde to show this category in list: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-category-ul id="<?php echo $current_category_id; ?>"]</span>, <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-by-category-in-list/" target="_blank">click here</a> for more prameters</p>
                    <p>Shortocde to show this category in columns: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-category-columns id="<?php echo $current_category_id; ?>" columns="2"]</span>, <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-by-category-in-columns/" target="_blank">click here</a> for more prameters</p>
                    <p>Shortocde to show this category in dropdown: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-category-dropdown id="<?php echo $current_category_id; ?>" target="_blank"]</span>, <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-by-category-in-dropdown/" target="_blank">click here</a> for more prameters</p>
                <?php
                }else{
                ?>
                    <p>Shortocde to show PDFs / Files in list: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-pdfs-ul id="1,2,3,4"]</span>, <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-specific-pdfs-in-list/" target="_blank">click here</a> for more prameters</p>
                    <p>Shortocde to show PDFs / Files in columns: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-pdfs-columns id="1,2,3,4" columns="2" target="_blank"]</span>, <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-specific-pdfs-in-columns/" target="_blank">click here</a> for more prameters</p>
                    <p>Shortocde to show PDFs / Files in dropdown: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-pdfs-dropdown id="1,2,3,4"  target="_blank"]</span>, <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-specific-pdfs-in-dropdown/" target="_blank">click here</a> for more prameters</p>
                <?php
                }
                
				$ajax_nonce = wp_create_nonce( 'bsk_pdf_manager_pdfs_page_ajax-oper-nonce' );
				echo '	<input type="hidden" id="bsk_pdf_manager_pdfs_page_ajax_nonce_ID" value="'.$ajax_nonce.'" />';
				echo '  </form>
					  </div>';
			}
		}else if ( $lists_curr_view == 'addnew' || $lists_curr_view == 'edit'){
			$pdf_id = -1;
			if(isset($_GET['pdfid']) && $_GET['pdfid']){
				$pdf_id = trim($_GET['pdfid']);
				$pdf_id = intval($pdf_id);
			}	
			echo '<div class="wrap">
					<div id="icon-edit" class="icon32"><br/></div>
					<h2>BSK PDF Document</h2>';
            $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_pdf );
			echo '<form id="bsk-pdf-manager-pdfs-form-id" method="post" enctype="multipart/form-data" action="'.admin_url( 'admin.php?page='.self::$_bsk_pdfm_pro_pages['pdf'] ).'">
					<input type="hidden" name="page" value="'.self::$_bsk_pdfm_pro_pages['pdf'].'" />';
					self::$_bsk_pdfm_OBJ_pdf->pdf_edit( $pdf_id );
			echo '<p style="margin-top:20px;"><input type="button" id="bsk_pdf_manager_pdf_save_form" class="button-primary" value="Save" /></p>'."\n";
			echo '</form>
				  </div>';
		}
	}
	
	function bsk_pdf_manager_settings_support(){
		global $current_user;
        ?>
        <div class="wrap" id="bsk_pdfm_setings_wrap_ID">
        	<div id="icon-edit" class="icon32"><br/></div>
			<h2>BSK PDF Settings & Support</h2>
        <?php
            $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_settings );
            self::$_bsk_pdfm_OBJ_settings->show_settings(); 
        ?>
        </div>
        <?php
	}
    
    function bsk_pdf_manager_help(){
		self::$_bsk_pdfm_OBJ_help->show_help();
	}
	
	function bsk_pdf_manager_pdfs_add_by_ftp_interface(){
		global $current_user;
		
		echo '<div class="wrap">
				<div id="icon-edit" class="icon32"><br/></div>
				<h2>BSK PDF Add by FTP</h2>';
        $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_add_by_ftp );
		echo '	<form id="bsk-pdf-manager-add-by-ftp-form-id" method="post" enctype="multipart/form-data">';
		echo '	<input type="hidden" name="page" value="'.self::$_bsk_pdfm_pro_pages['add_by_ftp'].'" />';
		
		self::$_bsk_pdfm_OBJ_ftp->bsk_pdf_manager_pdfs_add_by_ftp();
		
		echo '	</form>';
		
		echo '</div>';
	}
    
    function bsk_pdf_manager_show_pro_tip_box( $tips_array ){
        $tips = implode( ', ', $tips_array );
		$str = 
        '<div class="bsk-pro-tips-box">
			<b>Pro Tip: </b><span class="bsk-pro-tips-box-tip">'.$tips.' only supported in Pro version</span>
			<a href="'.BSKPDFManager::$url_to_upgrade.'" target="_blank">Upgrade to Pro</a>
		</div>';
		
		echo $str;
	}
}
