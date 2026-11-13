<?php
/**
 * Template Name: Planetārijs - Noteikumi
 */

// 1. Fetch data from the API (using native PHP to bypass 421)
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

// 2. Clean up the HTML
if ( $content ) {
    // Remove inline styles
    $content = preg_replace( '~<style[^>]*>.*?</style>~is', '', $content );
    
    // Remove the flag images
    $content = preg_replace( '~<div class="wp-block-image">.*?</div>~is', '', $content );
    
    // Remove Avada title wrapper
    $content = preg_replace( '~<div[^>]*class="[^"]*fusion-title[^"]*"[^>]*>.*?<h1[^>]*>(.*?)</h1>.*?</div>~is', '', $content );

    // Remove empty paragraphs
    $content = preg_replace( '~<p>\s*(?:&nbsp;|\s)*</p>~i', '', $content );
}

get_header();
?>

<style>
    /* =========================================================
       SPACE THEME STYLING FOR RULES PAGE
    ========================================================= */
    
    body.planetarium-theme, #main {
        background-color: #0b132b !important;
        background-image: radial-gradient(circle at 15% 20%, rgba(76, 201, 240, 0.15), transparent 40%), radial-gradient(circle at 85% 80%, rgba(114, 9, 183, 0.2), transparent 50%) !important;
    }

    .rules-wrap {
        max-width: 900px;
        margin: 0 auto;
        color: #b0c4de;
        font-size: 1.05rem;
        line-height: 1.7;
    }

    /* Force headers (I, II, III...) to stand out */
    .rules-wrap p > strong {
        display: block;
        color: #4cc9f0 !important;
        font-size: 1.4rem;
        margin-top: 40px;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid rgba(76, 201, 240, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .rules-wrap p {
        margin-bottom: 20px;
    }

    /* Style the intro subtitle */
    .rules-wrap p:first-of-type {
        font-size: 1.2rem;
        color: #fff;
        font-weight: bold;
        text-align: center;
        margin-bottom: 30px;
    }

    /* Style the main ordered lists */
    .rules-wrap ol {
        list-style: none;
        counter-reset: custom-counter;
        padding-left: 0;
        margin: 0 0 30px 0;
    }

    .rules-wrap ol > li {
        counter-increment: custom-counter;
        position: relative;
        padding-left: 40px;
        margin-bottom: 16px;
    }

    .rules-wrap ol > li::before {
        content: counter(custom-counter) ".";
        position: absolute;
        left: 0;
        top: 0;
        color: #4cc9f0;
        font-weight: 800;
        font-size: 1.1rem;
    }

    /* Style the bullet lists (uls inside the rules) */
    .rules-wrap ul {
        list-style: none;
        padding-left: 20px;
        margin: 16px 0;
    }

    .rules-wrap ul li {
        position: relative;
        padding-left: 24px;
        margin-bottom: 10px;
    }

    .rules-wrap ul li::before {
        content: "•";
        position: absolute;
        left: 0;
        color: #4cc9f0;
        font-weight: bold;
        font-size: 1.5rem;
        line-height: 1;
        top: -2px;
    }

    .rules-wrap a {
        color: #4cc9f0;
        text-decoration: underline;
        text-underline-offset: 3px;
    }
    
    .rules-wrap a:hover {
        color: #fff;
    }
</style>

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

    <section class="section" style="padding-top: 20px;">
        <div class="container">
            
            <?php if ( $error ) : ?>
                <div class="notice" style="background: rgba(255,0,0,0.1); border-left: 4px solid red; color: white; padding: 20px; border-radius: 8px;">
                    <h3 style="color: #ff4d4d; margin-top: 0;">API Bloks</h3>
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