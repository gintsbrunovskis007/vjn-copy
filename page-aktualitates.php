<?php
/**
 * Template Name: Aktualitātes
 * Description: Paginated list of all posts from the "Jaunrades nams" category.
 *
 * NOTE: Pagination uses ?strana=N, NOT ?paged=N.
 * WordPress's redirect_canonical() strips ?paged= from static pages
 * because their max_num_pages is always 1, which breaks the paginator.
 */

// ---------------------------------------------------------------------------
// Data fetchers
// ---------------------------------------------------------------------------

if ( ! function_exists( 'jn_akt_fetch_page' ) ) {
    /**
     * @return array{posts: array, total_pages: int, error: string}
     */
    function jn_akt_fetch_page( $page, $per_page = 9 ) {
        $page      = max( 1, (int) $page );
        $cache_key = 'jn_akt_p_' . $page . '_pp' . $per_page . '_v2';
        $cached    = get_transient( $cache_key );

        if ( is_array( $cached ) ) {
            return $cached;
        }

        $fail_key = 'jn_akt_p_fail_' . $page;
        if ( get_transient( $fail_key ) ) {
            return array( 'posts' => array(), 'total_pages' => 0, 'error' => 'cached_fail' );
        }

        $api_url = add_query_arg(
            array(
                'categories' => 8,
                'per_page'   => (int) $per_page,
                'page'       => $page,
                '_embed'     => 1,
            ),
            'https://jaunradesnams.lv/wp-json/wp/v2/posts'
        );

        $response = wp_remote_get(
            $api_url,
            array(
                'timeout'    => 8,
                'user-agent' => 'VentspilsJaunradesNams/1.0',
            )
        );

        if ( is_wp_error( $response ) ) {
            set_transient( $fail_key, 1, MINUTE_IN_SECONDS );
            return array( 'posts' => array(), 'total_pages' => 0, 'error' => $response->get_error_message() );
        }

        $code = wp_remote_retrieve_response_code( $response );

        if ( 400 === $code ) {
            // Out of range — page > total_pages
            return array( 'posts' => array(), 'total_pages' => 0, 'error' => 'out_of_range' );
        }

        if ( 200 !== $code ) {
            set_transient( $fail_key, 1, MINUTE_IN_SECONDS );
            return array( 'posts' => array(), 'total_pages' => 0, 'error' => 'http_' . $code );
        }

        $posts = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( ! is_array( $posts ) ) {
            set_transient( $fail_key, 1, MINUTE_IN_SECONDS );
            return array( 'posts' => array(), 'total_pages' => 0, 'error' => 'json' );
        }

        $total_pages = (int) wp_remote_retrieve_header( $response, 'x-wp-totalpages' );
        if ( $total_pages < 1 ) {
            $total_pages = 1;
        }

        $result = array(
            'posts'       => $posts,
            'total_pages' => $total_pages,
            'error'       => '',
        );

        set_transient( $cache_key, $result, 15 * MINUTE_IN_SECONDS );
        return $result;
    }
}

if ( ! function_exists( 'jn_akt_post_data' ) ) {
    function jn_akt_post_data( $post ) {
        $media = $post['_embedded']['wp:featuredmedia'][0] ?? array();
        return array(
            'title'   => $post['title']['rendered'] ?? '',
            'link'    => $post['link'] ?? '#',
            'date'    => $post['date'] ?? '',
            'excerpt' => wp_trim_words( wp_strip_all_tags( $post['excerpt']['rendered'] ?? '' ), 24 ),
            'image'   => $media['source_url'] ?? '',
            'alt'     => $media['alt_text'] ?? '',
            'slug' => $post['slug'] ?? '',
        );
    }
}

if ( ! function_exists( 'jn_akt_page_url' ) ) {
    function jn_akt_page_url( $page ) {
        $base = get_permalink();
        if ( ! $base ) {
            $base = home_url( '/jaunrades-nams/aktualitates/' );
        }
        if ( $page <= 1 ) {
            return $base;
        }
        return add_query_arg( 'strana', (int) $page, $base );
    }
}

if ( ! function_exists( 'jn_akt_pagination_range' ) ) {
    function jn_akt_pagination_range( $current, $total, $window = 1 ) {
        $current = max( 1, min( (int) $current, (int) $total ) );
        $keep    = array();

        for ( $i = 1; $i <= $total; $i++ ) {
            if ( 1 === $i || $total === $i || ( $i >= $current - $window && $i <= $current + $window ) ) {
                $keep[] = $i;
            }
        }

        $out  = array();
        $prev = 0;
        foreach ( $keep as $p ) {
            if ( $prev && $p - $prev > 1 ) {
                $out[] = -1;
            }
            $out[]  = $p;
            $prev   = $p;
        }
        return $out;
    }
}

// ---------------------------------------------------------------------------
// Page state
// ---------------------------------------------------------------------------

$per_page     = 9; // 3 × 3 grid
$current_page = isset( $_GET['strana'] ) ? max( 1, (int) $_GET['strana'] ) : 1;

