<?php
/**
 * Template Name: Planetārijs - Cenas
 */

$content = '';
$error   = '';

// 1. Fetch the API using native PHP (Bypasses cURL/421 issues)
$url = 'https://jaunradesnams.lv/wp-json/wp/v2/pages/28?_fields=content';

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n" .
                    "Accept: application/json\r\n" .
                    "Accept-Language: lv,en;q=0.9\r\n" .
                    "Referer: https://jaunradesnams.lv/\r\n",
        'protocol_version' => 1.1, // FORCE HTTP/1.1 to bypass Cloudflare 421
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
    // Check if Cloudflare blocked it
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

// 2. Clean the messy Avada HTML
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

<style>
    /* Spacey Theme CSS */
    .price-content-wrap { max-width: 900px; margin: 0 auto; color: #e0e1dd; }
    .price-heading { color: #4cc9f0 !important; font-size: 1.8rem; margin: 48px 0 24px !important; padding-bottom: 12px; border-bottom: 2px solid rgba(76,201,240,0.3); }
    .price-heading:first-child { margin-top: 0 !important; }
    .price-content-wrap ul { list-style: none; padding: 20px 24px; margin-bottom: 32px; background: rgba(255,255,255,0.05); border-radius: 14px; border: 1px solid rgba(255,255,255,0.1); }
    .price-content-wrap ul li { position: relative; padding-left: 24px; margin-bottom: 12px; color: #b0c4de; font-size: 1.05rem; }
    .price-content-wrap ul li:last-child { margin-bottom: 0; }
    .price-content-wrap ul li::before { content: "→"; position: absolute; left: 0; color: #4cc9f0; font-weight: bold; }
    .price-content-wrap p { color: #b0c4de; font-size: 1rem; line-height: 1.6; margin-bottom: 24px; }
    .price-content-wrap em { color: #fff; background: rgba(76,201,240,0.1); padding: 4px 8px; border-radius: 4px; font-style: normal; }
    .price-table-wrap { margin-bottom: 48px; overflow-x: auto; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); }
    .price-table { width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.03); text-align: left; }
    .price-table td { padding: 16px 24px; border-bottom: 1px solid rgba(255,255,255,0.08); color: #e0e1dd; }
    .price-table tr:last-child td { border-bottom: none; }
    .price-table tr:hover td { background: rgba(76,201,240,0.05); }
    .price-table tr:first-child td { background: #132a4c; color: #4cc9f0; font-weight: 800; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.05em; border-bottom: 2px solid #4cc9f0; }
    .price-table td:first-child { font-weight: 600; color: #fff; }
    .price-table td:nth-child(2) { font-weight: 800; color: #fff; }
    .price-table td:nth-child(3) { color: #b0c4de; font-size: 0.9rem; }
</style>

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
                <div class="notice" style="background: rgba(255,0,0,0.1); border-left: 4px solid red; color: white; padding: 20px; border-radius: 8px;">
                    <h3 style="color: #ff4d4d; margin-top: 0;">API Bloks</h3>
                    <p><strong>Kļūda:</strong> <?php echo esc_html( $error ); ?></p>
                </div>
            <?php else : ?>
                <?php echo $content; ?>
            <?php endif; ?>

        </div>
    </section>
</main>

<?php get_footer(); ?>