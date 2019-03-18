<?php
/*
Plugin Name: Electric Studio Download Counter
Plugin URI: http://www.electricstudio.co.uk
Description: Get Statistics on your Downloads
Version: 2.4
Author: Gabor Javorszky, Jon Walter, Leeroy Rose
License: GPL2
*/
// ini_set( 'display_errors', 1 );
/**
 * ESDC_Setup Class
 *
 * Handles installation and uninstallation
 * Assigns variables to be used throughout the plugin
 *
 * @author Gabor Javorszky based on James Irving-Swift
 *
 */

/* Runs when plugin is activated */
register_activation_hook( __FILE__, 'install' );

if( !function_exists('es_preit') ) {
    function es_preit( $obj, $echo = true ) {
        if( $echo ) {
            echo '<pre>';
            print_r( $obj );
            echo '</pre>';
        } else {
            return '<pre>' . print_r( $obj, true ) . '</pre>';
        }
    }
}

if( !function_exists('es_silent') ) {
    function es_silent( $obj ) {
          ?>
        <div style="display: none">
            <pre><?php print_r( $obj ); ?></pre>
        </div>
        <?php
    }
}



function install() {
    $a = new ESDC_Setup;
    $a->install();
}
class ESDC_Setup {
    /**
     * The table name. In case I ever want to change this. The variable is used in the rest of the code
     * so there should be no instances where half the code is changed, the other half isn't.
     */
    public $table_name;
    public $_db;
    public $_folder;
    function __construct() {
        global $wpdb;
        $this->_db = &$wpdb;
        $this->table_name = $this->_db->prefix . 'es_download_counter';
        $this->_folder = plugins_url( '', __FILE__ );
    }


    /**
     * Handles table creation. Uses the dbDelta() function of WordPress.
     *
     * dbDelta checks existing table structure, and adds / modifies table to match the structure
     * in the SQL statement. For more info see: http://codex.wordpress.org/Creating_Tables_with_Plugins
     *
     * Also adds preliminary options. The only options this plugin uses is the filetype associations.
     */
    function install() {
        $_tn = $this->table_name;
        $sql = "
            CREATE TABLE $_tn
            (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                download_name text NOT NULL,
                UNIQUE KEY id (id)
            );";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        add_option("esdc_file_types", array("pdf","doc","xls","docx","xlsx","csv"), '', 'yes');
        add_option("esdc_blocked_ips", array(""), '', 'yes');

    }


    /**
     * Drops the table and deletes the option.
     *
     */
    function remove() {
        $_tn = $this->table_name;
        $sql = "DROP TABLE IF EXISTS $_tn;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sq );
        delete_option('esdc_file_types');
        delete_option('esdc_blocked_ips');
    }
}
/**
 * Sets up the options page
 */
class ESDC_Options {
    /**
     * Attaches all the necessary bits to WordPress Hooks
     * The functions to create the options page
     * The functions to register the options the plugin uses
     * The ajax calls the plugin does for both search and register.
     */
    function __construct() {
        add_action('admin_menu', array(&$this,'create_options_page'));
        add_action('admin_init', array(&$this,'register_and_build_options'));

        add_action('wp_ajax_esdc_search_dates', array( $this, 'date_search' ) );
        add_action('wp_ajax_nopriv_esdc_search_dates', array( $this, 'date_search' ) );

        add_action('wp_ajax_esdc_addtocount', array( $this, 'count' ) );
        add_action('wp_ajax_nopriv_esdc_addtocount', array( $this, 'count' ) );
    }
    /**
     * Add the options page to the left menu
     * @return html WordPress' ownhtml
     */
    function create_options_page() {
        add_menu_page( 'Download Counter', 'Download Counter', 'install_plugins', __FILE__, array( $this, 'options_page' ) );
    }

    /**
     * Registers settings and adds the settings section and fields
     * @return html WordPress internals
     */
    function register_and_build_options() {
        /**
         * third argument is a callback that returns the sanitized values for the options
         * as per http://codex.wordpress.org/Function_Reference/register_setting
         */
        register_setting( 'esdc_options_group', 'esdc_file_types', array( $this, 'validate_file_types' ) );
        register_setting( 'esdc_options_group', 'esdc_blocked_ips', array( $this, 'validate_file_types' ) );

        add_settings_section( 'main_section', 'Download Settings', array( $this, 'section_callback' ), __FILE__ );

        add_settings_field( 'esdc_file_types_field', 'File Types: ', array( $this, 'file_types' ), __FILE__, 'main_section' );
        add_settings_field( 'esdc_blocked_ips_field', 'IP Addresses: ', array( $this, 'ip_addresses' ), __FILE__, 'main_section' );

    }

