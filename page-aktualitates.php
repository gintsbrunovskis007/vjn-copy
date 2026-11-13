<?php
/**
 * Template Name: Aktualitātes
 */

$per_page     = 9;
$current_page = isset( $_GET['strana'] ) ? max( 1, (int) $_GET['strana'] ) : 1;

$data        = jn_get_posts( $current_page, $per_page );
$posts       = $data['posts'];
$total_pages = max( 1, (int) $data['total_pages'] );
$error       = $data['error'];

$base = get_permalink() ?: home_url( '/jaunrades-nams/aktualitates/' );

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
                    <?php if ( 'out_of_range' === $error && $current_page > 1 ) : ?>
                        Šī lapa neeksistē — <a href="<?php echo esc_url( jn_page_url( $base, 1 ) ); ?>">atgriezties uz pirmo lapu</a>.
                    <?php elseif ( ! empty( $error ) ) : ?>
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

                                <h2 class="jn-post__title">
                                    <a href="<?php echo esc_url( $link ); ?>"><?php echo wp_kses_post( $title ); ?></a>
                                </h2>

                                <?php if ( $excerpt ) : ?>
                                    <p class="jn-post__excerpt"><?php echo esc_html( $excerpt ); ?></p>
                                <?php endif; ?>

                                <span class="jn-post__more" aria-hidden="true">Lasīt vairāk</span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ( $total_pages > 1 ) :
                    $range = jn_pagination_range( $current_page, $total_pages );
                    ?>
                    <nav class="pagination" aria-label="Lapu navigācija">

                        <?php if ( $current_page > 1 ) : ?>
                            <a class="pagination__arrow"
                               href="<?php echo esc_url( jn_page_url( $base, $current_page - 1 ) ); ?>"
                               rel="prev" aria-label="Iepriekšējā lapa">
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
                                   href="<?php echo esc_url( jn_page_url( $base, $p ) ); ?>"
                                   aria-label="Doties uz lapu <?php echo esc_attr( $p ); ?>">
                                    <?php echo esc_html( $p ); ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if ( $current_page < $total_pages ) : ?>
                            <a class="pagination__arrow"
                               href="<?php echo esc_url( jn_page_url( $base, $current_page + 1 ) ); ?>"
                               rel="next" aria-label="Nākamā lapa">
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