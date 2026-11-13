<?php
/**
 * Front page.
 * Flow: intro (title + quote + CTA) → 3 newest posts in one grid → footer.
 */

$data  = jn_get_posts( 1, 3 );
$posts = $data['posts'];
$news_url = home_url( '/jaunrades-nams/aktualitates/' );

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

    <h1 class="screen-reader-text">Ventspils Jaunrades nams</h1>

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
                        $link    = $slug ? jn_single_url_post( $slug ) : ( $post['link'] ?? '#' );
                        ?>
                        <article class="jn-post">
                            <?php if ( $image ) : ?>
                                <div class="jn-post__thumb">
                                    <img src="<?php echo esc_url( $image ); ?>"
                                         alt="<?php echo esc_attr( $alt ); ?>"
                                         loading="lazy" referrerpolicy="no-referrer">
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
                        Skatīt visas aktualitātes
                        <span aria-hidden="true">→</span>
                    </a>
                </div>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php get_footer(); ?>