    /**
     * Adds sanitization, removes whitespace
     * @param  string $option the field value on the option page
     * @return string         the field value on the option page sans whitespace
     */
    function validate_file_types( $option ) {
        $option = explode( ',', $option );
        foreach( $option as $i => $op ) {
            $option[ $i ] = trim( $op );
        }
        return $option;
        // $derp = implode( ',', $option );
        // return $derp;
    }

    /**
     * Empty function to shut WordPress up.
     * We don't need it, but the presence of the function is needed.
     */
    function section_callback() {}

    /**
     * The function that outputs the HTML required for the options page / field that stores the file types to track
     * @return html the html on the option page
     */
    function file_types() {
        $option = get_option( 'esdc_file_types' );
        if(is_array($option)) {
            $option = implode(',',$option);
        }
        ?>
        <label for="esdc_file_types">
            <input type="text" id="esdc_file_types" name="esdc_file_types" value="<?php echo $option; ?>"> The types you want to track: eg. pdf,mp3,wma
        </label>
        <?php
    }

    /**
     * The function that outputs the HTML required for the options page / field that checks for blocked IP addresses
     * @return html the html on the option page
     */
    function ip_addresses() {
        $option = get_option( 'esdc_blocked_ips' );
        if(is_array($option)) {
            $option = implode(',',$option);
        }
        ?>
        <label for="esdc_blocked_ips">
            <input type="text" id="esdc_blocked_ips" name="esdc_blocked_ips" value="<?php echo $option; ?>"> The IP's you do not want to include in the count: eg. 001.02.03.4, 005.06.07.8
        </label>
        <?php
    }

    /**
     * The html of the whole options page.
     * This calls the setting section and settings fields
     * @return html The html of the settings page
     */
    function options_page() {
        ?>
        <div id="esdc-theme-options-wrap" class="wrap">
            <div class="icon32" id="icon-tools"><br></div>
            <h2>Electric Studio Download Counter</h2>
            <h2 class="nav-tab-wrapper">
                <a href="#" id="esdc-lastweek" class="nav-tab nav-tab-active">Last week</a>
                <a href="#" id="esdc-lastmonth" class="nav-tab">Last Month</a>
                <a href="#" id="esdc-topten" class="nav-tab">Top Ten</a>
                <a href="#" id="esdc-search" class="nav-tab">Search...</a>
                <a href="#" id="esdc-options" class="nav-tab">Options</a>
            </h2>

            <div id="tabbed">
                <section class="esdc-lastweek esdc-container active">
                    <h1>Last week's downloads</h1>
                    <?php
                    $lastweek = date( "Y-m-d H:i:s" ,mktime(0, 0, 0, date("m"), date("d")-7, date("Y")));
                    $this->populate_stats( $lastweek, current_time('mysql'));
                    ?>
                </section>
                <section class="esdc-lastmonth esdc-container">
                    <h1>Last month's downloads</h1>
                    <?php
                    $lastmonth = date( "Y-m-d H:i:s" ,mktime(0, 0, 0, date("m")-1, date("d"), date("Y")));
                    $this->populate_stats( $lastmonth, current_time('mysql'));
                    ?>
                </section>

                <section class="esdc-topten esdc-container">
                    <h1>Top 10 most downloaded files</h1>
                    <?php
                    $this->populate_stats( '', '', '', 10 );
                    ?>
                </section>

                <section class="esdc-search esdc-container">
                    <h1>Search interval</h1>
                    <div id="searchfield" class="curtime">
                        <label for="esdc_time_from" id="timestamp">From:
                            <input type="text" class="datepick_it" name="esdc_time_from" id="esdc_time_from">
                        </label>
                        <label for="esdc_time_to" id="timestamp">To:
                            <input type="text" class="datepick_it" name="esdc_time_to" id="esdc_time_to">
                        </label>
                        <input type="button" id="esdc-search" class="button submit" value="Search">
                    </div>
                    </form>
                    <div id="esdc-search-results">
                        <p>Results will appear here once you click the Search button and wait a bit.</p>
                    </div>
                </section>

                <section class="esdc-options esdc-container">
                    <h1>Plugin Options</h1>
                    <?php $this->options(); ?>
                </section>
            </div>
        </div>
        <p>Plugin Created By <a href="http://www.electricstudio.co.uk/2011/07/wordpress-download-counter-plugin/">Electric Studio</a> | Get Wordpress Hosting from <a href="http://www.electrichosting.co.uk">Electric Hosting</a></p>
        <?php
    }

