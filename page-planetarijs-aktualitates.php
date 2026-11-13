<?php
/**
 * Template Name: Planetārijs - Aktualitātes
 */

$page = isset( $_GET['strana'] ) ? max( 1, (int) $_GET['strana'] ) : 1;
$cache_key = 'jn_planet_news_p' . $page . '_v9';
$data = get_transient( $cache_key );

if ( false === $data ) {
    $url = "https://jaunradesnams.lv/wp-json/wp/v2/posts?categories=7&per_page=9&page={$page}&_embed";

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
        $data = array( 'posts' => array(), 'total_pages' => 0, 'error' => 'Nevarēja savienoties ar API.' );
    } elseif (strpos($json, '421 Misdirected Request') !== false) {
        $data = array( 'posts' => array(), 'total_pages' => 0, 'error' => 'Cloudflare 421 (Jūsu lokālais serveris ir bloķēts).' );
    } else {
        $headers = $http_response_header ?? [];
        $total_pages = 1;
        foreach ($headers as $h) {
            if (stripos($h, 'x-wp-totalpages:') !== false) {
                $total_pages = (int) trim(substr($h, strpos($h, ':') + 1));
            }
        }
        $posts = json_decode($json, true);
        if ( ! is_array( $posts ) ) {
            $data = array( 'posts' => array(), 'total_pages' => 0, 'error' => 'Nederīga API atbilde.' );
        } else {
            $data = array( 'posts' => $posts, 'total_pages' => max( 1, $total_pages ), 'error' => '' );
            set_transient( $cache_key, $data, 15 * MINUTE_IN_SECONDS );
        }
    }
}

$posts = $data['posts'];
$total_pages = $data['total_pages'];
$error = $data['error'] ?? '';

$base_url = get_permalink();
$pg_url = function( $n ) use ( $base_url ) {
    return $n > 1 ? add_query_arg( 'strana', $n, $base_url ) : $base_url;
};

$range = array();
if ( $total_pages > 1 ) {
    for ( $i = 1; $i <= $total_pages; $i++ ) {
        if ( 1 === $i || $total_pages === $i || ( $i >= $page - 1 && $i <= $page + 1 ) ) {
            $range[] = $i;
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
                <span aria-current="page">Aktualitātes</span>
            </nav>
            <h1 id="page-title" class="page-header__title">Planetārija aktualitātes</h1>
            <p class="page-header__lead">Jaunākās ziņas un notikumi no Planetārija un Observatorijas.</p>
        </div>
    </section>

    <section class="section">
        <div class="container">

            <?php if ( $error ) : ?>
                <div class="notice">
                    <h3>API Bloks</h3>
                    <p><strong>Kļūda:</strong> <?php echo esc_html( $error ); ?></p>
                </div>
            <?php elseif ( empty( $posts ) ) : ?>
                <p class="notice">Šeit vēl nav publicētu aktualitāšu.</p>
            <?php else : ?>

                <div class="jn-latest-posts">
                    <?php foreach ( $posts as $post ) :
                        $media = $post['_embedded']['wp:featuredmedia'][0] ?? array();
                        $img = $media['source_url'] ?? '';
                        $title = $post['title']['rendered'] ?? '';
                        $slug = $post['slug'] ?? '';
                        $date = $post['date'] ?? '';
                        $excerpt = wp_trim_words( wp_strip_all_tags( $post['excerpt']['rendered'] ?? '' ), 24 );


                        $link = home_url( '/' . $slug . '/' );
                        ?>
                        <article class="jn-post">
                            <?php if ( $img ) : ?>
                                <div class="jn-post__thumb">
                                    <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" referrerpolicy="no-referrer">
                                </div>
                            <?php endif; ?>
                            <div class="jn-post__body">
                                <p class="jn-post__date"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ); ?></p>
                                <h2 class="jn-post__title">
                                    <a href="<?php echo esc_url( $link ); ?>"><?php echo wp_kses_post( $title ); ?></a>
                                </h2>
                                <p class="jn-post__excerpt"><?php echo esc_html( $excerpt ); ?></p>
                                <span class="jn-post__more" aria-hidden="true">Lasīt vairāk →</span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ( $total_pages > 1 ) : ?>
                    <nav class="pagination" aria-label="Lapu navigācija">
                        <?php if ( $page > 1 ) : ?>
                            <a class="pagination__arrow" href="<?php echo esc_url( $pg_url( $page - 1 ) ); ?>" rel="prev"><span aria-hidden="true">←</span></a>
                        <?php else : ?>
                            <span class="pagination__arrow is-disabled" aria-hidden="true">←</span>
                        <?php endif; ?>

                        <?php
                        $prev = 0;
                        foreach ( $range as $p ) :
                            if ( $prev && $p - $prev > 1 ) : ?>
                                <span class="pagination__gap" aria-hidden="true">…</span>
                            <?php endif;
                            $prev = $p;

                            if ( $p === $page ) : ?>
                                <span class="pagination__num is-current" aria-current="page"><?php echo esc_html( $p ); ?></span>
                            <?php else : ?>
                                <a class="pagination__num" href="<?php echo esc_url( $pg_url( $p ) ); ?>"><?php echo esc_html( $p ); ?></a>
                            <?php endif;
                        endforeach; ?>

                        <?php if ( $page < $total_pages ) : ?>
                            <a class="pagination__arrow" href="<?php echo esc_url( $pg_url( $page + 1 ) ); ?>" rel="next"><span aria-hidden="true">→</span></a>
                        <?php else : ?>
                            <span class="pagination__arrow is-disabled" aria-hidden="true">→</span>
                        <?php endif; ?>
                    </nav>
                    <p class="pagination__meta">Lapa <?php echo esc_html( $page ); ?> no <?php echo esc_html( $total_pages ); ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php get_footer(); ?>