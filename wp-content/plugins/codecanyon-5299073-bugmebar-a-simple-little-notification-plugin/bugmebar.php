<?php
/*
    Plugin Name: BugMeBar
    Plugin URI: http://pluginhero.com/portfolio/bugmebar/
    Description: Another annoying notification bar plugin
    Version: 1.0.4
    Author: PluginHero
    Author URI: http://pluginhero.com/
 */

// include the wp-settings-api-bootstrap file
require_once 'lib/wp-settings-api-bootstrap/class.wp-settings-api-bootstrap.php';

// also load language files
load_plugin_textdomain('pluginhero', false, basename( dirname( __FILE__ ) ) . '/languages' );

if ( !class_exists( 'PluginHero_BugMeBar' ) ) :
    class PluginHero_BugMeBar {

        /**
         * @var WP_Settings_API_Bootstrap
         */
        private $wp_settings_api;

        /**
         * Constructor
         */
        function __construct() {
            $this->wp_settings_api = new WP_Settings_API_Bootstrap();

            add_action( 'admin_init', array( $this, 'admin_init') );
            add_action( 'admin_menu', array( $this, 'admin_menu') );
        }

        /**
         * Initialize the settings on admin_init hook
         */
        function admin_init() {

            //set the settings
            $this->wp_settings_api->set_sections( $this->get_settings_sections() );
            $this->wp_settings_api->set_fields( $this->get_settings_fields() );

            //initialize settings
            $this->wp_settings_api->admin_init();
        }

        /**
         * Add the menu on admin_menu hook
         */
        function admin_menu() {
            add_options_page( 'BugMeBar Settings', 'BugMeBar Settings', 'delete_posts', 'pluginhero_bugmebar_settings_options', array($this, 'plugin_page') );
        }

        /**
         * Set up all of the Main settings sections
         *
         * @return array
         */
        function get_settings_sections() {
            $sections = array(
                array(
                    'id' => 'pluginhero_bugmebar_settings_load',
                    'title' => __( 'The Settings', 'mytextdomain' )
                )
            );
            return $sections;
        }

        /**
         * Returns all the settings fields
         *
         * @return array settings fields
         */
        function get_settings_fields() {

            $settings_fields = array(
                'pluginhero_bugmebar_settings_load' => array(
                    array(
                        'name' => 'disabled',
                        'label' => __('Disabled', 'pluginhero'),
                        'desc' => __('Disable BugMeBar?', 'pluginhero'),
                        'type' => 'checkbox',
                        'default' => ''
                    ),
                    array(
                        'name' => 'homepage-only',
                        'label' => __('Homepage Only', 'pluginhero'),
                        'desc' => __('Only show BMB on the Homepage!', 'pluginhero'),
                        'type' => 'checkbox',
                        'default' => ''
                    ),
                    array(
                        'name' => 'exclude-posts',
                        'label' => __('Exclude from Posts', 'pluginhero'),
                        'desc' => __('A comma-separated list of Post Ids for which BMB should not be shown.', 'pluginhero'),
                        'type' => 'text',
                        'default' => ''
                    ),
                    array(
                        'name' => 'exclude-categories',
                        'label' => __('Exclude from Categories', 'pluginhero'),
                        'desc' => __('A comma-separated list of Category Ids for which BMB should not be shown.', 'pluginhero'),
                        'type' => 'text',
                        'default' => ''
                    ),
                    array(
                            'name' => 'target',
                            'label' => __( 'Target', 'pluginhero' ),
                            'desc' => __( 'Enter your target in here e.g. body or header .element or #element', 'pluginhero' ),
                            'type' => 'text',
                            'default' => 'body'
                        ),
                        array(
                            'name' => 'message',
                            'label' => __( 'Message', 'pluginhero' ),
                            'desc' => __( 'HTML will work in here too, but do not use double quotes, only single quotes e.g. \' not " :). If you have to use them use an escape character first e.g. "Quote" would be \"Quote\"', 'pluginhero' ),
                            'type' => 'textarea',
                            'default' => "Put your message here and a link if you want <a href='#'>This is a link wow!</a>"
                        ),
                        array(
                            'name' => 'message_font_size',
                            'label' => __( 'Message Font size', 'pluginhero' ),
                            'desc' => __( 'You can pixels, ems, rems, pts in here. Just enter your value and unit and you\'re good to go e.g. 17px', 'pluginhero' ),
                            'type' => 'text',
                            'default' => '17px'
                        ),
                        array(
                            'name' => 'use_website_font',
                            'label' => __( 'Use your website\'s font', 'pluginhero' ),
                            'desc' => __( 'By default we use Helvetica as it is bold and stands out, choose "Yes" to use your current web font instead.', 'pluginhero' ),
                            'type' => 'select',
                            'options' => array(
                                'false' => 'No',
                                'true' => 'Yes'
                            ),
                            'default' => 'false'
                        ),
                        array(
                            'name'      => 'message_colour',
                            'label'     => __( 'Message Colour', 'pluginhero' ),
                            'desc'      => '',
                            'type'      => 'colorpicker',
                            'default'   => '#ffffff'
                        ),
                        array(
                            'name'      => 'message_link_colour',
                            'label'     => __( 'Message Link Colour', 'pluginhero' ),
                            'desc'      => '',
                            'type'      => 'colorpicker',
                            'default'   => '#ffffff'
                        ),
                        array(
                            'name'      => 'message_link_hover_colour',
                            'label'     => __( 'Message Link Hover Colour', 'pluginhero' ),
                            'desc'      => '',
                            'type'      => 'colorpicker',
                            'default'   => '#34495e'
                        ),
                        array(
                            'name' => 'align',
                            'label' => __( 'Align message', 'pluginhero' ),
                            'desc' => '',
                            'type' => 'select',
                            'options' => array(
                                'left' => 'Left',
                                'right' => 'Right',
                                'center' => 'Center'
                            ),
                            'default' => 'left'
                        ),
                        array(
                            'name' => 'close',
                            'label' => __( 'Show or Hide close button', 'pluginhero' ),
                            'desc' => '',
                            'type' => 'select',
                            'options' => array(
                                'true' => 'Show',
                                'false' => 'Hide'
                            ),
                            'default' => 'true'
                        ),
                        array(
                            'name' => 'closeText',
                            'label' => __( 'Close Text', 'pluginhero' ),
                            'desc' => __( 'Enter the text you want for the close button e.g. X', 'pluginhero' ),
                            'type' => 'text',
                            'default' => 'X'
                        ),
                        array(
                            'name'      => 'close_colour',
                            'label'     => __( 'Close Colour', 'pluginhero' ),
                            'desc'      => '',
                            'type'      => 'colorpicker',
                            'default'   => '#ffffff'
                        ),
                        array(
                            'name'      => 'close_hover_colour',
                            'label'     => __( 'Close Hover Colour', 'pluginhero' ),
                            'desc'      => '',
                            'type'      => 'colorpicker',
                            'default'   => '#34495e'
                        ),
                        array(
                            'name' => 'closeAlign',
                            'label' => __( 'Align Close button', 'pluginhero' ),
                            'desc' => '',
                            'type' => 'select',
                            'options' => array(
                                'left' => 'Left',
                                'right' => 'Right'
                            ),
                            'default' => 'right'
                        ),
                        array(
                            'name' => 'closeRight',
                            'label' => __( 'Close button, distance from the right (when right aligned)', 'pluginhero' ),
                            'desc' => '',
                            'type' => 'text',
                            'default' => '20px'
                        ),
                        array(
                            'name' => 'closeLeft',
                            'label' => __( 'Close button, distance from the left (when left aligned)', 'pluginhero' ),
                            'desc' => '',
                            'type' => 'text',
                            'default' => '0px'
                        ),
                        array(
                            'name' => 'remember',
                            'label' => __( 'Remember closing with a Cookie?', 'pluginhero' ),
                            'desc' => __( "This will add a cookie to the user's browser so that computer doesn't load it on each page", 'pluginhero' ),
                            'type' => 'select',
                            'options' => array(
                                'true' => 'Yes',
                                'false' => 'No'
                            ),
                            'default' => 'true'
                        ),
                        array(
                            'name' => 'expireIn',
                            'label' => __( 'Cookie expires in', 'pluginhero' ),
                            'desc' => __( 'This is in days', 'pluginhero' ),
                            'type' => 'text',
                            'default' => '1'
                        ),
                        array(
                            'name' => 'cookieName',
                            'label' => __( 'Name your cookie', 'pluginhero' ),
                            'desc' => __( 'Entering a different name will mean cookie bar reappears for everyone', 'pluginhero' ),
                            'type' => 'text',
                            'default' => 'bugmebar'
                        ),
                        array(
                            'name' => 'minimum-width',
                            'label' => __( 'Minimum width to display on', 'pluginhero' ),
                            'desc' => __( 'If set to a value greater than 0, only show BMB when the page is at least this many pixels wide. E.g', 'pluginhero' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        array(
                            'name' => 'colours',
                            'label' => __( 'Show transitioning colours', 'pluginhero' ),
                            'desc' => __( "This is for CSS3 browsers only", 'pluginhero' ),
                            'type' => 'select',
                            'options' => array(
                                'true' => 'Yes',
                                'false' => 'No'
                            ),
                            'default' => 'true'
                        ),
                        array(
                            'name' => 'animate',
                            'label' => __( 'Bounce in/out Animation in on open and close', 'pluginhero' ),
                            'desc' => __( "This is for CSS3 browsers only", 'pluginhero' ),
                            'type' => 'select',
                            'options' => array(
                                'true' => 'Yes',
                                'false' => 'No'
                            ),
                            'default' => 'true'
                        ),
                        array(
                            'name' => 'fixed',
                            'label' => __( 'Fixed position bar', 'pluginhero' ),
                            'desc' => __( "This will fix BugMeBar to the top of the page", 'pluginhero' ),
                            'type' => 'select',
                            'options' => array(
                                'true' => 'Yes',
                                'false' => 'No'
                            ),
                            'default' => 'false'
                        ),
                        array(
                            'name' => 'fixedSpacer',
                            'label' => __( 'Fixed bar spacer', 'pluginhero' ),
                            'desc' => __( "Want to create a bit of space for a fixed bar to go in rather than overlay content? Set to Yes. Only use this in conjunction with the Fixed position option above.", 'pluginhero' ),
                            'type' => 'select',
                            'options' => array(
                                'true' => 'Yes',
                                'false' => 'No'
                            ),
                            'default' => 'false'
                        ),
                    	array(
                    	    'name' => 'zindex',
                    	    'label' => __( 'Z-index', 'pluginhero' ),
                    	    'desc' => __( 'Add another 9 if BugMeBar is showing underneath content', 'pluginhero' ),
                    	    'type' => 'text',
                    	    'default' => '99999'
                    	),
                    	array(
                            'name'      => 'colour_1',
                            'label'     => __( 'Colour 1', 'pluginhero' ),
                            'desc'      => __( 'This is the main colour of the banner and will be used if you turn the colour fader off', 'pluginhero' ),
                            'type'      => 'colorpicker',
                            'default'   => '#3498db'
                        ),
                        array(
                            'name'      => 'colour_2',
                            'label'     => __( 'Colour 2', 'pluginhero' ),
                            'desc'      => '',
                            'type'      => 'colorpicker',
                            'default'   => '#2ecc71'
                        ),
                        array(
                            'name'      => 'colour_3',
                            'label'     => __( 'Colour 3', 'pluginhero' ),
                            'desc'      => '',
                            'type'      => 'colorpicker',
                            'default'   => '#1abc9c'
                        ),
                        array(
                            'name'      => 'colour_4',
                            'label'     => __( 'Colour 5', 'pluginhero' ),
                            'desc'      => '',
                            'type'      => 'colorpicker',
                            'default'   => '#9b59b6'
                        ),
                        array(
                            'name'      => 'colour_5',
                            'label'     => __( 'Colour 5', 'pluginhero' ),
                            'desc'      => '',
                            'type'      => 'colorpicker',
                            'default'   => '#e74c3c'
                        ),
                        array(
                            'name'      => 'colour_6',
                            'label'     => __( 'Colour 6', 'pluginhero' ),
                            'desc'      => '',
                            'type'      => 'colorpicker',
                            'default'   => '#f39c12'
                        ),
                        array(
                            'name'      => 'colour_7',
                            'label'     => __( 'Colour 7', 'pluginhero' ),
                            'desc'      => '',
                            'type'      => 'colorpicker',
                            'default'   => '#e67e22'
                        ),
               )    
            );

            return $settings_fields;
        }
        
        /**
         * Display the admin page
         */
        function plugin_page() {
            echo '<div class="wrap">';
            echo '<div id="icon-options-general" class="icon32"></div>';
            echo '<h2>'. __( 'BugMeBar Settings' , 'pluginhero') . '</h2>';

            $this->wp_settings_api->show_navigation();
            $this->wp_settings_api->show_forms();

            echo '</div>';
        }

        /**
         * Get all the pages
         *
         * @return array page names with key value pairs
         */
        function get_pages() {
            $pages = get_pages();
            $pages_options = array();
            if ( $pages ) {
                foreach ($pages as $page) {
                    $pages_options[$page->ID] = $page->post_title;
                }
            }

            return $pages_options;
        }

    }
endif; // if class_exists

// initiate the class
$settings = new PluginHero_BugMeBar();


/* #######################################################################

	Load PluginHero Admin CSS

####################################################################### */

function pluginhero_plugin_admin_css()
{
	wp_register_style("pluginhero_plugin_admin",  plugin_dir_url(__FILE__) . "pluginhero_plugin_admin.css", false, false);
	wp_enqueue_style("pluginhero_plugin_admin");
}
add_action('admin_print_styles', 'pluginhero_plugin_admin_css',11);


/* #######################################################################

	Setup defaults on activation

####################################################################### */

function plugin_activate() {
  $setting_data = array(
  		'target'=> "body",
		'message'=> "Put your message here and a link if you want <a href='#'>This is a link wow!</a>",
		'close'=> "true",
		'closeText'=> "X",
		'closeAlign'=> "right",
		'align'=> "left",
		'remember'=> "true",
		'expireIn'=> 7,
		'cookieName'=> "bugmebar",
		'colours'=> "true",
		'animate'=> "true",
		'fixed'=> "false",
		'fixedSpacer'=> "false",
		'zindex'=> 99999,
		'colour_1'=> "#3498db",
		'colour_2'=> "#2ecc71",
		'colour_3'=> "#1abc9c",
		'colour_4'=> "#9b59b6",
		'colour_5'=> "#e74c3c",
		'colour_6'=> "#f39c12",
		'colour_7'=> "#e67e22",
		'message_colour'=> '#ffffff',
		'message_link_colour'=> '#ffffff',
		'message_link_hover_colour'=> '#34495e',
		'close_colour'=> '#ffffff',
		'close_hover_colour'=> '#34495e',
		'use_website_font'=> "false",
		'message_font_size'=> '17px',
		'closeRight'=> '20px',
		'closeLeft'=> '0'
	);
	
	add_option("pluginhero_bugmebar_settings_load", $setting_data);
}
 
register_activation_hook(__FILE__, 'plugin_activate');


/* #######################################################################

	Load Base CSS

####################################################################### */

function bugmebar_load_css() {
	wp_register_style("bugmebar",  plugin_dir_url(__FILE__) . "bugme.css",false,"1.0.4");
	wp_enqueue_style("bugmebar");
}
add_action( 'wp_enqueue_scripts', 'bugmebar_load_css' );


/* #######################################################################

	Add the scripts in the header

####################################################################### */

function bugmebar_load_js() {  
	wp_register_script("bugmebar", plugin_dir_url(__FILE__) . "jquery.bugme.min.js",array("jquery"), "1.0.4" );	
	wp_register_script("bugmebar-cookie", plugin_dir_url(__FILE__) . "jquery.cookie.min.js",array("jquery"), "1.0.4" );	
	wp_enqueue_script("bugmebar");	
	wp_enqueue_script("bugmebar-cookie");	
}

add_action('wp_enqueue_scripts', 'bugmebar_load_js');  


/* #######################################################################

	Load Front End JS in the footer

####################################################################### */


function bugmebar_output_js()
{

// Get BugMeBar options...
$pluginhero_bugmebar_output = get_option('pluginhero_bugmebar_settings_load');

    // start disabled checks 
    $disabled = isset($pluginhero_bugmebar_output['disabled']) && $pluginhero_bugmebar_output['disabled'] == 'on';

    // check if we are on the homepage and in homepage-only mode
    $disabled = $disabled || ( isset($pluginhero_bugmebar_output['homepage-only']) && $pluginhero_bugmebar_output['homepage-only'] == 'on' && !is_front_page() );


    // check if we are in a category which is excluded
    if(!$disabled && !is_front_page() && is_single())
    {
        $excludedCategories = "," . $pluginhero_bugmebar_output['exclude-categories'] . ",";

        if(strlen($excludedCategories) > 2)
        {
            $categories = get_the_category();

            if(count($categories > 0))
            {
                foreach($categories as $category)
                {
                    $disabled = $disabled || strpos($excludedCategories, "," . $category->cat_ID . ",") !== FALSE;
                }
            }
        }
    }

    // check if we are in a post which is excluded
    if(!$disabled && !is_front_page())
    {
        $excludedPosts = "," . $pluginhero_bugmebar_output['exclude-posts'] . ",";

        if(strlen($excludedPosts) > 2)
        {
            global $post;

            $disabled = $disabled || strpos($excludedPosts, "," . $post->ID . ",") !== FALSE;
        }
    }

    // and output if not disabled
    if(!$disabled)
    {
        // clean message of empty lines
        $bugmebar_message = $pluginhero_bugmebar_output['message'];
        $bugmebar_message = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "", $bugmebar_message);


        ?>

        <script>

        jQuery(document).ready(function () {
            jQuery('body').bugme({
                target: "<?php echo $pluginhero_bugmebar_output['target']; ?>",
                message: "<?php echo $bugmebar_message; ?>",
                align: "<?php echo $pluginhero_bugmebar_output['align']; ?>",
                close: <?php echo $pluginhero_bugmebar_output['close']; ?>,
                closeText: "<?php echo $pluginhero_bugmebar_output['closeText']; ?>",
                closeAlign: "<?php echo $pluginhero_bugmebar_output['closeAlign']; ?>",
                remember: <?php echo $pluginhero_bugmebar_output['remember']; ?>, 
                expireIn: <?php echo $pluginhero_bugmebar_output['expireIn']; ?>, 
                cookieName: "<?php echo $pluginhero_bugmebar_output['cookieName']; ?>", 
                colours: <?php echo $pluginhero_bugmebar_output['colours']; ?>, 
                animate: <?php echo $pluginhero_bugmebar_output['animate']; ?>, 
                fixed: <?php echo $pluginhero_bugmebar_output['fixed']; ?>, 
                fixedSpacer: <?php echo $pluginhero_bugmebar_output['fixedSpacer']; ?>, 
                zindex: <?php echo $pluginhero_bugmebar_output['zindex']; ?>,
                minimumWidth: <?php echo(!empty($pluginhero_bugmebar_output['minimum-width']) ? $pluginhero_bugmebar_output['minimum-width'] : "0"); ?> 
            });
        });

        </script>

        <?php
    }
}

add_action('wp_footer', 'bugmebar_output_js');




/* #######################################################################

	Load Front End Dynamic CSS

####################################################################### */

function bugmebar_output_css()
{

?>

<?php
	
// Get BugMeBar options...
$pluginhero_bugmebar_output = get_option('pluginhero_bugmebar_settings_load');
?>

<style>
/* BugMeBar Colour options */ 

.bugme {
	background: <?php echo $pluginhero_bugmebar_output['colour_1']; ?>;
	color: <?php echo $pluginhero_bugmebar_output['message_colour']; ?>;
	font-size: <?php echo $pluginhero_bugmebar_output['message_font_size']; ?>;
<?php if ( $pluginhero_bugmebar_output['use_website_font'] == 'false' ) { ?>
	font-family: "Helvetica Neue", Helvetica, Arial, Verdana, sans-serif;
<?php } ?>
}

.bugme-close {
	right: <?php echo $pluginhero_bugmebar_output['closeRight']; ?>;
}

.bugme-close.bugme-close-left {
	right: auto;
	left: <?php echo $pluginhero_bugmebar_output['closeLeft']; ?>;
}

.bugme a {
	color: <?php echo $pluginhero_bugmebar_output['message_link_colour']; ?>;
}

.bugme a:hover {
	color: <?php echo $pluginhero_bugmebar_output['message_link_hover_colour']; ?>;
}

.bugme a.bugme-close {
	color: <?php echo $pluginhero_bugmebar_output['close_colour']; ?>;
}

.bugme a.bugme-close:hover {
	color: <?php echo $pluginhero_bugmebar_output['close_hover_colour']; ?>;
}

	@-webkit-keyframes colour {
		0% { background-color: <?php echo $pluginhero_bugmebar_output['colour_1']; ?>; }
		15% { background-color: <?php echo $pluginhero_bugmebar_output['colour_2']; ?>; }
		28% { background-color: <?php echo $pluginhero_bugmebar_output['colour_3']; ?>; }
		41% { background-color: <?php echo $pluginhero_bugmebar_output['colour_4']; ?>; }
		53% { background-color: <?php echo $pluginhero_bugmebar_output['colour_5']; ?>; }
		65% { background-color: <?php echo $pluginhero_bugmebar_output['colour_6']; ?>; }
		78% { background-color: <?php echo $pluginhero_bugmebar_output['colour_7']; ?>; }
		90% { background-color: <?php echo $pluginhero_bugmebar_output['colour_2']; ?>; }
		100% { background-color: <?php echo $pluginhero_bugmebar_output['colour_1']; ?>; }
	}
	
	@-moz-keyframes colour {
		0% { background-color: <?php echo $pluginhero_bugmebar_output['colour_1']; ?>; }
		15% { background-color: <?php echo $pluginhero_bugmebar_output['colour_2']; ?>; }
		28% { background-color: <?php echo $pluginhero_bugmebar_output['colour_3']; ?>; }
		41% { background-color: <?php echo $pluginhero_bugmebar_output['colour_4']; ?>; }
		53% { background-color: <?php echo $pluginhero_bugmebar_output['colour_5']; ?>; }
		65% { background-color: <?php echo $pluginhero_bugmebar_output['colour_6']; ?>; }
		78% { background-color: <?php echo $pluginhero_bugmebar_output['colour_7']; ?>; }
		90% { background-color: <?php echo $pluginhero_bugmebar_output['colour_2']; ?>; }
		100% { background-color: <?php echo $pluginhero_bugmebar_output['colour_1']; ?>; }
	}
	
	@-ms-keyframes colour {
		0% { background-color: <?php echo $pluginhero_bugmebar_output['colour_1']; ?>; }
		15% { background-color: <?php echo $pluginhero_bugmebar_output['colour_2']; ?>; }
		28% { background-color: <?php echo $pluginhero_bugmebar_output['colour_3']; ?>; }
		41% { background-color: <?php echo $pluginhero_bugmebar_output['colour_4']; ?>; }
		53% { background-color: <?php echo $pluginhero_bugmebar_output['colour_5']; ?>; }
		65% { background-color: <?php echo $pluginhero_bugmebar_output['colour_6']; ?>; }
		78% { background-color: <?php echo $pluginhero_bugmebar_output['colour_7']; ?>; }
		90% { background-color: <?php echo $pluginhero_bugmebar_output['colour_2']; ?>; }
		100% { background-color: <?php echo $pluginhero_bugmebar_output['colour_1']; ?>; }
	}
	
	@-o-keyframes colour {
		0% { background-color: <?php echo $pluginhero_bugmebar_output['colour_1']; ?>; }
		15% { background-color: <?php echo $pluginhero_bugmebar_output['colour_2']; ?>; }
		28% { background-color: <?php echo $pluginhero_bugmebar_output['colour_3']; ?>; }
		41% { background-color: <?php echo $pluginhero_bugmebar_output['colour_4']; ?>; }
		53% { background-color: <?php echo $pluginhero_bugmebar_output['colour_5']; ?>; }
		65% { background-color: <?php echo $pluginhero_bugmebar_output['colour_6']; ?>; }
		78% { background-color: <?php echo $pluginhero_bugmebar_output['colour_7']; ?>; }
		90% { background-color: <?php echo $pluginhero_bugmebar_output['colour_2']; ?>; }
		100% { background-color: <?php echo $pluginhero_bugmebar_output['colour_1']; ?>; }
	}
	
	@keyframes colour {
		0% { background-color: <?php echo $pluginhero_bugmebar_output['colour_1']; ?>; }
		15% { background-color: <?php echo $pluginhero_bugmebar_output['colour_2']; ?>; }
		28% { background-color: <?php echo $pluginhero_bugmebar_output['colour_3']; ?>; }
		41% { background-color: <?php echo $pluginhero_bugmebar_output['colour_4']; ?>; }
		53% { background-color: <?php echo $pluginhero_bugmebar_output['colour_5']; ?>; }
		65% { background-color: <?php echo $pluginhero_bugmebar_output['colour_6']; ?>; }
		78% { background-color: <?php echo $pluginhero_bugmebar_output['colour_7']; ?>; }
		90% { background-color: <?php echo $pluginhero_bugmebar_output['colour_2']; ?>; }
		100% { background-color: <?php echo $pluginhero_bugmebar_output['colour_1']; ?>; }
	}

</style>

<?php
}

add_action('wp_head', 'bugmebar_output_css');

?>