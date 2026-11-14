<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$jn_includes = array(
	'inc/helpers.php',
	'inc/data.php',
	'inc/api.php',
	'inc/content.php',
	'inc/setup.php',
	'inc/assets.php',
	'inc/navigation.php',
	'inc/security.php',
	'inc/seo.php',
	'inc/template-tags.php',
	'inc/routing.php',
);

foreach ( $jn_includes as $jn_file ) {
	$jn_path = get_template_directory() . '/' . $jn_file;
	if ( file_exists( $jn_path ) ) {
		require_once $jn_path;
	}
}

add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
