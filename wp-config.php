/**
 * Debug settings for Local (XAMPP) development.
 * This forces errors to show on the screen so you can fix blank pages.
 */
define( 'WP_DEBUG',         true );
define( 'WP_DEBUG_LOG',     true );
define( 'WP_DEBUG_DISPLAY', true ); // MUST be true locally to see errors
@ini_set( 'display_errors', 1 );

// Allow the API to connect to the Jaunrades Nams server
define( 'WP_HTTP_BLOCK_EXTERNAL', false );
define( 'WP_ACCESSIBLE_HOSTS',    'jaunradesnams.lv' );