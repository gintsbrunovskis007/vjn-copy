<?php
/**
 * Template Name: Planetārijs - Seansu laiki
 */

// 1. Fetch data from the API
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

// 2. Clean and format the HTML
if ( $content ) {
    // Remove inline styles
    $content = preg_replace( '~<style[^>]*>.*?</style>~is', '', $content );
    
    // Remove Avada clearfix and animated wrappers
    $content = preg_replace( '~<div[^>]*class="[^"]*fusion-clearfix[^"]*"[^>]*>\s*</div>~is', '', $content );
    $content = preg_replace( '~<div[^>]*class="[^"]*fusion-layout-column[^"]*"[^>]*>\s*<div[^>]*class="[^"]*fusion-column-wrapper[^"]*"[^>]*>\s*</div>\s*</div>~is', '', $content );
    
    // Clean Avada Button -> Custom Button
    $content = preg_replace( '~<div[^>]*class="[^"]*fusion-button-wrapper[^"]*"[^>]*>.*?<a[^>]*href="([^"]+)"[^>]*>.*?<span[^>]*>(.*?)</span>.*?</a>.*?</div>~is', '<div class="schedule-btn-wrap"><a href="$1" class="btn btn--primary" target="_blank">$2</a></div>', $content );

    // Format Days into H3 headers
    $content = preg_replace( '~<p[^>]*>\s*<strong>(Pirmdiena-slēgts|Otrdiena – piektdiena|Sestdiena – svētdiena)<\/strong>(.*?)<\/p>~is', '<h3 class="schedule-day">$1 $2</h3>', $content );
    $content = preg_replace( '~<p[^>]*>\s*<strong>Pirmdiena-slēgts<\/strong>(.*?)<br>\s*<strong>Otrdiena – piektdiena<\/strong>(.*?)<\/p>~is', '<h3 class="schedule-day">Pirmdiena - slēgts</h3><h3 class="schedule-day">Otrdiena – piektdiena</h3>', $content );

    // FIXED: Wrap times in a styled span (Matches ANY characters between </u> and </strong>)
    $content = preg_replace( '~<strong><u>(\d{2}:\d{2})<\/u>.*?<\/strong>~is', '<span class="schedule-time">$1</span>', $content );
    
    // Handle the 19:00 time which is inside a span
    $content = preg_replace( '~<span[^>]*text-decoration:\s*underline;?[^>]*><strong>(\d{2}:\d{2})<\/strong><\/span>~is', '<span class="schedule-time">$1</span>', $content );

    // Highlight red asterisks (Observatorija)
    $content = preg_replace( '~<span class="has-inline-color has-vivid-red-color">\*<\/span>~is', '<span class="schedule-star">*</span>', $content );
    $content = preg_replace( '~<span class="has-inline-color has-vivid-red-color">(Jaunums!)<\/span>~is', '<span class="schedule-new">$1</span>', $content );

    // Remove empty paragraphs
    $content = preg_replace( '~<p>\s*(?:&nbsp;|\s)*</p>~i', '', $content );
}

get_header();
?>

<style>
    /* Spacey Theme CSS for Schedule */
    .schedule-wrap { max-width: 900px; margin: 0 auto; color: #e0e1dd; font-size: 1.05rem; }
    
    .schedule-wrap h3.schedule-day { 
        color: #4cc9f0; 
        font-size: 1.5rem; 
        margin: 40px 0 20px !important; 
        padding-bottom: 10px; 
        border-bottom: 2px solid rgba(76,201,240,0.3); 
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .schedule-wrap h3.schedule-day:first-child { margin-top: 0 !important; }

    .schedule-wrap p { 
        margin-bottom: 12px; 
        display: flex; 
        align-items: flex-start; 
        gap: 15px; 
        line-height: 1.5;
    }
    
    .schedule-time { 
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #132a4c;
        color: #4cc9f0;
        font-family: monospace;
        font-size: 1.1rem;
        font-weight: bold;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid rgba(76, 201, 240, 0.4);
        min-width: 65px;
        flex-shrink: 0;
    }

    .schedule-star { color: #ff4d4d; font-weight: bold; font-size: 1.2rem; }
    .schedule-new { 
        background: #4cc9f0; 
        color: #0b132b; 
        font-size: 0.7rem; 
        font-weight: 800; 
        padding: 2px 6px; 
        border-radius: 4px; 
        text-transform: uppercase; 
        margin-left: 8px;
    }

    .schedule-wrap ul { 
        list-style: none; 
        padding: 20px 24px; 
        margin: 30px 0; 
        background: rgba(255,255,255,0.05); 
        border-radius: 14px; 
        border: 1px solid rgba(255,255,255,0.1); 
    }
    .schedule-wrap ul li { 
        position: relative; 
        padding-left: 24px; 
        margin-bottom: 12px; 
        color: #b0c4de; 
    }
    .schedule-wrap ul li:last-child { margin-bottom: 0; }
    .schedule-wrap ul li::before { 
        content: "→"; 
        position: absolute; 
        left: 0; 
        color: #4cc9f0; 
        font-weight: bold; 
    }
    
    .schedule-wrap em { 
        color: #fff; 
        background: rgba(76,201,240,0.1); 
        padding: 2px 6px; 
        border-radius: 4px; 
        font-style: normal; 
    }

    .schedule-btn-wrap {
        text-align: center;
        margin-top: 40px;
    }
</style>

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
                <div class="notice" style="background: rgba(255,0,0,0.1); border-left: 4px solid red; color: white; padding: 20px; border-radius: 8px; max-width: 900px; margin: 0 auto;">
                    <h3 style="color: #ff4d4d; margin-top: 0;">API Bloks</h3>
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