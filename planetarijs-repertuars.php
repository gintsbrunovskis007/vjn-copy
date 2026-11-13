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

    <section class="section section--tight">
        <div class="container">

            <?php if ( $error ) : ?>
                <div class="notice">
                    <h3>API Bloks</h3>
                    <p><strong>Kļūda:</strong> <?php echo esc_html( $error ); ?></p>
                </div>
            <?php else : ?>

                <div class="repertuars-layout">
                    <div class="movies-section">
                        <div class="movies-grid">
                            <?php foreach ( $movies as $movie ) : ?>
                                <a href="<?php echo esc_url( $movie['link'] ); ?>" class="movie-card">
                                    <img src="<?php echo esc_url( $movie['img'] ); ?>" class="movie-card__img" alt="<?php echo esc_attr( wp_strip_all_tags( $movie['title'] ) ); ?>" referrerpolicy="no-referrer" loading="lazy">
                                    <div class="movie-card__body">
                                        <h3 class="movie-card__title"><?php echo wp_kses_post( $movie['title'] ); ?></h3>
                                        <p class="movie-card__excerpt"><?php echo wp_kses_post( $movie['excerpt'] ); ?></p>
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