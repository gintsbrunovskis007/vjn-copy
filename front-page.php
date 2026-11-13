<?php
/**
 * Front page.
 * Flow: intro (title + quote + CTA) → 3 newest posts in one grid → footer.
 */

if ( ! function_exists( 'jn_get_latest_posts' ) ) {
    function jn_get_latest_posts() {
        $cache_key = 'jn_latest_posts_v3';
        $cached    = get_transient( $cache_key );
        if ( is_array( $cached ) ) {
            return $cached;
        }
        if ( get_transient( 'jn_latest_posts_failed' ) ) {
            return array();
        }

        $response = wp_remote_get(
            'https://jaunradesnams.lv/wp-json/wp/v2/posts?categories=8&per_page=3&_embed',
            array(
                'timeout'    => 8,
                'user-agent' => 'VentspilsJaunradesNams/1.0',
            )
        );

        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
            set_transient( 'jn_latest_posts_failed', 1, 2 * MINUTE_IN_SECONDS );
            return array();
        }

        $posts = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( ! is_array( $posts ) ) {
            set_transient( 'jn_latest_posts_failed', 1, 2 * MINUTE_IN_SECONDS );
            return array();
        }

        set_transient( $cache_key, $posts, 15 * MINUTE_IN_SECONDS );
        return $posts;
    }
}

if ( ! function_exists( 'jn_post_data' ) ) {
    function jn_post_data( $post ) {
        $media = $post['_embedded']['wp:featuredmedia'][0] ?? array();
        return array(
            'title'   => $post['title']['rendered'] ?? '',
            'link'    => $post['link'] ?? '#',
            'date'    => $post['date'] ?? '',
            'excerpt' => wp_trim_words( wp_strip_all_tags( $post['excerpt']['rendered'] ?? '' ), 24 ),
            'image'   => $media['source_url'] ?? '',
            'alt'     => $media['alt_text'] ?? '',
        );
    }
}

$posts    = jn_get_latest_posts();
$news_url = home_url( '/jaunrades-nams/aktualitates/' );

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

    <h1 class="screen-reader-text">Ventspils Jaunrades nams</h1>

    <!-- ============================================================
         INTRO — title, tagline, CTAs
    ============================================================ -->
    <section class="intro" aria-labelledby="intro-title">
        <div class="intro__inner">

            <p class="intro__eyebrow">Laipni lūdzam</p>

            <h2 id="intro-title" class="intro__title">
                <span>Ventspils</span>
                <span class="intro__title-accent">Jaunrades nams</span>
            </h2>

            <p class="intro__lead">
                Bērns ir ne tikai tas, kas viņš ir šodien, bet arī tas, par ko viņš var kļūt rīt.
                <cite>— Ventspils Jaunrades nams</cite>
            </p>

            <div class="intro__actions">
                <a class="btn btn--primary" href="<?php echo esc_url( home_url( '/jaunrades-nams/pulcini/' ) ); ?>">Apskati nodarbības</a>
                <a class="btn btn--ghost" href="<?php echo esc_url( $news_url ); ?>">Aktualitātes</a>
            </div>

        </div>
    </section>

    <!-- ============================================================
         NEWS — 3 newest posts in one unified grid
    ============================================================ -->
    <section class="section" aria-labelledby="news-title">
        <div class="container">

            <div class="section__head">
                <h2 id="news-title">Aktualitātes</h2>
                <p>Jaunākie notikumi, nodarbības un pasākumi Ventspils Jaunrades namā.</p>
            </div>

            <?php if ( empty( $posts ) ) : ?>
                <p class="notice">
                    Aktualitātes šobrīd nevar ielādēt.
                    <a href="<?php echo esc_url( $news_url ); ?>">Skatīt aktualitāšu lapu</a>.
                </p>
            <?php else : ?>

                <div class="jn-latest-posts">
                    <?php foreach ( $posts as $post ) :
                        $p = jn_post_data( $post );
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

                              <h3 class="jn-post__title">
                                    <?php
                                    $slug     = $post['slug'] ?? '';
                                    $internal = $slug ? jn_single_url_post( $slug ) : $p['link'];
                                    ?>
                                    <a href="<?php echo esc_url( $internal ); ?>"><?php echo wp_kses_post( $p['title'] ); ?></a>
                                </h3>

                                <?php if ( $p['excerpt'] ) : ?>
                                    <p class="jn-post__excerpt"><?php echo esc_html( $p['excerpt'] ); ?></p>
                                <?php endif; ?>

                                <span class="jn-post__more" aria-hidden="true">Lasīt vairāk</span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div class="jn-all-wrap">
                    <a class="jn-all-link" href="<?php echo esc_url( $news_url ); ?>">
                        Skatīt visas aktualitātes
                        <span aria-hidden="true">→</span>
                    </a>
                </div>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php get_footer(); ?>