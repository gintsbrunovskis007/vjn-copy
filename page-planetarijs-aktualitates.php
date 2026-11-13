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

<style>
    html, body, #page, #content, .site, .site-content, #main, .wrapper {
        background-color: #0b132b !important;
        background-image: radial-gradient(circle at 15% 20%, rgba(76, 201, 240, 0.15), transparent 40%), radial-gradient(circle at 85% 80%, rgba(114, 9, 183, 0.2), transparent 50%) !important;
        color: #e0e1dd !important;
        max-width: 100% !important;
        width: 100% !important;
        box-shadow: none !important;
    }
    #main .container { max-width: 1200px !important; width: 100% !important; margin: 0 auto !important; padding: 0 20px !important; }
    #main .jn-latest-posts { display: grid !important; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)) !important; gap: 30px !important; width: 100% !important; margin-top: 40px; }
    #main .jn-post { background: #132a4c !important; border: 1px solid rgba(76, 201, 240, 0.2) !important; border-radius: 14px !important; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important; display: flex !important; flex-direction: column !important; text-decoration: none; }
    #main .jn-post__thumb { aspect-ratio: 16/9 !important; width: 100% !important; background: #0b132b !important; }
    #main .jn-post__thumb img { width: 100% !important; height: 100% !important; object-fit: cover !important; }
    #main .jn-post__body { padding: 24px !important; }
    #main .jn-post__title a { color: #ffffff !important; font-size: 1.3rem !important; line-height: 1.4 !important; text-decoration: none !important; }
    #main .jn-post__date { color: #4cc9f0 !important; background: transparent !important; padding: 0 !important; margin-bottom: 10px !important; font-weight: bold !important; }
    #main .jn-post__excerpt { color: #b0c4de !important; margin-bottom: 20px !important; }
    #main .jn-post__more { color: #4cc9f0 !important; margin-top: auto !important; }
    #main .page-header__title { color: #4cc9f0 !important; font-size: 3rem !important; text-align: center !important; }
    #main .page-header__lead { color: #b0c4de !important; text-align: center !important; }
    #main .pagination__num, #main .pagination__arrow { background: rgba(255, 255, 255, 0.1) !important; color: #ffffff !important; border-color: transparent !important; }
    #main .pagination__num:hover, #main .pagination__arrow:hover, #main .pagination__num.is-current { background: #4cc9f0 !important; color: #0b132b !important; }
    #main .pagination__meta { color: #b0c4de !important; }
</style>

<main id="main" class="site-main" tabindex="-1">
    <section class="page-header" aria-labelledby="page-title" style="padding-top: 60px;">
        <div class="container">
            <nav class="breadcrumb" aria-label="Atrašanās vieta" style="justify-content: center; margin-bottom: 20px;">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: #b0c4de;">Sākums</a> <span aria-hidden="true" style="color: #b0c4de;">/</span>
                <a href="<?php echo esc_url( home_url( '/planetarijs/' ) ); ?>" style="color: #b0c4de;">Planetārijs</a> <span aria-hidden="true" style="color: #b0c4de;">/</span>
                <span aria-current="page" style="color: #4cc9f0;">Aktualitātes</span>
            </nav>
            <h1 id="page-title" class="page-header__title">Planetārija aktualitātes</h1>
            <p class="page-header__lead">Jaunākās ziņas un notikumi no Planetārija un Observatorijas.</p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            
            <?php if ( $error ) : ?>
                <div class="notice" style="background: rgba(255,0,0,0.1); border-left: 4px solid red; color: white; padding: 20px; border-radius: 8px; max-width: 900px; margin: 0 auto;">
                    <h3 style="color: #ff4d4d; margin-top: 0;">API Bloks</h3>
                    <p><strong>Kļūda:</strong> <?php echo esc_html( $error ); ?></p>
                </div>
            <?php elseif ( empty( $posts ) ) : ?>
                <p class="notice" style="background: rgba(255,255,255,0.05); color: white; padding: 20px; border-radius: 8px; text-align: center;">Šeit vēl nav publicētu aktualitāšu.</p>
            <?php else : ?>
                
                <div class="jn-latest-posts">
                    <?php foreach ( $posts as $post ) : 
                        $media = $post['_embedded']['wp:featuredmedia'][0] ?? array();
                        $img = $media['source_url'] ?? '';
                        $title = $post['title']['rendered'] ?? '';
                        $slug = $post['slug'] ?? '';
                        $date = $post['date'] ?? '';
                        $excerpt = wp_trim_words( wp_strip_all_tags( $post['excerpt']['rendered'] ?? '' ), 24 );
                        
                        // LOCAL LINK WITHOUT /AKTUALITATE/
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
                                <span class="pagination__gap" aria-hidden="true" style="color: white;">…</span>
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
                    <p class="pagination__meta" style="text-align: center; margin-top: 20px;">Lapa <?php echo esc_html( $page ); ?> no <?php echo esc_html( $total_pages ); ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php get_footer(); ?>