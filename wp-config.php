<?php


// ** MySQL settings ** //
/** The name of the database for WordPress */
define('DB_NAME', 'lightingglobal_prod');

/** MySQL database username */
define('DB_USER', 'dbadmlgprod');

/** MySQL database password */
define('DB_PASSWORD', 't8q!J6n0');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('AUTH_KEY',         '*.$_x8gwg2p(4ewvj&542ZF3+GowiQ9-/itF@7hJQ%{Z>BPaZW42vLUuU<eKA[|h');
define('SECURE_AUTH_KEY',  'Qc.Oy^8z9Z&kq4]$TrbW^!iBn:wG+Ub/kGLK1ZM^@ij8:+-jKp+mV(L5%m(I0q7N');
define('LOGGED_IN_KEY',    'e7m-?BKWBF{Uifh2S</ct=(M1r7tZ-|^a6@+@p:74C4FS[v,.j&gMXKp$H]B9+I.');
define('NONCE_KEY',        'hyP84^bk`~9Gfzn#qL*R>%(940*N#ev5 kj4s78_H}I|^WD+{T0OgopumWVs$O+B');
define('AUTH_SALT',        'UkTdBNR>nRUM<tw GE&?*)rw:`1Be+z%nJB+:9N0v1UG,wZgV=I46KT6LqAF}Yz+');
define('SECURE_AUTH_SALT', 'zl[Y1g?dcW^/%)S}Sm+2V;)@v|u/NgwJP}Veq,IJ9^H^=]4gmaWM}`5jN.ehe%*@');
define('LOGGED_IN_SALT',   'Qs{JvKY2dSyokonaopeHWD$!X5-::Vb$d^$Xe?~KOHim(yDy9;q}FMhyGpqAV,ka');
define('NONCE_SALT',       '(Z9@@2&/+F~$pvqvGb.NyI2R|lu+wLKs<rQrfHZB-I_6:{TFXn4FUFrbv l!A@RQ');


$table_prefix = 'wp_';

define('WP_HOME','https://www.lightingglobal.org');
define('WP_SITEURL','https://www.lightingglobal.org');




/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
//Disable File Edits
define('DISALLOW_FILE_EDIT', true);