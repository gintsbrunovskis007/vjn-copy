<?php
/**
 * Template Name: Planetārijs - Repertuārs
 */

$content = '';
$error   = '';

$url = 'https://jaunradesnams.lv/wp-json/wp/v2/pages/26?_fields=content';

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

$movies = array();
$info_boxes = array();

if ( $content ) {
    if ( preg_match_all( '/<div id="post-(\d+)".*?<a href="([^"]+)">.*?<img.*?src="([^"]+)".*?<h2 class="entry-title"><a href="([^"]+)">(.*?)<\/a><\/h2>.*?<div class="fusion-post-content-container"><p>(.*?)<\/p>/is', $content, $matches, PREG_SET_ORDER ) ) {
        foreach ( $matches as $m ) {
            $title = trim( $m[5] );
            if ( empty( $title ) ) { continue; }
            
            // GET SLUG AND CREATE LOCAL ROOT LINK (WITHOUT /AKTUALITATE/)
            $slug = basename( parse_url( $m[4], PHP_URL_PATH ) );
            $local_link = home_url( '/' . $slug . '/' );

            $movies[] = array(
                'img'     => esc_url( $m[3] ),
                'title'   => esc_html( $title ),
                'excerpt' => wp_trim_words( wp_strip_all_tags( $m[6] ), 18 ),
                'link'    => $local_link,
            );
        }
    }

    if ( preg_match_all( '/<div class="reading-box-additional">(.*?)<\/div>/is', $content, $boxes ) ) {
        foreach ( $boxes[1] as $box ) {
            $info_boxes[] = trim( strip_tags( $box ) );
        }
    }
}

get_header();
?>

<style>
    body.planetarium-theme, #main {
        background-color: #0b132b !important;
        background-image: radial-gradient(circle at 15% 20%, rgba(76, 201, 240, 0.15), transparent 40%), radial-gradient(circle at 85% 80%, rgba(114, 9, 183, 0.2), transparent 50%) !important;
    }
    .repertuars-layout { display: grid; grid-template-columns: 1fr 320px; gap: 40px; max-width: 1200px; margin: 0 auto; align-items: start; }
    @media (max-width: 900px) { .repertuars-layout { grid-template-columns: 1fr; } }
    .movies-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; }
    .movie-card { display: flex; flex-direction: column; background: #132a4c !important; border: 1px solid rgba(76, 201, 240, 0.2) !important; border-radius: 14px; overflow: hidden; text-decoration: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5); transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .movie-card:hover { transform: translateY(-4px); box-shadow: 0 15px 40px rgba(0, 0, 0, 0.7); }
    .movie-card__img { width: 100%; aspect-ratio: 16/9; object-fit: cover; border-bottom: 2px solid rgba(76, 201, 240, 0.3); }
    .movie-card__body { padding: 20px; }
    .movie-card__title { color: #4cc9f0 !important; font-size: 1.25rem; margin: 0 0 10px 0 !important; line-height: 1.3; }
    .movie-card__excerpt { color: #b0c4de !important; font-size: 0.95rem; margin: 0; line-height: 1.5; }
    .info-sidebar-title { color: #ffffff; font-size: 1.5rem; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid rgba(255, 255, 255, 0.1); }
    .info-box { background: rgba(255, 255, 255, 0.05); border-left: 4px solid #4cc9f0; padding: 20px; border-radius: 8px; margin-bottom: 16px; color: #e0e1dd; font-size: 0.95rem; line-height: 1.6; }
</style>

<main id="main" class="site-main" tabindex="-1">
    <section class="page-header" aria-labelledby="page-title">
        <div class="container">
            <nav class="breadcrumb" aria-label="Atrašanās vieta">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a> <span aria-hidden="true">/</span>
                <a href="<?php echo esc_url( home_url( '/planetarijs/' ) ); ?>">Planetārijs</a> <span aria-hidden="true">/</span>
                <span aria-current="page">Repertuārs</span>
            </nav>
            <h1 id="page-title" class="page-header__title">Repertuārs</h1>
            <p class="page-header__lead">Planetārija filmu un šovu katalogs.</p>
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
                
                <div class="repertuars-layout">
                    <div class="movies-section">
                        <div class="movies-grid">
                            <?php foreach ( $movies as $movie ) : ?>
                                <a href="<?php echo $movie['link']; ?>" class="movie-card">
                                    <img src="<?php echo $movie['img']; ?>" class="movie-card__img" referrerpolicy="no-referrer" loading="lazy">
                                    <div class="movie-card__body">
                                        <h3 class="movie-card__title"><?php echo $movie['title']; ?></h3>
                                        <p class="movie-card__excerpt"><?php echo $movie['excerpt']; ?></p>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="info-sidebar">
                        <h2 class="info-sidebar-title">Informācija</h2>
                        <?php foreach ( $info_boxes as $box ) : ?>
                            <div class="info-box">
                                <?php echo esc_html( $box ); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </section>
</main>

<?php get_footer(); ?>