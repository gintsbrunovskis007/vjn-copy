<?php
/**
 * Template Name: Planetārijs - Galerija
 */

if ( ! function_exists( 'jn_load_planet_gallery' ) ) {
    function jn_load_planet_gallery() {

        $cache = get_transient( 'jn_planet_albums_v3' );
        if ( is_array( $cache ) ) { return $cache; }

        $albums = array();


        $url = 'https://jaunradesnams.lv/wp-json/wp/v2/pages/65?_fields=content';
        $r   = wp_remote_get( $url, array( 'timeout' => 20, 'user-agent' => 'VJN/1.0' ) );

        if ( ! is_wp_error( $r ) && 200 === wp_remote_retrieve_response_code( $r ) ) {
            $page = json_decode( wp_remote_retrieve_body( $r ), true );
            $html = $page['content']['rendered'] ?? '';

            if ( ! empty( $html ) && strpos( $html, 'ngg-album-compact' ) !== false ) {
                if ( preg_match_all( '#<div\s+class="ngg-album-compact"[^>]*>(.*?)<h4>(.*?)</h4>.*?<strong>(\d+)</strong>#is', $html, $matches, PREG_SET_ORDER ) ) {
                    foreach ( $matches as $m ) {
                        $block = $m[1];
                        $title = trim( wp_strip_all_tags( $m[2] ) );
                        $count = (int) $m[3];

                        $album_link = '';
                        if ( preg_match( '#<a[^>]+href=[\'"]([^\'"]+)[\'"]#', $block, $lm ) ) {
                            $album_link = html_entity_decode( $lm[1], ENT_QUOTES, 'UTF-8' );
                        }
                        $thumb = '';
                        if ( preg_match( '#<img[^>]+src=[\'"]([^\'"]+)[\'"]#', $block, $im ) ) {
                            $thumb = html_entity_decode( $im[1], ENT_QUOTES, 'UTF-8' );
                        }

                        if ( ! $album_link || ! $title || ! $thumb ) { continue; }

                        $albums[] = array(
                            'title' => $title,
                            'link'  => $album_link,
                            'thumb' => $thumb,
                            'count' => $count,
                        );
                    }
                }
            }
        }

        if ( ! empty( $albums ) ) {
            set_transient( 'jn_planet_albums_v3', $albums, 15 * MINUTE_IN_SECONDS );
        }
        return $albums;
    }
}


$album_url = isset( $_GET['albums'] ) ? urldecode( wp_unslash( $_GET['albums'] ) ) : '';
$is_detail = false;

if ( $album_url && strpos( $album_url, 'jaunradesnams.lv' ) !== false && strpos( $album_url, '/galerija/' ) !== false ) {
    $is_detail = true;
}

