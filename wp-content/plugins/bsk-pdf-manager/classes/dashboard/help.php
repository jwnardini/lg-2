<?php

class BSKPDFM_Dashboard_Help {
	
	private static $_bsk_pdf_settings_page_url = '';
	private static $_bsk_plugin_support_center = 'http://www.bannersky.com/contact-us/';
	private static $_bsk_plugin_documentation_page = 'http://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/';
	   
	public function __construct() {
	}
	
	function show_help(){
        ?>
        <div class="wrap">
            <div style="width: 70%; float: left;">
                <div class="wrap" id="bsk_pdfm_help_wrap_ID">
                    <h2 class="nav-tab-wrapper">
                            <a class="nav-tab nav-tab-active" href="javascript:void(0);" id="bsk_pdfm_help_tab-quick-start">Quick Start</a>
                            <a class="nav-tab" href="javascript:void(0);" id="bsk_pdfm_help_tab-plugin-documentation">Plugin Documentation</a>
                        <a class="nav-tab" href="javascript:void(0);" id="bsk_pdfm_help_tab-demos">Demos</a>
                            <a class="nav-tab" href="javascript:void(0);" id="bsk_pdfm_help_tab-pugin-support">Plugin Support Centre</a>
                    </h2>
                    <div id="bsk_pdfm_help_tab_content_wrap_ID">
                        <section><?php $this->show_quick_start(); ?></section>
                        <section><?php $this->show_plugin_documentaiton(); ?></section>
                        <section><?php $this->show_demos(); ?></section>
                        <section><?php $this->show_plugin_support(); ?></section>
                    </div>
                </div>
            </div>
            <div style="width: 28%; float: left;">
                <div class="wrap" id="bsk_pdfm_help_other_product_wrap_ID">
                    <h2>&nbsp;</h2>
                    <div>
                        <?php BSKPDFM_Dashboard_Ads::show_other_plugin_of_cf7_to_zoho(); ?>
                    </div>
                    <div style="margin-top: 20px;">
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
    
    function show_quick_start(){
        ?>
        <h4>Display PDFs in list</h4>
        <ul>
            <li>Display all PDFs in unordered list in date descending order and open PDF in new window: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-pdfs-ul id="ALL" order_by="date" order="DESC" target="_blank"]</span></li>
            <li>Display specific PDFs in ordered list in id sequence order: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-pdfs-ol id="1,2,3,4,5,6"]</span></li>
            <li>&nbsp;</li>
            <li><span style="font-style: italic;">for more shortcode attributes, check from <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-specific-pdfs-in-list/" target="_blank">https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-specific-pdfs-in-list/</a></span></li>
        </ul>
        <h4>Display PDFs in dropdown</h4>
        <ul>
            <li>Display all PDFs in dropdown in date descending order and open PDF in new window: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-pdfs-dropdown id="all"]</span></li>
            <li>&nbsp;</li>
            <li><span style="font-style: italic;">for more shortcode attributes, check from <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-specific-pdfs-in-dropdown/" target="_blank">https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-specific-pdfs-in-dropdown/</a></span></li>
        </ul>
        <div class="bsk-pro-tips-box" style="text-align: left;">
            <h4>Display PDFs in columns</h4>
            <ul>
                <li>Display all PDFs in two columns in date descending order: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-pdfs-columns id="all" columns="2" order_by="date" order="DESC"]</span></li>
            </ul>
            <p>
                <b>Pro Tip: </b><span class="bsk-pro-tips-box-tip">the feature only supported in Pro version</span>
                <a href="<?php echo BSKPDFManager::$url_to_upgrade; ?>" target="_blank">Upgrade to Pro</a>
            </p>
        </div>
        <br />
        <hr />
        <h4 style="margin-top: 40px;">Display PDFs in list by Category</h4>
        <ul>
            <li>Display all PDFs under category of id = 61 in unordered list in date descending order: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-category-ul id="61" show_cat_title="yes" order_by="date" order="DESC"]</span></li>
            <li>&nbsp;</li>
            <li><span style="font-style: italic;">for more shortcode attributes, check from <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-by-category-in-list/" target="_blank">https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-by-category-in-list/</a></span></li>
        </ul>
        <h4>Display PDFs in dropdown</h4>
        <ul>
            <li>Display all PDFs under category of id = 25 and 61 in dropdown in date descending order and open PDF in new window: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-category-dropdown id="25,61"]</span></li>
            <li>&nbsp;</li>
            <li><span style="font-style: italic;">for more shortcode attributes, check from <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-by-category-in-dropdown/" target="_blank">https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-by-category-in-dropdown/</a></span></li>
        </ul>
        <div class="bsk-pro-tips-box" style="text-align: left;">
            <h4>Display PDFs in columns</h4>
            <ul>
                <li>Display all PDFs in two columns in date descending order: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-pdfs-columns id="all" columns="2" order_by="date" order="DESC"]</span></li>
            </ul>
            <p>
                <b>Pro Tip: </b><span class="bsk-pro-tips-box-tip">the feature only supported in Pro version</span>
                <a href="<?php echo BSKPDFManager::$url_to_upgrade; ?>" target="_blank">Upgrade to Pro</a>
            </p>
        </div>
        <?php
    }
	
    function show_plugin_documentaiton(){
    ?>
    <h4>Upgrade</h4>
    <ul>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/how-to-upgrade-to-pro-version/"  title="How to upgrade to Pro version" target="_blank">How to upgrade to Pro version</a></li>
    </ul>
    <h4>Settings</h4>
    <ul>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/hierarchical-category/"  title="Hierarchical Category" target="_blank">Hierarchical Category</a></li>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/featured-image-settings/"  title="Featured image settings" target="_blank">Featured image settings</a></li>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/pdf-orders-title-date-custom-last-modified/"  title="PDF orders - title - date - custom-last modified" target="_blank">PDF orders - title - date - custom-last modified</a></li>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/multiple-columns-settings/"  title="Multiple Columns Settings" target="_blank">Multiple Columns Settings</a></li>
    </ul>
    <h4>Display specific / all PDFs</h4>
    <ul>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-specific-pdfs-in-list/"  title="Display specific PDFs in list" target="_blank">Display specific PDFs in list</a></li>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-specific-pdfs-in-columns/"  title="Display specific PDFs in columns" target="_blank">Display specific PDFs in columns</a></li>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-specific-pdfs-in-dropdown/"  title="Display specific PDFs in dropdown" target="_blank">Display specific PDFs in dropdown</a></li>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/get-specific-pdfs-link-only/"  title="Get specific PDFs' link only" target="_blank">Get specific PDFs' link only</a></li>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/get-specific-pdfs-url-only/"  title="Get specific PDFs' URL only" target="_blank">Get specific PDFs' URL only</a></li>
    </ul>
    <h4>Display PDFs by category</h4>
    <ul>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-by-category-in-list/"  title="Display PDFs by category in list" class="bsk-tools-current-link" target="_blank">Display PDFs by category in list</a></li>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-by-category-in-columns/"  title="Display PDFs by category in columns" target="_blank">Display PDFs by category in columns</a></li>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-by-category-in-dropdown/"  title="Display PDFs by category in dropdown" target="_blank">Display PDFs by category in dropdown</a></li>
    </ul>
    <h4>Display PDFs with category selector</h4>
    <ul>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-with-category-selector-in-list/"  title="Display PDFs with category selector in list" target="_blank">Display PDFs with category selector in list</a></li>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-with-category-selector-in-columns/"  title="Display PDFs with category selector in columns" target="_blank">Display PDFs with category selector in columns</a></li>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-with-category-selector-in-dropdown/"  title="Display PDFs with category selector in dropdown" target="_blank">Display PDFs with category selector in dropdown</a></li>
    </ul>
    <h4>Filters and Hooks</h4>
    <ul>
        <li><a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/filters-and-hooks/"  title="Filters and Hooks" target="_blank">Filters and Hooks</a></li>
    </ul>
    <?php
    }
    
    function show_plugin_support(){
    ?>
    <ul>
        <li><a href="<?php echo self::$_bsk_plugin_support_center; ?>" target="_blank">Visit the Support Centre</a> if you have a question on using this plugin</li>
    </ul>
    <?php
    }
    
    function show_demos(){
        $demo_site_url = 'https://demo.bannersky.com/';
    ?>
    <div class="bsk-pro-tips-box" style="text-align: left;">
        <p>
            <span class="bsk-pro-tips-box-tip">Demos are using Pro version, to get all features please</span>
            <a href="<?php echo BSKPDFManager::$url_to_upgrade; ?>" target="_blank">Upgrade to Pro</a>
        </p>
    </div>

    <h2 style="margin-top:40px;padding-bottom:10px; border-bottom: 1px solid;">Display All / Specific PDFs</h2>
    <h4>Display in unordered / ordered list</h4>
    <ul>
        <li><a href="<?php echo $demo_site_url; ?>display-all-specific-pdfs/all-pdfs-in-unordered-list-in-date-descending-order-open-in-new-window-with-pagination/" target="_blank">All PDFs, unordered list, date descending order</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-all-specific-pdfs/all-pdfs-in-unordered-list-with-date-in-title-and-date-filter/" target="_blank">All PDFs, unordered list, date in title, date filter</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-all-specific-pdfs/all-pdfs-in-unordered-list-with-search-bar-and-date-weekday-filter/" target="_blank">All PDFs, unordered list, weekday in title, date filter, search bar</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-all-specific-pdfs/all-pdfs-by-year-with-weekday-filter/" target="_blank">All PDFs, unordered list, weekday filter, specific year of</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-all-specific-pdfs/all-pdfs-in-unordered-list-with-featured-image/" target="_blank">All PDFs, unordered list, with featured image, ID ascending order</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-all-specific-pdfs/specific-pdfs-in-ordered-list-with-featured-image-and-description/" target="_blank">Specific PDFs, ordered list, featured image, PDF description</a></li>

        <h4>Display in columns</h4>
        <li><a href="<?php echo $demo_site_url; ?>display-all-specific-pdfs/all-pdfs-in-columns-with-featured-image-with-exclude-ids/" target="_blank">All PDFs, in columns, featured image, date weekday filter, search bar, pagination, exclude specific PDFs by id</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-all-specific-pdfs/specific-pdfs-in-columns-with-featured-image-and-description/" target="_blank">Specific PDFs, in columns, featured image, PDF description, title above featured image</a></li>

        <h4>Display in dropdown</h4>
        <li><a href="<?php echo $demo_site_url; ?>display-all-specific-pdfs/all-pdfs-in-dropdown-with-exclude-ids/" target="_blank">All PDFs, in dropdow, date weekday filter, exclude specific PDFs by id</a></li>
    </ul>
    <h2 style="margin-top:40px;padding-bottom:10px; border-bottom: 1px solid;">Display PDFs by Category</h2>
    <h4>Display in unordered / ordered list</h4>
    <ul>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-by-category/display-pdfs-by-category-in-unordered-list-with-pagination/" target="_blank">Single or multiple categories, unordered list, pagination, date in PDF title</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-by-category/display-pdfs-by-category-in-unordered-list-with-category-title-description-and-pagination/" target="_blank">Single or multiple categories, show category title, show category description, unordered list, pagination, date in PDF title</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-by-category/display-pdfs-by-category-in-unordered-list-with-search-bar-date-weekday-filter/" target="_blank">Multiple categories, unordered list, pagination, date in PDF title, search bar, date weekday filter</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-by-category/display-pdfs-by-category-with-hierarchy-sub-categories-enabled-in-unordered-list/" target="_blank">Single category with hierarchy ( sub categories ), unordered list, pagination, date in PDF title</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-by-category/display-pdfs-by-category-with-password-required/" target="_blank">Single category, password protected, unordered list, pagination, date in PDF title, search bar, date weekday filter</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-by-category/specific-categories-in-unordered-list-with-featured-image-search-bar-date-weekday-filter/" target="_blank">Specific categories, default featured image, password protected, unordered list, pagination, date in PDF title, search bar, date weekday filter</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-by-category/all-category-in-ordered-list-with-featured-image-search-bar-date-weekday-filter/" target="_blank">All category, featured image place on right, password protected, unordered list, pagination, date in PDF title, search bar, date weekday filter</a></li>
    </ul>

    <h4>Display in columns</h4>
    <ul>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-by-category/all-category-in-columns-with-featured-image-search-bar-date-weekday-filter/" target="_blank">All category, three columns, featured image, password protected, pagination, date in PDF title, search bar, date weekday filter</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-by-category/all-category-in-columns-with-title-below-featured-image-search-bar-date-weekday-filter/" target="_blank">All category, two columns, featured image above PDF title, password protected, pagination, date in PDF title, search bar, date weekday filter</a></li>

        <h4>Display in dropdown</h4>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-by-category/specific-categories-with-hierarchy-in-dropdown-date-weekday-filter/" target="_blank">Sing category / Multiple categories, in dropdown, date weekday filter</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-by-category/all-categories-with-hierarchy-in-dropdown-date-weekday-filter/" target="_blank">All categories, in dropdown, date weekday filter, category title as option group, password protected</a></li>
    </ul>

    <h2 style="margin-top:40px;padding-bottom:10px; border-bottom: 1px solid;">Display PDFs with Category Selector</h2>
    <h4>Display in unordered / ordered list</h4>
    <ul>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-with-category-selector/all-categories-in-dropdown-as-selector-change-to-load-pdfs-by-category-in-unordered-list/" target="_blank">All categories with hierarchy, Exclude categories by ID, PDFs in unordered list, Search bar, Date weekday filter</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-with-category-selector/specific-categories-in-dropdown-as-selector-load-pdfs-by-category-in-ordered-list-with-default-category/" target="_blank">Specific categories with hierarchy, Default category loaded initial, PDFs in ordered list, Search bar, Date weekday filter</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-with-category-selector/all-categories-in-dropdown-as-selector-pdfs-with-featured-image-in-unordered-list/" target="_blank">All categories with hierarchy, Exclude categories by ID, PDFs in unordered list, Featured image on left, Show PDF title and descriptionSearch bar, Date weekday filter</a></li>
    </ul>

    <h4>Display in columns</h4>
    <ul>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-with-category-selector/all-categories-in-selector-pdfs-with-featured-image-in-columns/" target="_blank">All categories with hierarchy, Exclude categories by ID, Default category loaded initial, PDFs in two columns, Featured image above title, Show PDF title and description, Search bar, Date weekday filter</a></li>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-with-category-selector/specific-categories-in-selector-pdfs-with-featured-image-in-columns/" target="_blank">Specific categories with hierarchy, Default category loaded initial, PDFs in three columns, Featured image above title, Show PDF title and description, Search bar, Date weekday filter</a></li>

        <h4>Display in dropdown</h4>
        <li><a href="<?php echo $demo_site_url; ?>display-pdfs-with-category-selector/all-categories-in-selector-pdfs-in-columns-date-weekday-filter-on-right/" target="_blank">All categories with hierarchy, Exclude categories by ID, Default category loaded initial, PDFs in dropdown, Date weekday filter</a></li>
    </ul>

    <?php
    }
}