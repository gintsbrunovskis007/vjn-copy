<?php
/**
 * Template Name: Planetārijs - Cenas
 */

$content = '';
$error   = '';


$url = 'https://jaunradesnams.lv/wp-json/wp/v2/pages/28?_fields=content';

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n" .
                    "Accept: application/json\r\n" .
                    "Accept-Language: lv,en;q=0.9\r\n" .
                    "Referer: https://jaunradesnams.lv/\r\n",
        'protocol_version' => 1.1,
        'timeout' => 15,
        'ignore_errors' => true
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
]);

$json = @file_get_contents($url, false, $context);

if ($json === false) {
    $error = 'Nevarēja savienoties ar API (file_get_contents neizdevās).';
} else {

    if (strpos($json, '421 Misdirected Request') !== false) {
        $error = 'Cloudflare 421 Misdirected Request (Jūsu vietējais serveris ir bloķēts).';
    } else {
        $data = json_decode($json, true);
        if (isset($data['content']['rendered'])) {
            $content = $data['content']['rendered'];
        } else {
            $error = 'Nederīga API atbilde.';
        }
    }
}


if ( $content ) {
    $content = preg_replace( '~<style[^>]*>.*?</style>~is', '', $content );
    $content = preg_replace( '~<div class="wp-block-image">.*?</div>~is', '', $content );
    $content = preg_replace( '~<div[^>]*class="[^"]*fusion-title[^"]*"[^>]*>.*?<h3[^>]*>(.*?)</h3>.*?</div>~is', '<h3 class="price-heading">$1</h3>', $content );
    $content = preg_replace( '~<div[^>]*class="[^"]*fusion-button-wrapper[^"]*"[^>]*>.*?<a[^>]*href="([^"]+)"[^>]*>.*?<span[^>]*>(.*?)</span>.*?</a>.*?</div>~is', '<a href="$1" class="btn btn--primary" target="_blank">$2</a>', $content );
    $content = str_replace( array( 'class="fusion-table table-1"', 'class="fusion-table"' ), 'class="price-table-wrap"', $content );
    $content = str_replace( '<table', '<table class="price-table"', $content );
    $content = preg_replace( '~<p>\s*(?:&nbsp;|\s)*</p>~i', '', $content );
}

get_header();
?>

<main id="main" class="site-main" tabindex="-1">
    <section class="page-header" aria-labelledby="page-title">
        <div class="container">
            <nav class="breadcrumb" aria-label="Atrašanās vieta">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a> <span aria-hidden="true">/</span>
                <a href="<?php echo esc_url( home_url( '/planetarijs/' ) ); ?>">Planetārijs</a> <span aria-hidden="true">/</span>
                <span aria-current="page">Cenas</span>
            </nav>
            <h1 id="page-title" class="page-header__title">Biļešu cenas</h1>
            <p class="page-header__lead">Planetārija un Observatorijas apmeklējuma izcenojumi.</p>
        </div>
    </section>

    <section class="section">
        <div class="container price-content-wrap">

            <?php if ( $error ) : ?>
                <div class="notice">
                    <h3>API Bloks</h3>
                    <p><strong>Kļūda:</strong> <?php echo esc_html( $error ); ?></p>
                </div>
            <?php else : ?>
                <?php echo $content; ?>
            <?php endif; ?>

        </div>
    </section>
</main>

<?php get_footer(); ?>