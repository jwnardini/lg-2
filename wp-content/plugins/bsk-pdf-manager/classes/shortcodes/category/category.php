<?php

class BSKPDFM_Shortcodes_Category {

    public $_category_OBJ_dropdown = NULL;
    public $_category_OBJ_ul_ol = NULL;
    public $_category_OBJ_columns = NULL;
    
	public function __construct() {
        require_once( 'category-functions.php' );
        
        require_once( 'category-dropdown.php' );
        require_once( 'category-ul-ol.php' );
        
        $this->_category_OBJ_dropdown = new BSKPDFM_Shortcodes_Category_Dropdown();
        $this->_category_OBJ_ul_ol = new BSKPDFM_Shortcodes_Category_UL_OL();
	}
}