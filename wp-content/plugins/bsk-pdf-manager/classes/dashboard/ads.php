<?php

class BSKPDFM_Dashboard_Ads {
	
	private static $_bsk_pdf_settings_page_url = '';
	private static $_bsk_plugin_support_center = 'http://www.bannersky.com/contact-us/';
	private static $_bsk_plugin_documentation_page = 'http://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/';
	   
	public function __construct() {
	}
	
	
    public static function show_other_plugin_of_gravity_forms_black_list(){
        $_free_url = 'https://wordpress.org/plugins/bsk-gravityforms-blacklist/';
        $_pro_url = 'https://www.bannersky.com/gravity-forms-blacklist/';
    ?>
    <div class="bsk-prdoucts-single">
        <h3>BSK GravityForms Blacklist</h3>
        <p>Built to help block submissions from users using spam data or competitors info to create new entry to your site. This plugin allows you to validate a field's value against the keywords and email addresses.</p>
        <ul style="list-style: square; list-style-position: inside;">
            <li>Blacklist, white list or Email list supported</li>
            <li>Custom validation message</li>
            <li>Block submitting or disable notifications</li>
            <li>Import items( keywords ) from CSV</li>
            <li>Export items ( keywords ) to CSV</li>
        </ul>
        <p class="bsk-prdoucts-single-center">
            <a class="bsk-prdoucts-single-link-button" href="<?php echo $_free_url; ?>" target="_blank">Free Version</a>
            <a class="bsk-prdoucts-single-link-button" href="<?php echo $_pro_url; ?>" target="_blank" style="margin-left: 20px;">Pro Version</a>
        </p>
    </div>
    <?php
	}
    
    public static function show_other_plugin_of_cf7_black_list(){
        $_free_url = 'https://wordpress.org/plugins/bsk-contact-form-7-blacklist/';
        $_pro_url = 'https://www.bannersky.com/contact-form-7-blacklist/';
    ?>
    <div class="bsk-prdoucts-single">
        <h3>BSK Contact Form 7 Blacklist</h3>
        <p>Built to help block submissions from users using spam data or competitors info to your site. This plugin allows you to validate a field's value against the keywords and email addresses.</p>
        <ul style="list-style: square; list-style-position: inside;">
            <li>Blacklist, white list or Email list supported</li>
            <li>Custom validation message</li>
            <li>Block submitting or skip mails</li>
            <li>Import items( keywords ) from CSV</li>
            <li>Export items ( keywords ) to CSV</li>
        </ul>
        <p class="bsk-prdoucts-single-center">
            <a class="bsk-prdoucts-single-link-button" href="<?php echo $_free_url; ?>" target="_blank">Free Version</a>
            <a class="bsk-prdoucts-single-link-button" href="<?php echo $_pro_url; ?>" target="_blank" style="margin-left: 20px;">Pro Version</a>
        </p>
    </div>
    <?php
	}
    
    public static function show_other_plugin_of_cf7_to_zoho(){
        $_free_url = 'https://wordpress.org/plugins/bsk-contact-form-7-to-zoho/';
        $_pro_url = 'https://www.bannersky.com/contact-form-7-to-zoho/';
    ?>
    <div class="bsk-prdoucts-single">
        <h3>BSK Contact Form 7 7 to Zoho</h3>
        <p>Use Zoho API 2.0 to integrate Contact Form 7 with your Zoho CRM and let you insert Lead, Contact, Account, Activity, Campaign record to your Zoho. Zoho has accounced that version 1.0 of Zoho CRM APIs are in the End-of-Life period and will be deprecated on Dec 31, 2018.So use this plugin DONOT need to worry about the suddenly stop of old forms to Zoho plugin.</p>
        <ul style="list-style: square; list-style-position: inside;">
            <li>Using Zoho API 2.0 to post form data to Zoho module as a record</li>
            <li>Download / Refresh module fields from Zoho</li>
            <li>A Contact Form 7 form may have multiple feeds ( mapping ). Only one feed for free version</li>
            <li>Multiple Contact Form 7 form fields map to a Zoho field</li>
            <li>Zoho custom fields, upload files to you Zoho as attachments</li>
            <li>Triggers for Approval, Workflow, Blueprint</li>
        </ul>
        <p class="bsk-prdoucts-single-center">
            <a class="bsk-prdoucts-single-link-button" href="<?php echo $_free_url; ?>" target="_blank">Free Version</a>
            <a class="bsk-prdoucts-single-link-button" href="<?php echo $_pro_url; ?>" target="_blank" style="margin-left: 20px;">Pro Version</a>
        </p>
    </div>
    <?php
	}
    
}