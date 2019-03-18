<?php

class BSKPDFM_Shortcodes_PDFs {
    public $_pdfs_OBJ_dropdown = NULL;
    public $_pdfs_OBJ_ul_ol = NULL;
    public $_pdfs_OBJ_columns = NULL;
    
	public function __construct() {
        
        require_once( 'pdfs-dropdown.php' );
        require_once( 'pdfs-ul-ol.php' );
        
        $this->_pdfs_OBJ_dropdown = new BSKPDFM_Shortcodes_PDFs_Dropdown();
        $this->_pdfs_OBJ_ul_ol = new BSKPDFM_Shortcodes_PDFs_UL_OL();
	}
}