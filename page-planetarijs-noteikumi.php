<?php
/**
 * Template Name: Planetārijs - Noteikumi
 */


$content = '';
$error   = '';

$url = 'https://jaunradesnams.lv/wp-json/wp/v2/pages/63?_fields=content';

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n" .
                    "Accept: application/json\r\n" .
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
    $error = 'Nevarēja savienoties ar API.';
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


    $content = preg_replace( '~<div[^>]*class="[^"]*fusion-title[^"]*"[^>]*>.*?<h1[^>]*>(.*?)</h1>.*?</div>~is', '', $content );


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
                <span aria-current="page">Noteikumi</span>
            </nav>
            <h1 id="page-title" class="page-header__title">Noteikumi</h1>
            <p class="page-header__lead">Iekšējās kārtības un drošības noteikumi.</p>
        </div>
    </section>

    <section class="section section--tight">
        <div class="container">

            <?php if ( $error ) : ?>
                <div class="notice">
                    <h3>API Bloks</h3>
                    <p><strong>Kļūda:</strong> <?php echo esc_html( $error ); ?></p>
                </div>
            <?php else : ?>

                <div class="rules-wrap">
                    <?php echo $content; ?>
                </div>

            <?php endif; ?>

        </div>
    </section>
</main>

<?php get_footer(); ?>