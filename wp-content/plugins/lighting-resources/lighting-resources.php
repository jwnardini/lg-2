<?php
/**
 * Plugin Name: Lighting Resources
 * Version: 1.0
 * Description: A plugin for registering the 'Resource' post type and associated meta information
 * Author: Casey Driscoll at CHIEF
 * Author URI: casey.driscolL@agencychief.com
 * Plugin URI: https://agencychief.com
 * Text Domain: lighting-resources
 * Domain Path: /languages
 * @package Lighting-resources
 */


include_once 'class.cpt.resource.php';
include_once 'class.tax.resource.php';

Resource_CPT::init();
Resource_TAX::init();