    /**
     * Outputs the form needed for the options bit to work. Is called from the function called
     * 'options_page'
     * @return html WordPress internals.
     */
    function options() {
        ?>
        <form method="post" action="options.php">
            <?php
                do_settings_sections( __FILE__ );
                settings_fields( 'esdc_options_group' );
                submit_button();
            ?>
        </form>
        <?php
    }

    /**
     * Generates a table in html based on a query based on the arguments passed to it
     *
     * @param  string $from     Timestamp, optional. Is usually YYYY-MM-DD, will be passed to get_data
     * @param  string $to       Timestamp, optional. Is usually YYYY-MM-DD, will be passed to get_data
     * @param  string $filename The name of the file. Nuff said.
     * @param  integer $limit   Limit the number of results returned. Appended to the end of the SQL query as LIMIT 0, n
     * @return html             HTML Table
     */
    function populate_stats( $from = '', $to = '', $filename = '', $limit = '' ) {
        $d = new ESDC_Data();
        $results = $d->get_data($filename, $from, $to, $limit);
        ?>
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Download Count</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $oddEven = 'odd';
                foreach( $results as $result ) {
                    echo "<tr class=\"";
                    $oddEven = ( $oddEven == 'odd') ? 'even' : 'odd';
                    echo $oddEven;
                    echo "\">";
                    echo "<td>".$result['download_name']."</td><td class=\"esdc-result-count\">".$result['count']."</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <?php
    }


    /**
     * Handles AJAX request on the server side. Is called by the WordPress hook when someone clicks a link
     * that we're tracking
     * @return void nothing. Dies.
     */
    function count() {

        $user_ip        = $_SERVER['REMOTE_ADDR'];
        $blocked_ips    = get_option( 'esdc_blocked_ips' );

        if ( ! in_array($user_ip, $blocked_ips) ) {
            check_ajax_referer( 'esdc_count', 'cnonce' );
            $_db = new ESDC_Data;
            $id = $_db->add_to_count( $_REQUEST[ 'filename' ] );
            echo json_encode( array( 'id' => $id ) );
        }

        die();
    }

    /**
     * Handles AJAX request on the server side. Is called by the WordPress hook when someone searches for a
     * time range on the administration area.
     * @return void nothing. Dies.
     */
    function date_search() {
        check_ajax_referer( 'esdc_datesearch', 'snonce' );
        $this->populate_stats( $_REQUEST[ 'from' ], $_REQUEST[ 'to' ] );
        die();
    }
}

/**
 * Provides a way to interface with the data
 * SQL calls are implemented
 */
class ESDC_Data {
    private $s;

    /**
     * We need $s as a Setup to get the table name
     */
    function __construct() {
        $this->s = new ESDC_Setup;
    }

    /**
     * Adds a new row to the database with the current time
     * and the name of the file
     * @param string $filename the name of the file
     */
    function add_to_count( $filename ) {
        $data = array(
            'time' => current_time( 'mysql' ),
            'download_name' => $filename
        );
        $format = array( '%s', '%s' );
        $id = $this->s->_db->insert( $this->s->table_name, $data, $format );
        return $id;
    }

    /**
     * Gets the count of how many times a certain file has been downloaded
     * It works it out by requesting the filename and optionally the time
     * constaints, and returns the array, which then can be counted, thus
     * yielding what we want
     *
     * @param  string $filename the file name in question
     * @param  string $from     optional timestamp to compare against
     * @param  string $to       optional timestamp to compare against
     * @return associative array           results in an ARRAY_A
     */
    function get_count( $filename, $from = '', $to = '' ) {
        if( $to == '' ) {
            $to = current_time( 'mysql' );
        } else {
            $to = $to . ' 23:59:59';
        }
        if( $from == '' ) { $from = '0000-00-00 00:00:00'; }

        $_tn = $this->s->table_name;
        $_db = $this->s->_db;
        $prepped_sql = $_db->prepare(
            "SELECT count(id) as count FROM $_tn WHERE download_name = '%s' AND time BETWEEN '%s' AND '%s'",
            array( $filename, $from, $to )
        );

        $results = $_db->get_results( $prepped_sql, ARRAY_A );
        return $results;
    }

