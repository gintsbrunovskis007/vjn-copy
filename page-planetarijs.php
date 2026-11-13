<?php
/**
 * Template Name: Planetārijs
 */

if ( ! function_exists( 'jn_planetarium_news' ) ) {
    function jn_planetarium_news() {
        $cache = get_transient( 'jn_planet_news_v1' );
        if ( is_array( $cache ) ) { return $cache; }

        $r = wp_remote_get( 'https://jaunradesnams.lv/wp-json/wp/v2/posts?categories=7&per_page=3&_embed', array( 'timeout' => 15, 'user-agent' => 'VJN/1.0' ) );
        if ( is_wp_error( $r ) || 200 !== wp_remote_retrieve_response_code( $r ) ) { return array(); }

        $posts = json_decode( wp_remote_retrieve_body( $r ), true );
        if ( ! is_array( $posts ) ) { return array(); }

        set_transient( 'jn_planet_news_v1', $posts, 15 * MINUTE_IN_SECONDS );
        return $posts;
    }
}

$posts = jn_planetarium_news();
get_header();
?>

<main id="main" class="site-main" tabindex="-1">
    <section class="page-header" aria-labelledby="page-title">
        <div class="container">
            <nav class="breadcrumb" aria-label="Atrašanās vieta">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Planetārijs</span>
            </nav>
            <h1 id="page-title" class="page-header__title">Planetārijs un Observatorija</h1>
            <p class="page-header__lead">Baltijā modernākais digitālais planetārijs un observatorija ar modernu teleskopu.</p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="hub-grid hub-grid--planet">
                <a class="hub-card hub-card--no-image" href="<?php echo esc_url( home_url( '/planetarijs/aktualitates/' ) ); ?>">
                    <div class="hub-card__content">
                        <h2 class="hub-card__title">Aktualitātes</h2>
                        <p class="hub-card__sub">Jaunākās ziņas un notikumi</p>
                        <span class="hub-card__arrow" aria-hidden="true">→</span>
                    </div>
                </a>
                <a class="hub-card hub-card--no-image" href="<?php echo esc_url( home_url( '/planetarijs/galerija/' ) ); ?>">
                    <div class="hub-card__content">
                        <h2 class="hub-card__title">Galerija</h2>
                        <p class="hub-card__sub">Foto no pasākumiem un observatorijas</p>
                        <span class="hub-card__arrow" aria-hidden="true">→</span>
                    </div>
                </a>
                <a class="hub-card hub-card--no-image" href="<?php echo esc_url( home_url( '/planetarijs/seansu-laiki/' ) ); ?>">
                    <div class="hub-card__content">
                        <h2 class="hub-card__title">Seansu laiki</h2>
                        <p class="hub-card__sub">Apskati filmu un observatorijas grafiku</p>
                        <span class="hub-card__arrow" aria-hidden="true">→</span>
                    </div>
                </a>
                <a class="hub-card hub-card--no-image" href="<?php echo esc_url( home_url( '/planetarijs/cenas/' ) ); ?>">
                    <div class="hub-card__content">
                        <h2 class="hub-card__title">Biļešu cenas</h2>
                        <p class="hub-card__sub">Informācija par biļešu iegādi</p>
                        <span class="hub-card__arrow" aria-hidden="true">→</span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section__head section__head--left">
                <h2>Jaunākās aktualitātes</h2>
            </div>

            <?php if ( empty( $posts ) ) : ?>
                <p class="notice">Aktualitātes šobrīd nevar ielādēt.</p>
            <?php else : ?>
                <div class="jn-latest-posts">
                    <?php foreach ( $posts as $post ) :
                        $media = $post['_embedded']['wp:featuredmedia'][0] ?? array();
                        $img = $media['source_url'] ?? '';
                        $title = $post['title']['rendered'] ?? '';
                        $slug = $post['slug'] ?? '';
                        $date = $post['date'] ?? '';
                        $link = jn_single_url_post( $slug );
                        ?>
                        <article class="jn-post">
                            <?php if ( $img ) : ?>
                                <div class="jn-post__thumb">
                                    <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" referrerpolicy="no-referrer">
                                </div>
                            <?php endif; ?>
                            <div class="jn-post__body">
                                <p class="jn-post__date"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ); ?></p>
                                <h3 class="jn-post__title">
                                    <a href="<?php echo esc_url( $link ); ?>"><?php echo wp_kses_post( $title ); ?></a>
                                </h3>
                                <span class="jn-post__more" aria-hidden="true">Lasīt vairāk →</span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php get_footer(); ?>