<?php

$posts = array();
$url   = 'https://jaunradesnams.lv/wp-json/wp/v2/posts?categories=8&per_page=3&_embed';

$context = stream_context_create([
    'http' => [
        'method'           => 'GET',
        'header'           => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n" .
                              "Accept: application/json\r\n" .
                              "Referer: https://jaunradesnams.lv/\r\n",
        'protocol_version' => 1.1,
        'timeout'          => 15,
        'ignore_errors'    => true,
    ],
    'ssl' => [
        'verify_peer'      => false,
        'verify_peer_name' => false,
    ],
]);

$json = @file_get_contents( $url, false, $context );
if ( false !== $json && false === strpos( $json, '421 Misdirected' ) ) {
    $fetched = json_decode( $json, true );
    if ( is_array( $fetched ) ) {
        $posts = $fetched;
    }
}

$news_url   = home_url( '/jaunrades-nams/aktualitates/' );
$hero_image = 'https://jaunradesnams.lv/wp-content/uploads/jnams-1200x666.jpg';

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

    <h1 class="screen-reader-text">Ventspils Jaunrades nams</h1>

    <section class="hero" aria-labelledby="hero-title">
        <div class="hero__inner">
            <div class="hero__content">
                <p class="hero__eyebrow">Laipni lūdzam</p>
                <h2 id="hero-title" class="hero__title">
                    Ventspils
                    <em>Jaunrades nams</em>
                </h2>
                <p class="hero__lead">
                    Bērns ir ne tikai tas, kas viņš ir šodien, bet arī tas, par ko viņš var kļūt rīt.
                    <cite>— Ventspils Jaunrades nams</cite>
                </p>
                <div class="hero__actions">
                    <a class="btn btn--primary" href="<?php echo esc_url( home_url( '/jaunrades-nams/pulcini/' ) ); ?>">Apskati nodarbības</a>
                    <a class="btn btn--ghost" href="<?php echo esc_url( $news_url ); ?>">Aktualitātes</a>
                </div>
                <div class="hero__stats">
                    <div class="hero__stat">
                        <strong>1955</strong>
                        <span>Kopš dibināšanas</span>
                    </div>
                    <div class="hero__stat">
                        <strong>55+</strong>
                        <span>Pulciņi</span>
                    </div>
                    <div class="hero__stat">
                        <strong>3–25</strong>
                        <span>Gadu vecums</span>
                    </div>
                </div>
            </div>
            <div class="hero__visual">
                <div class="hero__image">
                    <img src="<?php echo esc_url( $hero_image ); ?>" alt="Ventspils Jaunrades nams" loading="eager" referrerpolicy="no-referrer">
                </div>
                <div class="hero__badge">
                    <strong>1955</strong>
                    <span>Darbojamies kopš</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section section--flush">
        <div class="container">
            <div class="planetarium-banner">
                <div class="planetarium-banner__content">
                    <span class="planetarium-banner__tag">Ventspils Planetārijs</span>
                    <h2 class="planetarium-banner__title">Atklāj Visumu</h2>
                    <p class="planetarium-banner__desc">Apmeklē Baltijā modernāko digitālo planetāriju un observatoriju.</p>
                </div>
                <a href="<?php echo esc_url( home_url( '/planetarijs/' ) ); ?>" class="btn planetarium-banner__btn">
                    Uzzināt vairāk <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </section>

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
                        $media   = $post['_embedded']['wp:featuredmedia'][0] ?? array();
                        $title   = $post['title']['rendered'] ?? '';
                        $slug    = $post['slug'] ?? '';
                        $date    = $post['date'] ?? '';
                        $image   = $media['source_url'] ?? '';
                        $alt     = $media['alt_text'] ?? '';
                        $excerpt = wp_trim_words( wp_strip_all_tags( $post['excerpt']['rendered'] ?? '' ), 24 );
                        $link    = $slug ? home_url( '/' . $slug . '/' ) : ( $post['link'] ?? '#' );
                        ?>
                        <article class="jn-post">
                            <?php if ( $image ) : ?>
                                <div class="jn-post__thumb">
                                    <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" referrerpolicy="no-referrer">
                                </div>
                            <?php endif; ?>
                            <div class="jn-post__body">
                                <?php if ( $date ) : ?>
                                    <p class="jn-post__date">
                                        <time datetime="<?php echo esc_attr( $date ); ?>">
                                            <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ); ?>
                                        </time>
                                    </p>
                                <?php endif; ?>
                                <h3 class="jn-post__title">
                                    <a href="<?php echo esc_url( $link ); ?>"><?php echo wp_kses_post( $title ); ?></a>
                                </h3>
                                <?php if ( $excerpt ) : ?>
                                    <p class="jn-post__excerpt"><?php echo esc_html( $excerpt ); ?></p>
                                <?php endif; ?>
                                <span class="jn-post__more" aria-hidden="true">Lasīt vairāk</span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div class="jn-all-wrap">
                    <a class="jn-all-link" href="<?php echo esc_url( $news_url ); ?>">
                        Skatīt visas aktualitātes <span aria-hidden="true">→</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
