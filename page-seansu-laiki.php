<?php
/**
 * Template Name: Planetārijs - Seansu laiki
 */


$content = '';
$error   = '';

$url = 'https://jaunradesnams.lv/wp-json/wp/v2/pages/24?_fields=content';

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


    $content = preg_replace( '~<div[^>]*class="[^"]*fusion-clearfix[^"]*"[^>]*>\s*</div>~is', '', $content );
    $content = preg_replace( '~<div[^>]*class="[^"]*fusion-layout-column[^"]*"[^>]*>\s*<div[^>]*class="[^"]*fusion-column-wrapper[^"]*"[^>]*>\s*</div>\s*</div>~is', '', $content );


    $content = preg_replace( '~<div[^>]*class="[^"]*fusion-button-wrapper[^"]*"[^>]*>.*?<a[^>]*href="([^"]+)"[^>]*>.*?<span[^>]*>(.*?)</span>.*?</a>.*?</div>~is', '<div class="schedule-btn-wrap"><a href="$1" class="btn btn--primary" target="_blank">$2</a></div>', $content );


    $content = preg_replace( '~<p[^>]*>\s*<strong>(Pirmdiena-slēgts|Otrdiena – piektdiena|Sestdiena – svētdiena)<\/strong>(.*?)<\/p>~is', '<h3 class="schedule-day">$1 $2</h3>', $content );
    $content = preg_replace( '~<p[^>]*>\s*<strong>Pirmdiena-slēgts<\/strong>(.*?)<br>\s*<strong>Otrdiena – piektdiena<\/strong>(.*?)<\/p>~is', '<h3 class="schedule-day">Pirmdiena - slēgts</h3><h3 class="schedule-day">Otrdiena – piektdiena</h3>', $content );


    $content = preg_replace( '~<strong><u>(\d{2}:\d{2})<\/u>.*?<\/strong>~is', '<span class="schedule-time">$1</span>', $content );


    $content = preg_replace( '~<span[^>]*text-decoration:\s*underline;?[^>]*><strong>(\d{2}:\d{2})<\/strong><\/span>~is', '<span class="schedule-time">$1</span>', $content );


    $content = preg_replace( '~<span class="has-inline-color has-vivid-red-color">\*<\/span>~is', '<span class="schedule-star">*</span>', $content );
    $content = preg_replace( '~<span class="has-inline-color has-vivid-red-color">(Jaunums!)<\/span>~is', '<span class="schedule-new">$1</span>', $content );


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
                <span aria-current="page">Seansu laiki</span>
            </nav>
            <h1 id="page-title" class="page-header__title">Seansu laiki</h1>
            <p class="page-header__lead">Planetārija un Observatorijas filmu grafiks.</p>
        </div>
    </section>

    <section class="section">
        <div class="container">

            <?php if ( $error ) : ?>
                <div class="notice">
                    <h3>API Bloks</h3>
                    <p><strong>Kļūda:</strong> <?php echo esc_html( $error ); ?></p>
                </div>
            <?php else : ?>

                <div class="schedule-wrap">
                    <?php echo $content; ?>
                </div>

            <?php endif; ?>

        </div>
    </section>
</main>

<?php get_footer(); ?>