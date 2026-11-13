<?php
/**
 * Template Name: Piedāvājums izglītības iestādēm
 */

$page_id   = 24319;
$cache_key = 'jn_edu_offer_v2';
$html      = get_transient( $cache_key );

if ( false === $html ) {
    $r = wp_remote_get(
        "https://jaunradesnams.lv/wp-json/wp/v2/pages/{$page_id}?_fields=content",
        array( 'timeout' => 15, 'user-agent' => 'VJN/1.0' )
    );
    if ( ! is_wp_error( $r ) && 200 === wp_remote_retrieve_response_code( $r ) ) {
        $data = json_decode( wp_remote_retrieve_body( $r ), true );
        $html = $data['content']['rendered'] ?? '';
        set_transient( $cache_key, $html, 15 * MINUTE_IN_SECONDS );
    }
}

if ( $html ) {
    // Only convert <center> → .offer-price. Do NOT strip Avada separators here —
    // they contain nested <div>s, and `.*?</div>` leaves orphan closing tags
    // that corrupt the DOM tree. CSS hides them safely instead.
    $html = preg_replace( '#<center>(.*?)</center>#is', '<div class="offer-price">$1</div>', $html );
    $html = '<div class="jn-offer-wrap">' . $html . '</div>';
}

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

    <section class="page-header" aria-labelledby="page-title">
        <div class="container">
            <nav class="breadcrumb" aria-label="Atrašanās vieta">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a>
                <span aria-hidden="true">/</span>
                <a href="<?php echo esc_url( home_url( '/jaunrades-nams/dokumenti/' ) ); ?>">Resursi</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Piedāvājums izglītības iestādēm</span>
            </nav>
            <h1 id="page-title" class="page-header__title">Piedāvājums izglītības iestādēm</h1>
            <p class="page-header__lead">Ekskursijas, laboratorijas darbi un planetārija seansi skolēnu grupām.</p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <?php if ( $html ) : ?>
                <?php echo $html; // pre-cleaned, structure untouched ?>
            <?php else : ?>
                <p class="notice">Informācija šobrīd nav pieejama.</p>
            <?php endif; ?>

            <div style="text-align:center; margin-top:64px;">
                <a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/jaunrades-nams/dokumenti/' ) ); ?>">← Atpakaļ uz resursiem</a>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>