<?php
/**
 * Single aktualitāte rendered from the jaunradesnams.lv REST API.
 * Reached via /aktualitate/<slug>/
 */

$slug = get_query_var( 'jn_single_post' );
if ( ! $slug ) {
    get_header();
    echo '<main class="container"><p class="notice">Aktualitāte nav atrasta.</p></main>';
    get_footer();
    return;
}

$cache_key = 'jn_post_' . md5( $slug );
$post = get_transient( $cache_key );
if ( ! is_array( $post ) ) {
    $data = jn_api_get( 'posts', array(
        'slug'     => $slug,
        'per_page' => 1,
        '_embed'   => 1,
    ) );
    if ( empty( $data[0] ) ) {
        get_header();
        echo '<main class="container"><p class="notice">Aktualitāte nav atrasta.</p></main>';
        get_footer();
        return;
    }
    $post = $data[0];
    set_transient( $cache_key, $post, 15 * MINUTE_IN_SECONDS );
}

// -------- Extract fields --------
$title   = wp_strip_all_tags( $post['title']['rendered'] ?? '' );
$date    = $post['date'] ?? '';
$content = $post['content']['rendered'] ?? '';
$link    = $post['link'] ?? '';

$media = $post['_embedded']['wp:featuredmedia'][0] ?? array();
$hero  = $media['source_url'] ?? '';
$alt   = $media['alt_text'] ?? $title;

// Strip the trailing "Publicēts: dd.mm.yyyy." paragraph from the content
$content = preg_replace( '#<p>\s*Publicēts?:.*?</p>#is', '', $content );

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

    <article class="single-post">

        <header class="single-post__header<?php echo $hero ? ' single-post__header--with-image' : ''; ?>"
                <?php if ( $hero ) : ?>style="background-image: linear-gradient(rgba(92,8,28,0.78), rgba(92,8,28,0.92)), url('<?php echo esc_url( $hero ); ?>');"<?php endif; ?>>
            <div class="container">
                <nav class="breadcrumb breadcrumb--light" aria-label="Atrašanās vieta">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a>
                    <span aria-hidden="true">/</span>
                    <a href="<?php echo esc_url( home_url( '/jaunrades-nams/aktualitates/' ) ); ?>">Aktualitātes</a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page"><?php echo esc_html( wp_trim_words( $title, 6, '…' ) ); ?></span>
                </nav>

                <?php if ( $date ) : ?>
                    <p class="single-post__date">
                        <time datetime="<?php echo esc_attr( $date ); ?>">
                            <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ); ?>
                        </time>
                    </p>
                <?php endif; ?>

                <h1 class="single-post__title"><?php echo esc_html( $title ); ?></h1>
            </div>
        </header>

        <div class="container single-post__body-wrap">

            <?php if ( $hero ) : ?>
                <figure class="single-post__hero-img">
                    <img src="<?php echo esc_url( $hero ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
                </figure>
            <?php endif; ?>

            <div class="single-post__content">
                <?php
                // The API returns rendered HTML from the original site.
                // We trust it (it's the same content the real site serves).
                echo $content; // phpcs:ignore WordPress.Security.EscapeOutput
                ?>
            </div>

            <div class="single-post__back">
                <a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/jaunrades-nams/aktualitates/' ) ); ?>">← Atpakaļ uz aktualitātēm</a>
                <?php if ( $link ) : ?>
                    <a class="single-post__source" href="<?php echo esc_url( $link ); ?>" rel="noopener" target="_blank">
                        Skatīt oriģinālā ↗
                    </a>
                <?php endif; ?>
            </div>

        </div>

    </article>

</main>

<?php get_footer(); ?>