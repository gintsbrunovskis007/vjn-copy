<?php

function jaunrades_enqueue_assets() {
    $version = wp_get_theme()->get( 'Version' );

    wp_enqueue_style(
        'jaunrades-fonts',
        'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap',
        array(),
        null
    );

    wp_enqueue_style(
        'jaunrades-style',
        get_stylesheet_uri(),
        array( 'jaunrades-fonts' ),
        $version
    );

    wp_enqueue_script(
        'jaunrades-theme',
        get_template_directory_uri() . '/assets/theme.js',
        array(),
        $version,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'jaunrades_enqueue_assets' );

add_filter( 'wp_resource_hints', function ( $urls, $relation ) {
    if ( 'preconnect' === $relation ) {
        $urls[] = 'https://fonts.googleapis.com';
        $urls[] = 'https://fonts.gstatic.com';
    }
    return $urls;
}, 10, 2 );

add_action( 'init', function () {
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

add_action( 'template_redirect', function () {
    if ( is_404() ) {
        $path      = trim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
        $home_path = trim( parse_url( home_url(), PHP_URL_PATH ), '/' );

        if ( $home_path && strpos( $path, $home_path ) === 0 ) {
            $path = trim( substr( $path, strlen( $home_path ) ), '/' );
        }

        if ( $path && strpos( $path, '/' ) === false ) {
            global $wp_query;
            $wp_query->is_404 = false;
            status_header( 200 );

            set_query_var( 'jn_single_post', $path );

            $template = locate_template( 'single-jn-post.php' );
            if ( $template ) {
                include $template;
                exit;
            }
        }
    }
} );

add_filter( 'template_include', function ( $template ) {
    if ( get_query_var( 'jn_single_program' ) ) {
        $t = locate_template( 'single-jn-program.php' );
        if ( $t ) {
            return $t;
        }
    }
    return $template;
} );

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

if ( ! function_exists( 'jn_pg_range' ) ) {
    function jn_pg_range( $cur, $tot, $w = 1 ) {
        $cur = max( 1, min( (int) $cur, (int) $tot ) );
        $k   = array();
        for ( $i = 1; $i <= $tot; $i++ ) {
            if ( 1 === $i || $tot === $i || ( $i >= $cur - $w && $i <= $cur + $w ) ) {
                $k[] = $i;
            }
        }
        $out  = array();
        $prev = 0;
        foreach ( $k as $p ) {
            if ( $prev && $p - $prev > 1 ) {
                $out[] = -1;
            }
            $out[] = $p;
            $prev  = $p;
        }
        return $out;
    }
}

add_filter( 'body_class', function ( $classes ) {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if ( strpos( $request_uri, '/planetarijs/' ) !== false || is_page( 'planetarijs' ) || !empty( $GLOBALS['is_planetarium_post'] ) ) {
        $classes[] = 'planetarium-theme';
    }
    return $classes;
} );

if ( ! function_exists( 'jn_api_cache' ) ) {
    function jn_api_cache( $key, $path, $query = array(), $ttl = 300 ) {
        $cached = get_transient( $key );
        if ( false !== $cached ) {
            return $cached;
        }

        $data = jn_api_get( $path, $query );
        if ( null !== $data ) {
            set_transient( $key, $data, (int) $ttl );
        }

        return $data;
    }
}

if ( ! function_exists( 'jn_get_posts' ) ) {
    function jn_get_posts( $page = 1, $per_page = 9 ) {
        $page     = max( 1, (int) $page );
        $per_page = max( 1, (int) $per_page );
        $key      = 'jn_posts_' . $page . '_' . $per_page;

        $cached = get_transient( $key );
        if ( is_array( $cached ) ) {
            return $cached;
        }

        $url = add_query_arg(
            array(
                'categories' => 8,
                'per_page'   => $per_page,
                'page'       => $page,
                '_embed'     => 1,
            ),
            'https://jaunradesnams.lv/wp-json/wp/v2/posts'
        );

        $response = wp_remote_get(
            $url,
            array(
                'timeout'    => 15,
                'user-agent' => 'VJN/1.0',
            )
        );

        if ( is_wp_error( $response ) ) {
            return array( 'posts' => array(), 'total_pages' => 1, 'error' => 'request_failed' );
        }

        $code  = (int) wp_remote_retrieve_response_code( $response );
        $total = (int) wp_remote_retrieve_header( $response, 'x-wp-totalpages' );
        $total = max( 1, $total );
        $body  = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( 400 === $code && $page > 1 ) {
            return array( 'posts' => array(), 'total_pages' => $total, 'error' => 'out_of_range' );
        }

        if ( 200 !== $code || ! is_array( $body ) ) {
            return array( 'posts' => array(), 'total_pages' => 1, 'error' => 'api_error' );
        }

        $result = array( 'posts' => $body, 'total_pages' => $total, 'error' => '' );
        set_transient( $key, $result, 5 * MINUTE_IN_SECONDS );

        return $result;
    }
}

if ( ! function_exists( 'jn_get_page_by_slug' ) ) {
    function jn_get_page_by_slug( $slug ) {
        $slug = sanitize_title( $slug );
        if ( '' === $slug ) {
            return null;
        }

        $data = jn_api_cache(
            'jn_page_' . md5( $slug ),
            'pages',
            array(
                'slug'    => $slug,
                '_fields' => 'slug,title,link,content',
            ),
            15 * MINUTE_IN_SECONDS
        );

        if ( ! is_array( $data ) || empty( $data[0] ) ) {
            return null;
        }

        return $data[0];
    }
}

if ( ! function_exists( 'jn_get_pages_by_slugs' ) ) {
    function jn_get_pages_by_slugs( $slugs, $fields = 'slug,title,link,content' ) {
        $slugs = array_values( array_unique( array_filter( array_map( 'sanitize_title', (array) $slugs ) ) ) );
        $out   = array();

        foreach ( array_chunk( $slugs, 20 ) as $chunk ) {
            $key = 'jn_pages_' . md5( implode( ',', $chunk ) . '|' . $fields );

            $cached = get_transient( $key );
            if ( is_array( $cached ) ) {
                $out += $cached;
                continue;
            }

            $data = jn_api_cache(
                $key . '_api',
                'pages',
                array(
                    'slug'     => implode( ',', $chunk ),
                    'per_page' => 100,
                    '_fields'  => $fields,
                ),
                15 * MINUTE_IN_SECONDS
            );

            if ( ! is_array( $data ) ) {
                continue;
            }

            $chunk_out = array();
            foreach ( $data as $page ) {
                if ( ! empty( $page['slug'] ) ) {
                    $chunk_out[ $page['slug'] ] = $page;
                }
            }

            set_transient( $key, $chunk_out, 15 * MINUTE_IN_SECONDS );
            $out += $chunk_out;
        }

        return $out;
    }
}

if ( ! function_exists( 'jn_page_url' ) ) {
    function jn_page_url( $base, $page ) {
        $page = (int) $page;
        return $page <= 1 ? $base : add_query_arg( 'strana', $page, $base );
    }
}

if ( ! function_exists( 'jn_pagination_range' ) ) {
    function jn_pagination_range( $current, $total, $window = 1 ) {
        return jn_pg_range( $current, $total, $window );
    }
}
