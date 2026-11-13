<?php
/**
 * Template Name: Planetārijs - Darbības apraksts
 */

// 1. Fetch data from the API
$content = '';
$error   = '';

$url = 'https://jaunradesnams.lv/wp-json/wp/v2/pages/61?_fields=content';

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

// 2. Clean the HTML from Avada junk
if ( $content ) {
    $content = preg_replace( '~<style[^>]*>.*?</style>~is', '', $content );
    $content = preg_replace( '~<p>\s*(?:&nbsp;|\s)*</p>~i', '', $content );
}

get_header();
?>

<style>
    /* =========================================================
       FIX AVADA LAYOUT AND FORCE SPACE THEME
    ========================================================= */
    
    body.planetarium-theme, #main {
        background-color: #0b132b !important;
        background-image: radial-gradient(circle at 15% 20%, rgba(76, 201, 240, 0.15), transparent 40%), radial-gradient(circle at 85% 80%, rgba(114, 9, 183, 0.2), transparent 50%) !important;
    }

    .darbibas-wrap {
        max-width: 1000px;
        margin: 0 auto;
        color: #e0e1dd;
    }

    /* Hide the sidebar with flags and recent posts */
    .darbibas-wrap .fusion-one-fifth {
        display: none !important;
    }

    /* Make main container full width */
    .darbibas-wrap .fusion-four-fifth {
        float: none !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* TURN THE INNER WRAPPER INTO A 2-COLUMN GRID */
    .darbibas-wrap .fusion-four-fifth > .fusion-column-wrapper {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.5fr) !important; /* Image gets 1fr, Text gets 1.5fr */
        gap: 40px !important;
        align-items: center !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Reset the Avada columns inside the grid */
    .darbibas-wrap .fusion-one-third,
    .darbibas-wrap .fusion-two-third {
        width: 100% !important;
        float: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Reset inner wrappers */
    .darbibas-wrap .fusion-one-third > .fusion-column-wrapper,
    .darbibas-wrap .fusion-two-third > .fusion-column-wrapper {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* THIS IS THE FIX: Hide all Avada clearfixes so they don't break the grid */
    .darbibas-wrap .fusion-clearfix,
    .darbibas-wrap .fusion-separator,
    .darbibas-wrap .fusion-sep-clear {
        display: none !important;
    }

    /* Fix Images */
    .darbibas-wrap img {
        width: 100% !important;
        height: auto !important;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(76, 201, 240, 0.2);
        margin: 0 !important;
        display: block;
    }

    /* Fix Text */
    .darbibas-wrap p {
        color: #b0c4de !important;
        font-size: 1.05rem;
        line-height: 1.7;
        margin-bottom: 16px !important;
    }

    .darbibas-wrap h2 {
        color: #4cc9f0 !important;
        font-size: 1.8rem !important;
        margin: 0 0 20px 0 !important;
        padding-bottom: 10px;
        border-bottom: 2px solid rgba(76, 201, 240, 0.3);
    }

    .darbibas-wrap .fusion-title {
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Fix Lists */
    .darbibas-wrap ul {
        list-style: none !important;
        padding: 0 !important;
        margin: 20px 0 !important;
    }

    .darbibas-wrap ul li {
        position: relative;
        padding-left: 24px;
        margin-bottom: 10px;
        color: #b0c4de !important;
    }

    .darbibas-wrap ul li::before {
        content: "→";
        position: absolute;
        left: 0;
        color: #4cc9f0;
        font-weight: bold;
    }

    .darbibas-wrap em {
        color: #fff;
        background: rgba(76, 201, 240, 0.1);
        padding: 4px 8px;
        border-radius: 4px;
        font-style: normal;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .darbibas-wrap .fusion-four-fifth > .fusion-column-wrapper {
            grid-template-columns: 1fr !important;
            gap: 20px !important;
            margin-bottom: 40px !important;
        }
    }
</style>

<main id="main" class="site-main" tabindex="-1">
    <section class="page-header" aria-labelledby="page-title">
        <div class="container">
            <nav class="breadcrumb" aria-label="Atrašanās vieta">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a> <span aria-hidden="true">/</span>
                <a href="<?php echo esc_url( home_url( '/planetarijs/' ) ); ?>">Planetārijs</a> <span aria-hidden="true">/</span>
                <span aria-current="page">Darbības apraksts</span>
            </nav>
            <h1 id="page-title" class="page-header__title">Darbības apraksts</h1>
            <p class="page-header__lead">Iepazīstieties ar mūsu Planetāriju un Observatoriju.</p>
        </div>
    </section>

    <section class="section" style="padding-top: 20px;">
        <div class="container darbibas-wrap">
            
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