    /**
     * Gets the data (download count) by filename
     * @param  string $filename the name of the file in question
     * @param  string $from     optional timestamp to compare against
     * @param  string $to       optional timestamp to compare against
     * @param  integer $limit    A number, actually
     * @return associative array           results in an ARRAY_A
     */
    function get_data( $filename = '', $from = '', $to = '', $limit = '' ) {
        $_db = $this->s->_db;
        $_tn = $this->s->table_name;
        $data = array();
        if( $to == '' ) {
            $to = current_time( 'mysql' );
        } else {
            $to = $to . ' 23:59:59';
        }
        $from = ( $from == '' ) ? $from = '0000-00-00 00:00:00' : $from;

        // Kickoff the sql
        $_sql = "SELECT count(id) AS count, download_name FROM $_tn WHERE 1";

        // construct it even further (are we interested in one file, or all of them?)
        if( $filename != '' ) {
            $_sql .= " AND download_name = '%s'";
            $data[] = $filename;
        }

        // add times and grouping / ordering
        $_sql .= " AND time BETWEEN '%s' AND '%s' GROUP BY download_name ORDER BY count DESC";
        $data[] = $from;
        $data[] = $to;

        // are we interested in everything, or the top 10 for example?
        if( $limit != '' ) {
            $_sql .= " LIMIT 0, %d";
            $data[] = $limit;
        }
        $results = $_db->get_results( $_db->prepare( $_sql, $data ), ARRAY_A );
        return $results;
    }
}

/**
 * The Widget Class. Allows the widget to appear on the backend
 * and settings set
 * and appear on the frontend.
 */
class ESDC_Widget extends WP_Widget {

    private $_setup;
    private $_db;

    public function __construct() {
        $this->_setup = new ESDC_Setup;
        $this->_db = new ESDC_Data;
        parent::__construct(
            'esdc_top_download_widget', // Base ID
            'Top Downloads', // Name
            array(
                'description' => __( 'Top downloads', '' ),
                'title' => __( 'Top Downloads', '' ),
                'show_count' => true
            )
        );
    }

    /**
     * Displays the widget on the frontend.
     * @param  array $args     Arguments (?)
     * @param  array $instance saved options of the widget
     * @return html           generated html based on stuff
     */
    public function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', $instance[ 'title' ] );
        echo $before_widget;
        if( !empty( $title ) ) {
            echo $before_title . $title . $after_title;
        }
        $downloads = $this->_db->get_data( '', '', '', $instance[ 'limit' ] );
        echo '<ul>';
        foreach( $downloads as $d ) {
            echo '<li>';
            echo $d[ 'download_name' ];
            if( $instance[ 'show_count' ] ) {
                echo " (". $d[ 'count' ] . ")";
            }
            echo '</li>';
        }
        echo '</ul>';
        echo $after_widget;
    }

    /**
     * Function to handle updating of data. Sanitization is there. Because
     * limit needs to be a positive integer, I need to make sure users don't
     * suddenly break the SQL statement and end up with something stupid.
     * @param  array $new_instance The new data that is being saved
     * @param  array $old_instance The old data we are writing over
     * @return array               The new data, now sanitized
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );

        $instance[ 'limit' ] = strip_tags( $new_instance[ 'limit' ] );
        if( is_numeric( $instance[ 'limit' ] ) ) {
            $_int = intval( $instance[ 'limit' ] );
            if( $_int > 0 ) {
                $instance[ 'limit' ] = $_int;
            } else {
                $instance[ 'limit' ] = 10;
            }
        } else {
            $instance[ 'limit' ] = 10;
        }
        $instance[ 'show_count' ] = strip_tags( $new_instance[ 'show_count' ] );
        return $instance;
    }

    /**
     * Rewriting the form for the widget settings on the backend
     * @param  array $instance The settings of the widget
     * @return html           The html needed for the options page
     */
    public function form( $instance ) {
        $_t = $this->get_field_id( 'title' );
        $_tn = $this->get_field_name( 'title' );

        $_l = $this->get_field_id( 'limit' );
        $_ln = $this->get_field_name( 'limit' );

        $_sc = $this->get_field_id( 'show_count' );
        $_scn = $this->get_field_name( 'show_count' );

        if( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        } else {
            $title = __( 'Top Downloads', 'text_domain' );
        }

        if( isset( $instance[ 'limit' ] ) ) {
            $limit = $instance[ 'limit' ];
        } else {
            $limit = 10;
        }

        if( isset( $instance[ 'show_count' ] ) ) {
            $sc = $instance[ 'show_count' ];
        } else {
            $sc = true;
        }
        $checked = ( $sc ) ? ' checked="checked"' : '';
        ?>
        <p>
            <label for="<?php echo $_t; ?>"><?php _e( 'Title:' ); ?></label>
            <input type="text" class="widefat" id="<?php echo $_t; ?>" name="<?php echo $_tn; ?>" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $_l; ?>"><?php _e( 'Number of Files to see:' ); ?></label>
            <input type="text" class="widefat" id="<?php echo $_l; ?>" name="<?php echo $_ln; ?>" value="<?php echo esc_attr( $limit ); ?>">
            Non-numbers will be converted to 10. Negative numbers will be converted to 10. Fractions will be converted to their integer values (773.8872 => 773).
        </p>
        <p>
            <label for="<?php echo $_sc; ?>"><?php _e( 'Show Count:' ); ?></label>
            <input type="checkbox" class="widefat" id="<?php echo $_sc; ?>" name="<?php echo $_scn; ?>" value="true"<?php echo $checked; ?>>
        </p>
        <?php
    }
}

