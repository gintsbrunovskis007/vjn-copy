<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	function () {
		add_rewrite_rule( '^nodarbiba/(?:[^/]+/)?([^/]+)/?$', 'index.php?jn_single_program=$matches[1]', 'top' );
		add_rewrite_rule( '^sitemap\.xml$', 'index.php?jn_sitemap=1', 'top' );

		if ( '2' !== get_option( 'jn_rewrite_version' ) ) {
			flush_rewrite_rules( false );
			update_option( 'jn_rewrite_version', '2', false );
		}
	},
	99
);

add_action(
	'after_switch_theme',
	function () {
		flush_rewrite_rules();
	}
);

add_filter(
	'query_vars',
	function ( $vars ) {
		$vars[] = 'jn_single_post';
		$vars[] = 'jn_single_program';
		$vars[] = 'jn_sitemap';
		return $vars;
	}
);

add_action(
	'template_redirect',
	function () {
		if ( get_query_var( 'jn_sitemap' ) ) {
			status_header( 200 );
			header( 'Content-Type: application/xml; charset=UTF-8' );
			echo jn_sitemap_xml();
			exit;
		}

		if ( get_query_var( 'jn_single_post' ) || get_query_var( 'jn_single_program' ) ) {
			global $wp_query;
			$wp_query->is_home       = false;
			$wp_query->is_front_page = false;
			$wp_query->is_404        = false;
		}

		if ( is_404() ) {
			$path      = (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH );
			$path      = trim( $path, '/' );
			$home_path = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );

			if ( $home_path && 0 === strpos( $path, $home_path ) ) {
				$path = trim( substr( $path, strlen( $home_path ) ), '/' );
			}

			$segments = array_values( array_filter( explode( '/', $path ) ) );
			$reserved = array(
				'wp-admin', 'wp-content', 'wp-includes', 'wp-json', 'wp-login.php',
				'xmlrpc.php', 'feed', 'comments', 'trackback', 'robots.txt',
				'favicon.ico', 'sitemap.xml',
			);

			if ( 1 === count( $segments ) && ! in_array( $segments[0], $reserved, true ) ) {
				$slug = sanitize_title( $segments[0] );
				if ( $slug ) {
					global $wp_query;
					$wp_query->is_404 = false;
					status_header( 200 );
					set_query_var( 'jn_single_post', $slug );
					$GLOBALS['jn_is_virtual'] = true;
				}
			}
		}
	},
	5
);

add_filter(
	'template_include',
	function ( $template ) {
		if ( get_query_var( 'jn_single_program' ) ) {
			$found = locate_template( 'single-jn-program.php' );
			if ( $found ) {
				return $found;
			}
		}

		if ( get_query_var( 'jn_single_post' ) ) {
			$found = locate_template( 'single-jn-post.php' );
			if ( $found ) {
				return $found;
			}
		}

		return $template;
	}
);

add_filter(
	'body_class',
	function ( $classes ) {
		if ( jn_is_planetarium() ) {
			$classes[] = 'planetarium-theme';
		}
		if ( get_query_var( 'jn_single_post' ) || get_query_var( 'jn_single_program' ) ) {
			$classes[] = 'is-virtual-page';
		}
		return $classes;
	}
);
