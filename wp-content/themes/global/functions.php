<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/wrapper.php',   // Theme wrapper class
  'lib/customizer.php' // Theme customizer
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

function pre( $out ) {
  echo '<pre>' . print_r( $out, 1 ) . '</pre>';
  // die();
}


add_action( 'init', 'create_manufacturers_tax' );

function create_manufacturers_tax() {
    register_taxonomy(
        'manufacturers',
        'product',
        array(
            'label' => __( 'Manufacturers' ),
            'rewrite' => 'false',
            'hierarchical' => true,
        )
    );
}

/**
* Allow SVG uploads to Media Library
*/
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');


add_action('login_init', 'acme_autocomplete_login_init');
function acme_autocomplete_login_init()
{
    ob_start();
}

add_action('login_form', 'acme_autocomplete_login_form');
function acme_autocomplete_login_form()
{
    $content = ob_get_contents();
    ob_end_clean();
    $content = str_replace('id="user_pass"', 'id="user_pass" autocomplete="new-password"', $content);
    $content = str_replace('id="loginform"', 'id="loginform" autocomplete="off"', $content);
    echo $content;
}

setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
if ( SITECOOKIEPATH != COOKIEPATH )
setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);




// FWP Custom Hooks


add_filter( 'facetwp_sort_options', function( $options, $params ) {
    $options = array(
      'default' => array(
          'label' => __( 'Date (Newest)', 'fwp' ),
          'query_args' => array()
      ),

      'date_desc' => array(
          'label' => __( 'Date (Newest)', 'fwp' ),
          'query_args' => array(
              'orderby' => 'date',
              'order' => 'DESC',
          )
      ),
      'date_asc' => array(
          'label' => __( 'Date (Oldest)', 'fwp' ),
          'query_args' => array(
              'orderby' => 'date',
              'order' => 'ASC',
          )
      ),
      'title_asc' => array(
          'label' => __( 'Product Name (A-Z)', 'fwp' ),
          'query_args' => array(
              'orderby' => 'title',
              'order' => 'ASC',
          )
      ),
      'title_desc' => array(
          'label' => __( 'Product Name (Z-A)', 'fwp' ),
          'query_args' => array(
              'orderby' => 'title',
              'order' => 'DESC',
          )
      ),
    );
    return $options;
}, 10, 2 );
