<?php
/**
 * Jaunrades nams — Custom theme functions and routing.
 */

// 1. Enqueue styles
function jaunrades_enqueue_styles() {
    wp_enqueue_style(
        'jaunrades-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'jaunrades_enqueue_styles');

// 2. Rewrite rules (Only for Programs now)
add_action( 'init', function () {
    add_rewrite_rule(
        '^nodarbiba/(?:[^/]+/)?([^/]+)/?$',
        'index.php?jn_single_program=$matches[1]',
        'top'
    );
} );

// 3. Register query vars
add_filter( 'query_vars', function ( $vars ) {
    $vars[] = 'jn_single_post';
    $vars[] = 'jn_single_program';
    return $vars;
} );

// 4. Catch 404s for single posts and route them to our custom template
add_action( 'template_redirect', function() {
    if ( is_404() ) {
        // Get the path from the URL
        $path = trim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
        $home_path = trim( parse_url( home_url(), PHP_URL_PATH ), '/' );
        
        // Remove the subdirectory (e.g. "wordpress/") if it exists
        if ( $home_path && strpos( $path, $home_path ) === 0 ) {
            $path = trim( substr( $path, strlen( $home_path ) ), '/' );
        }
        
        // Check if it's a single segment slug like /citadais-planetarijs-talu-prom-kira/
        if ( $path && strpos( $path, '/' ) === false ) {
            global $wp_query;
            // Remove the 404 status
            $wp_query->is_404 = false;
            status_header( 200 );
            
            // Pass the slug to the template
            set_query_var( 'jn_single_post', $path );
            
            // Load the custom single template
            $template = locate_template( 'single-jn-post.php' );
            if ( $template ) {
                include $template;
                exit;
            }
        }
    }
});

// 5. Route programs to the program template
add_filter( 'template_include', function ( $template ) {
    if ( get_query_var( 'jn_single_program' ) ) {
        $t = locate_template( 'single-jn-program.php' );
        if ( $t ) return $t;
    }
    return $template;
} );

// 6. Helpers
if ( ! function_exists( 'jn_api_get' ) ) {
    function jn_api_get( $path, $query = array() ) {
        $url = add_query_arg( $query, 'https://jaunradesnams.lv/wp-json/wp/v2/' . ltrim( $path, '/' ) );
        $r   = wp_remote_get( $url, array( 'timeout' => 15, 'user-agent' => 'VJN/1.0' ) );
        if ( is_wp_error( $r ) || 200 !== wp_remote_retrieve_response_code( $r ) ) {
            return null;
        }
        return json_decode( wp_remote_retrieve_body( $r ), true );
    }
}

if ( ! function_exists( 'jn_single_url_post' ) ) {
    function jn_single_url_post( $slug ) {
        return home_url( '/' . $slug . '/' );
    }
}

if ( ! function_exists( 'jn_single_url_program' ) ) {
    function jn_single_url_program( $slug, $cat = '' ) {
        $cat = $cat ? trailingslashit( $cat ) : '';
        return home_url( '/nodarbiba/' . $cat . $slug . '/' );
    }
}

// 7. Pagination helper
if ( ! function_exists( 'jn_pg_range' ) ) {
    function jn_pg_range( $cur, $tot, $w = 1 ) {
        $cur = max( 1, min( (int) $cur, (int) $tot ) );
        $k = array();
        for ( $i = 1; $i <= $tot; $i++ ) {
            if ( 1 === $i || $tot === $i || ( $i >= $cur - $w && $i <= $cur + $w ) ) { $k[] = $i; }
        }
        $out = array(); $prev = 0;
        foreach ( $k as $p ) {
            if ( $prev && $p - $prev > 1 ) { $out[] = -1; }
            $out[] = $p; $prev = $p;
        }
        return $out;
    }
}

// 8. Apply Planetarium Body Class
add_filter( 'body_class', function( $classes ) {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if ( strpos( $request_uri, '/planetarijs/' ) !== false || is_page( 'planetarijs' ) || !empty( $GLOBALS['is_planetarium_post'] ) ) {
        $classes[] = 'planetarium-theme';
    }
    return $classes;
});