if ( $is_detail ) {
    $cache_key = 'jn_planet_album_imgs_v3_' . md5( $album_url );
    $images    = get_transient( $cache_key );

    if ( ! is_array( $images ) || empty( $images ) ) {
        $images = array();
        $r = wp_remote_get( $album_url, array( 'timeout' => 25, 'user-agent' => 'Mozilla/5.0' ) );
        if ( ! is_wp_error( $r ) && 200 === wp_remote_retrieve_response_code( $r ) ) {
            $html = wp_remote_retrieve_body( $r );


            $gallery_html = $html;
            if ( preg_match( '#<div[^>]*class="[^"]*ngg-galleryoverview[^"]*"[^>]*>(.*?)<div[^>]*class="[^"]*ngg-navigation#is', $html, $gm ) ) {
                $gallery_html = $gm[1];
            }

            if ( preg_match_all( '#<a\s[^>]*href=["\']([^"\']+\.(?:jpe?g|png|gif|webp))["\'][^>]*>#i', $gallery_html, $anchors, PREG_OFFSET_CAPTURE ) ) {
                $seen = array();
                $total = count( $anchors[0] );
                for ( $i = 0; $i < $total; $i++ ) {
                    $a_html = $anchors[0][ $i ][0];
                    $a_pos  = $anchors[0][ $i ][1];
                    $full   = html_entity_decode( $anchors[1][ $i ][0], ENT_QUOTES, 'UTF-8' );

                    if ( ! $full || isset( $seen[ $full ] ) ) { continue; }
                    $seen[ $full ] = true;

                    $window_end = ( isset( $anchors[0][ $i + 1 ] ) ) ? $anchors[0][ $i + 1 ][1] : strlen( $gallery_html );
                    $window = substr( $gallery_html, $a_pos + strlen( $a_html ), $window_end - ( $a_pos + strlen( $a_html ) ) );

                    if ( preg_match( '#<img[^>]+src=["\']([^"\']+)["\']#i', $window, $im ) ) {
                        $thumb = html_entity_decode( $im[1], ENT_QUOTES, 'UTF-8' );
                        $mid = preg_replace( '#-(\d+)x(\d+)x(\d+)-#', '-640x480x100-', $thumb );
                        $images[] = array( 'thumb' => $mid ?: $thumb, 'full' => $full );
                    }
                }
            }
        }
        if ( ! empty( $images ) ) { set_transient( $cache_key, $images, 15 * MINUTE_IN_SECONDS ); }
    }

    $album_title = ucwords( str_replace( array( '-', '_' ), ' ', urldecode( basename( rtrim( wp_parse_url( $album_url, PHP_URL_PATH ), '/' ) ) ) ) );
    $index_url = get_permalink();

    get_header();
    ?>
    <main id="main" class="site-main" tabindex="-1">
        <section class="page-header" aria-labelledby="page-title">
            <div class="container">
                <nav class="breadcrumb" aria-label="Atrašanās vieta">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a> <span aria-hidden="true">/</span>
                    <a href="<?php echo esc_url( home_url( '/planetarijs/' ) ); ?>">Planetārijs</a> <span aria-hidden="true">/</span>
                    <a href="<?php echo esc_url( $index_url ); ?>">Galerija</a> <span aria-hidden="true">/</span>
                    <span aria-current="page"><?php echo esc_html( wp_trim_words( $album_title, 6, '…' ) ); ?></span>
                </nav>
                <h1 id="page-title" class="page-header__title"><?php echo esc_html( $album_title ); ?></h1>
                <p class="page-header__lead"><?php echo count( $images ); ?> foto</p>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <?php if ( empty( $images ) ) : ?>
                    <p class="notice">Albuma bildes šobrīd nevar ielādēt.</p>
                <?php else : ?>
                    <div class="album-grid" id="jn-album-grid">
                        <?php foreach ( $images as $i => $img ) : ?>
                            <a class="album-grid__item" href="<?php echo esc_url( $img['full'] ); ?>" data-index="<?php echo (int) $i; ?>" data-full="<?php echo esc_url( $img['full'] ); ?>" rel="noopener">
                                <img src="<?php echo esc_url( $img['thumb'] ); ?>" alt="" loading="lazy" referrerpolicy="no-referrer">
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="util-center">
                    <a class="btn btn--ghost" href="<?php echo esc_url( $index_url ); ?>">← Atpakaļ uz galeriju</a>
                </div>
            </div>
        </section>

        <div class="jn-lightbox" id="jn-lightbox" aria-hidden="true" role="dialog" aria-modal="true">
            <button class="jn-lightbox__close" type="button" aria-label="Aizvērt">×</button>
            <button class="jn-lightbox__nav jn-lightbox__nav--prev" type="button" aria-label="Iepriekšējais">‹</button>
            <button class="jn-lightbox__nav jn-lightbox__nav--next" type="button" aria-label="Nākamais">›</button>
            <figure class="jn-lightbox__figure"><img class="jn-lightbox__img" src="" alt=""></figure>
            <div class="jn-lightbox__counter" aria-live="polite"></div>
        </div>
        <script>
        (function () {
            var box = document.getElementById('jn-lightbox'), grid = document.getElementById('jn-album-grid');
            if (!box || !grid) { return; }
            var items = Array.prototype.slice.call(grid.querySelectorAll('.album-grid__item'));
            var img = box.querySelector('.jn-lightbox__img'), counter = box.querySelector('.jn-lightbox__counter');
            var btnPrev = box.querySelector('.jn-lightbox__nav--prev'), btnNext = box.querySelector('.jn-lightbox__nav--next'), btnClose = box.querySelector('.jn-lightbox__close');
            var urls = items.map(function (el) { return el.getAttribute('data-full'); }), current = 0;
            function render() { img.src = urls[current]; counter.textContent = (current + 1) + ' / ' + urls.length; btnPrev.disabled = current === 0; btnNext.disabled = current === urls.length - 1; }
            function open(index) { current = index; render(); box.classList.add('is-open'); box.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden'; btnClose.focus(); }
            function close() { box.classList.remove('is-open'); box.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; }
            function prev() { if (current > 0) { current--; render(); } }
            function next() { if (current < urls.length - 1) { current++; render(); } }
            items.forEach(function (el, i) { el.addEventListener('click', function (e) { e.preventDefault(); open(i); }); });
            btnPrev.addEventListener('click', function (e) { e.stopPropagation(); prev(); });
            btnNext.addEventListener('click', function (e) { e.stopPropagation(); next(); });
            btnClose.addEventListener('click', close);
            box.addEventListener('click', function (e) { if (e.target === box || e.target.classList.contains('jn-lightbox__figure')) { close(); } });
            document.addEventListener('keydown', function (e) { if (!box.classList.contains('is-open')) { return; } if (e.key === 'Escape') { close(); } if (e.key === 'ArrowLeft') { prev(); } if (e.key === 'ArrowRight') { next(); } });
        })();
        </script>
    </main>
    <?php get_footer(); return;
}


$albums = jn_load_planet_gallery();
$base = get_permalink();

get_header();
?>
<main id="main" class="site-main" tabindex="-1">
    <section class="page-header" aria-labelledby="page-title">
        <div class="container">
            <nav class="breadcrumb" aria-label="Atrašanās vieta">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a> <span aria-hidden="true">/</span>
                <a href="<?php echo esc_url( home_url( '/planetarijs/' ) ); ?>">Planetārijs</a> <span aria-hidden="true">/</span>
                <span aria-current="page">Galerija</span>
            </nav>
            <h1 id="page-title" class="page-header__title">Planetārija galerija</h1>
            <p class="page-header__lead">Foto no planetārija un observatorijas pasākumiem.</p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <?php if ( empty( $albums ) ) : ?>
                <p class="notice">Galerija šobrīd nevar ielādēt.</p>
            <?php else : ?>
                <div class="gallery-grid">
                    <?php foreach ( $albums as $album ) :
                        $internal = add_query_arg( 'albums', rawurlencode( $album['link'] ), $base );
                        ?>
                        <a class="gallery-card" href="<?php echo esc_url( $internal ); ?>">
                            <div class="gallery-card__media">
                                <img src="<?php echo esc_url( $album['thumb'] ); ?>" alt="<?php echo esc_attr( $album['title'] ); ?>" loading="lazy" referrerpolicy="no-referrer">
                                <span class="gallery-card__count"><?php echo esc_html( $album['count'] ); ?> foto</span>
                            </div>
                            <div class="gallery-card__body">
                                <h2 class="gallery-card__title"><?php echo esc_html( $album['title'] ); ?></h2>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php get_footer(); ?>