$data        = jn_akt_fetch_page( $current_page, $per_page );
$posts       = $data['posts'];
$total_pages = max( 1, (int) $data['total_pages'] );

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

    <section class="page-header" aria-labelledby="page-title">
        <div class="container">
            <nav class="breadcrumb" aria-label="Atrašanās vieta">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Aktualitātes</span>
            </nav>

            <h1 id="page-title" class="page-header__title">Aktualitātes</h1>
            <p class="page-header__lead">Jaunākie notikumi, nodarbības un pasākumi Ventspils Jaunrades namā.</p>
        </div>
    </section>

    <section class="section">
        <div class="container">

            <?php if ( empty( $posts ) ) : ?>

                <p class="notice">
                    <?php if ( 'out_of_range' === $data['error'] && $current_page > 1 ) : ?>
                        Šī lapa neeksistē — <a href="<?php echo esc_url( jn_akt_page_url( 1 ) ); ?>">atgriezties uz pirmo lapu</a>.
                    <?php elseif ( ! empty( $data['error'] ) ) : ?>
                        Aktualitātes šobrīd nevar ielādēt.
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Atgriezties uz sākumlapu</a>.
                    <?php else : ?>
                        Šeit vēl nav publicētu aktualitāšu.
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Atgriezties uz sākumlapu</a>.
                    <?php endif; ?>
                </p>

            <?php else : ?>

                <div class="jn-latest-posts">
                    <?php foreach ( $posts as $post ) :
                        $p = jn_akt_post_data( $post );
                        ?>
                        <article class="jn-post">
                            <?php if ( $p['image'] ) : ?>
                                <div class="jn-post__thumb">
                                    <img src="<?php echo esc_url( $p['image'] ); ?>"
                                         alt="<?php echo esc_attr( $p['alt'] ); ?>"
                                         loading="lazy">
                                </div>
                            <?php endif; ?>

                            <div class="jn-post__body">
                                <?php if ( $p['date'] ) : ?>
                                    <p class="jn-post__date">
                                        <time datetime="<?php echo esc_attr( $p['date'] ); ?>">
                                            <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $p['date'] ) ) ); ?>
                                        </time>
                                    </p>
                                <?php endif; ?>

                                <h2 class="jn-post__title">

                                    <?php
                                    $post_slug = '';
                                    if ( ! empty( $post['slug'] ) ) { $post_slug = $post['slug']; }
                                    $internal  = $post_slug ? jn_single_url_post( $post_slug ) : $p['link'];
                                    ?>
                                    <a href="<?php echo esc_url( $internal ); ?>"><?php echo wp_kses_post( $p['title'] ); ?></a>

                                 </h2>

                                <?php if ( $p['excerpt'] ) : ?>
                                    <p class="jn-post__excerpt"><?php echo esc_html( $p['excerpt'] ); ?></p>
                                <?php endif; ?>

                                <span class="jn-post__more" aria-hidden="true">Lasīt vairāk</span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ( $total_pages > 1 ) :
                    $range = jn_akt_pagination_range( $current_page, $total_pages );
                    ?>
                    <nav class="pagination" aria-label="Lapu navigācija">

                        <?php if ( $current_page > 1 ) : ?>
                            <a class="pagination__arrow"
                               href="<?php echo esc_url( jn_akt_page_url( $current_page - 1 ) ); ?>"
                               rel="prev"
                               aria-label="Iepriekšējā lapa">
                                <span aria-hidden="true">←</span>
                            </a>
                        <?php else : ?>
                            <span class="pagination__arrow is-disabled" aria-hidden="true">←</span>
                        <?php endif; ?>

                        <?php foreach ( $range as $p ) : ?>

                            <?php if ( -1 === $p ) : ?>
                                <span class="pagination__gap" aria-hidden="true">…</span>

                            <?php elseif ( $p === $current_page ) : ?>
                                <span class="pagination__num is-current" aria-current="page">
                                    <?php echo esc_html( $p ); ?>
                                    <span class="screen-reader-text">(pašreizējā lapa)</span>
                                </span>

                            <?php else : ?>
                                <a class="pagination__num"
                                   href="<?php echo esc_url( jn_akt_page_url( $p ) ); ?>"
                                   aria-label="Doties uz lapu <?php echo esc_attr( $p ); ?>">
                                    <?php echo esc_html( $p ); ?>
                                </a>
                            <?php endif; ?>

                        <?php endforeach; ?>

                        <?php if ( $current_page < $total_pages ) : ?>
                            <a class="pagination__arrow"
                               href="<?php echo esc_url( jn_akt_page_url( $current_page + 1 ) ); ?>"
                               rel="next"
                               aria-label="Nākamā lapa">
                                <span aria-hidden="true">→</span>
                            </a>
                        <?php else : ?>
                            <span class="pagination__arrow is-disabled" aria-hidden="true">→</span>
                        <?php endif; ?>

                    </nav>

                    <p class="pagination__meta">
                        Lapa <?php echo esc_html( $current_page ); ?> no <?php echo esc_html( $total_pages ); ?>
                    </p>
                <?php endif; ?>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php get_footer(); ?>