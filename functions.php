<?php
/**
 * Jaunrades nams — custom routes for single API-backed content.
 */

function jaunrades_enqueue_styles() {
    wp_enqueue_style(
    'jaunrades-style',
    get_stylesheet_uri(),
    array(),
    time()
);
}
add_action('wp_enqueue_scripts', 'jaunrades_enqueue_styles');



// ---------- Rewrite rules ----------
add_action( 'init', function () {
    // /aktualitate/<slug>/
    add_rewrite_rule(
        '^aktualitate/([^/]+)/?$',
        'index.php?jn_single_post=$matches[1]',
        'top'
    );
    // /nodarbiba/<any>/<slug>/   or   /nodarbiba/<slug>/
    add_rewrite_rule(
        '^nodarbiba/(?:[^/]+/)?([^/]+)/?$',
        'index.php?jn_single_program=$matches[1]',
        'top'
    );
} );

add_filter( 'query_vars', function ( $vars ) {
    $vars[] = 'jn_single_post';
    $vars[] = 'jn_single_program';
    return $vars;
} );

// ---------- Route to templates ----------
add_filter( 'template_include', function ( $template ) {
    if ( get_query_var( 'jn_single_post' ) ) {
        $t = locate_template( 'single-jn-post.php' );
        if ( $t ) return $t;
    }
    if ( get_query_var( 'jn_single_program' ) ) {
        $t = locate_template( 'single-jn-program.php' );
        if ( $t ) return $t;
    }
    return $template;
} );

// ---------- Helpers (shared by templates) ----------
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
        return home_url( '/aktualitate/' . $slug . '/' );
    }
}

if ( ! function_exists( 'jn_single_url_program' ) ) {
    function jn_single_url_program( $slug, $cat = '' ) {
        $cat = $cat ? trailingslashit( $cat ) : '';
        return home_url( '/nodarbiba/' . $cat . $slug . '/' );
    }
}

// Flush rules once on theme activation if you want (optional).
add_action( 'after_switch_theme', function () { flush_rewrite_rules(); } );

/**
 * Image proxy — fetches and caches external images server-side so they
 * display regardless of hotlink protection on the source site.
 *
 * Two entry points (either works):
 *   /?jn_img=<urlencoded_url>
 *   /wp-admin/admin-ajax.php?action=jn_img&u=<urlencoded_url>
 */
if ( ! function_exists( 'jn_img_proxy_handle' ) ) {
    function jn_img_proxy_handle( $url ) {
        if ( ! $url ) {
            status_header( 400 );
            exit;
        }

        $host = wp_parse_url( $url, PHP_URL_HOST );
        if ( ! $host || strpos( $host, 'jaunradesnams.lv' ) === false ) {
            status_header( 403 );
            exit;
        }

        $upload_dir = wp_upload_dir();
        $cache_dir  = $upload_dir['basedir'] . '/jn-img-cache';
        if ( ! file_exists( $cache_dir ) ) {
            wp_mkdir_p( $cache_dir );
        }

        $hash = md5( $url );
        $ext  = strtolower( pathinfo( wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
        if ( ! in_array( $ext, array( 'jpg', 'jpeg', 'png', 'gif', 'webp' ), true ) ) {
            $ext = 'jpg';
        }
        $file = $cache_dir . '/' . $hash . '.' . $ext;

        // Serve from disk cache
        if ( file_exists( $file ) && filesize( $file ) > 0 && filemtime( $file ) > time() - 7 * DAY_IN_SECONDS ) {
            $mime = ( 'jpg' === $ext || 'jpeg' === $ext ) ? 'image/jpeg' : 'image/' . $ext;
            header( 'Content-Type: ' . $mime );
            header( 'Cache-Control: public, max-age=86400' );
            header( 'Content-Length: ' . filesize( $file ) );
            readfile( $file );
            exit;
        }

        // Fetch from source — WITH Referer + full browser headers to bypass hotlink protection
        $r = wp_remote_get( $url, array(
            'timeout'   => 20,
            'sslverify' => false,
            'headers'   => array(
                'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Referer'         => 'https://jaunradesnams.lv/',
                'Accept'          => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                'Accept-Language' => 'lv,en;q=0.9',
            ),
        ) );

        if ( is_wp_error( $r ) || 200 !== wp_remote_retrieve_response_code( $r ) ) {
            $status = is_wp_error( $r ) ? $r->get_error_message() : wp_remote_retrieve_response_code( $r );
            error_log( 'jn_img FAIL: ' . $url . ' => ' . $status );
            status_header( 404 );
            exit;
        }

        $body = wp_remote_retrieve_body( $r );
        if ( ! $body ) {
            status_header( 404 );
            exit;
        }

        @file_put_contents( $file, $body );

        $mime = wp_remote_retrieve_header( $r, 'content-type' );
        if ( ! $mime || 0 !== strpos( $mime, 'image/' ) ) {
            $mime = ( 'jpg' === $ext || 'jpeg' === $ext ) ? 'image/jpeg' : 'image/' . $ext;
        }

        header( 'Content-Type: ' . $mime );
        header( 'Cache-Control: public, max-age=86400' );
        header( 'Content-Length: ' . strlen( $body ) );
        echo $body;
        exit;
    }
}

// Entry point 1: admin-ajax (original)
add_action( 'wp_ajax_nopriv_jn_img', function () {
    jn_img_proxy_handle( isset( $_GET['u'] ) ? urldecode( wp_unslash( $_GET['u'] ) ) : '' );
} );
add_action( 'wp_ajax_jn_img', function () {
    jn_img_proxy_handle( isset( $_GET['u'] ) ? urldecode( wp_unslash( $_GET['u'] ) ) : '' );
} );

// Entry point 2: /?jn_img=<url> — works even when admin-ajax is blocked
add_action( 'template_redirect', function () {
    if ( ! empty( $_GET['jn_img'] ) ) {
        jn_img_proxy_handle( urldecode( wp_unslash( $_GET['jn_img'] ) ) );
    }
} );