/**
 * Everything is run by this thing. The encapsulating class that calls everything else.
 * Think of it as the conductor.
 */
class ESDC {
    public $set;
    public $count_nonce;
    public $data;
    public $datesearch_nonce;
    public $pdir;
    private $ajax;

    /**
     * Spawns all the necessary classes and their constructors.
     */
    function __construct() {
        // So that we access table name and whatnot
        $this->set = new ESDC_Setup;

        // To be able to hook in custom js files
        $this->pdir = $this->set->_folder;

        // To build the options pages
        $this->optionpage = new ESDC_Options;

        // Load them javascripts
        if( is_admin() ) {
            add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
        } else {
            add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
        }
        add_shortcode( 'downloadcount', array( $this, 'download_count_shortcode' ) );

    }

    /**
     * Function for the shortcode
     * @param  array $atts Arguments passed to the shorcode
     * @return html       html
     */
    function download_count_shortcode( $atts ) {
        extract( shortcode_atts( array( 'link' => '' ), $atts ) );
        $d = new ESDC_Data;
        if( $link != '' ) {
            $_c = $d->get_data( $link );
            return $_c[ 0 ][ 'count' ];

        } else {
            return 'No count available';
        }
    }

    // generate nonces
    function nonces() {
        $this->count_nonce = wp_create_nonce( 'esdc_count' );
        $this->datesearch_nonce = wp_create_nonce( 'esdc_datesearch' );
    }

    /**
     * Loading javascripts.
     * jQuery is builtin
     * jQueryUI is builtin
     * esdc-js is the mastermind javascript
     * esdc-css is the css for the datepicker (grabbed it from jqueryui.com)
     * esdc-css-main is the css for the administration area
     *
     * @return script tags in the body because all of these need to be included
     */
    function load_scripts() {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-datepicker', false, array( 'jquery' ) );
        wp_register_script( 'esdc-js', $this->pdir . '/js/esdc.js', 'jquery-ui-datepicker' );
        // wp_register_script( 'esdc-js', $this->pdir . '/js/esdc-ck.js', 'jquery-ui-datepicker' );
        wp_enqueue_script( 'esdc-js' );
        wp_register_style( 'esdc-css', $this->pdir . '/css/esdc-dp.css' );
        wp_enqueue_style( 'esdc-css' );
        wp_register_style( 'esdc-css-main', $this->pdir . '/css/esdc-custom.css' );
        wp_enqueue_style( 'esdc-css-main' );

        $this->count_nonce = wp_create_nonce( 'esdc_count' );
        $this->datesearch_nonce = wp_create_nonce( 'esdc_datesearch' );



        $options = get_option( 'esdc_file_types' );

        // And while we're at it, let's make some variables accessible to javascript. Because we can.
        $data = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'count_nonce' => $this->count_nonce,
            'ds_nonce' => $this->datesearch_nonce,
            'tracked' => json_encode( $options )
        );

        wp_localize_script( 'esdc-js', 'ESDC_JS', $data );
    }
}

add_action( 'widgets_init', 'register_esdc_widgets' );
function register_esdc_widgets() {
    register_widget( 'ESDC_Widget' );
}



$aj = new ESDC;
