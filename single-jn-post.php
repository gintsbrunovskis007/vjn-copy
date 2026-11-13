<?php


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

$title   = wp_strip_all_tags( $post['title']['rendered'] ?? '' );
$date    = $post['date'] ?? '';
$content = $post['content']['rendered'] ?? '';
$link    = $post['link'] ?? '';

$media = $post['_embedded']['wp:featuredmedia'][0] ?? array();
$hero  = $media['source_url'] ?? '';
$alt   = $media['alt_text'] ?? $title;


$content = preg_replace( '#<p>\s*Publicēts?:.*?</p>#is', '', $content );


$categories = $post['categories'] ?? array();
$is_planetarium_post = in_array( 7, $categories );

if ( $is_planetarium_post ) {
    $GLOBALS['is_planetarium_post'] = true;
}

$back_url = $is_planetarium_post ? home_url( '/planetarijs/aktualitates/' ) : home_url( '/jaunrades-nams/aktualitates/' );
$back_text = $is_planetarium_post ? '← Atpakaļ uz Planetārija aktualitātēm' : '← Atpakaļ uz aktualitātēm';
$cat_name = $is_planetarium_post ? 'Planetārijs' : 'Aktualitātes';

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

    <article class="single-post">

        <header class="single-post__header<?php echo $hero ? ' single-post__header--with-image' : ''; ?>"
                <?php if ( $hero ) : ?>style="--hero-image: url('<?php echo esc_url( $hero ); ?>');"<?php endif; ?>>
            <div class="container">
                <nav class="breadcrumb breadcrumb--light" aria-label="Atrašanās vieta">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a>
                    <span aria-hidden="true">/</span>
                    <a href="<?php echo esc_url( $back_url ); ?>"><?php echo esc_html( $cat_name ); ?></a>
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
                    <img src="<?php echo esc_url( $hero ); ?>" alt="<?php echo esc_attr( $alt ); ?>" referrerpolicy="no-referrer">
                </figure>
            <?php endif; ?>

            <div class="single-post__content">
                <?php echo $content; ?>
            </div>

            <div class="single-post__back">
                <a class="btn btn--ghost" href="<?php echo esc_url( $back_url ); ?>"><?php echo esc_html( $back_text ); ?></a>
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