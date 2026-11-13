<?php
/**
 * Single nodarbība rendered from the jaunradesnams.lv REST API.
 */

$slug = get_query_var( 'jn_single_program' );

if ( ! $slug ) {
    get_header();
    echo '<main class="container"><p class="notice">Nodarbība nav atrasta.</p></main>';
    get_footer();
    return;
}

// Extract category from URL path: /nodarbiba/<kat>/<slug>/
$kat      = '';
$raw_uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
$req_path = trim( (string) wp_parse_url( $raw_uri, PHP_URL_PATH ), '/' );
$segments = $req_path ? explode( '/', $req_path ) : array();
$pos      = array_search( 'nodarbiba', $segments, true );
if ( false !== $pos && isset( $segments[ $pos + 2 ] ) ) {
    $kat = sanitize_key( $segments[ $pos + 1 ] );
}

$hub_names = array(
    'kulturizglitibas-programmas'    => 'Kultūrizglītība',
    'stem'                           => 'STEM',
    'sports-un-fiziskas-aktivitates' => 'Sports',
    'socialas-un-pilsoniskas-jomas'  => 'Sociālās programmas',
);
$cat_name = isset( $hub_names[ $kat ] ) ? $hub_names[ $kat ] : ucfirst( str_replace( '-', ' ', $kat ) );

$page = jn_get_page_by_slug( $slug );

if ( ! $page ) {
    get_header();
    echo '<main class="container"><p class="notice">Nodarbība nav atrasta.</p></main>';
    get_footer();
    return;
}

$title   = wp_strip_all_tags( $page['title']['rendered'] ?? '' );
$content = $page['content']['rendered'] ?? '';
$link    = $page['link'] ?? '';

$hero = ''; $alt = $title;
if ( preg_match( '~<img[^>]+src="([^"]+)"[^>]*>~i', $content, $m ) ) {
    $hero = $m[1];
    if ( preg_match( '~alt="([^"]*)"~i', $m[0], $a ) ) {
        $alt = $a[1] ?: $title;
    }
    $content = preg_replace( '~<img[^>]+' . preg_quote( $hero, '~' ) . '[^>]*>~i', '', $content, 1 );
}

$content = preg_replace( '~<div[^>]*fusion-separator[^>]*>.*?</div>~is', '', $content );
$content = preg_replace( '~<div[^>]*fusion-sep-clear[^>]*>\s*</div>~is', '', $content );
$content = preg_replace( '~<div[^>]*fusion-clearfix[^>]*>\s*</div>~is', '', $content );
$content = preg_replace( '~<p>\s*(?:&nbsp;|\s)*</p>~i', '', $content );

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

    <article class="single-program">

        <header class="single-program__header<?php echo $hero ? ' single-program__header--with-image' : ''; ?>"
                <?php if ( $hero ) : ?>style="background-image: linear-gradient(rgba(92,8,28,0.78), rgba(92,8,28,0.92)), url('<?php echo esc_url( $hero ); ?>');"<?php endif; ?>>
            <div class="container">
                <nav class="breadcrumb breadcrumb--light" aria-label="Atrašanās vieta">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a>
                    <span aria-hidden="true">/</span>
                    <a href="<?php echo esc_url( home_url( '/jaunrades-nams/pulcini/' ) ); ?>">Nodarbības</a>
                    <?php if ( $kat ) : ?>
                        <span aria-hidden="true">/</span>
                        <a href="<?php echo esc_url( add_query_arg( 'kat', $kat, home_url( '/jaunrades-nams/pulcini/' ) ) ); ?>"><?php echo esc_html( $cat_name ); ?></a>
                    <?php endif; ?>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page"><?php echo esc_html( $title ); ?></span>
                </nav>

                <h1 class="single-program__title"><?php echo esc_html( $title ); ?></h1>
            </div>
        </header>

        <div class="container single-program__body-wrap">

            <?php if ( $hero ) : ?>
                <figure class="single-program__hero-img">
                    <img src="<?php echo esc_url( $hero ); ?>" alt="<?php echo esc_attr( $alt ); ?>" referrerpolicy="no-referrer">
                </figure>
            <?php endif; ?>

            <div class="single-program__content">
                <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput — trusted same-site source ?>
            </div>

            <div class="single-program__back">
                <a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/jaunrades-nams/pulcini/' ) ); ?>">← Atpakaļ uz nodarbībām</a>
                <?php if ( $link ) : ?>
                    <a class="single-program__source" href="<?php echo esc_url( $link ); ?>" rel="noopener" target="_blank">
                        Skatīt oriģinālā ↗
                    </a>
                <?php endif; ?>
            </div>

        </div>

    </article>

</main>

<?php get_footer(); ?>