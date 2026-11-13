<?php
/**
 * Template Name: Galerija
 */

if ( ! function_exists( 'jn_parse_gallery_html' ) ) {
    function jn_parse_gallery_html( string $html ): array {
        if ( '' === $html || strpos( $html, 'ngg-album-compact' ) === false ) {
            return array();
        }
        $albums = array();
        if ( ! preg_match_all(
            '#<div\s+class="[^"]*ngg-album-compact[^"]*"[^>]*>(.*?)<h4[^>]*>(.*?)</h4>.*?<strong>(\d+)</strong>#is',
            $html, $matches, PREG_SET_ORDER
        ) ) {
            return $albums;
        }

        foreach ( $matches as $m ) {
            $block = $m[1];
            $title = trim( wp_strip_all_tags( $m[2] ) );
            $count = (int) $m[3];

            $link = ''; $thumb = '';
            if ( preg_match( '#<a[^>]+href=[\'"]([^\'"]+)[\'"]#i', $block, $lm ) ) {
                $link = esc_url_raw( html_entity_decode( $lm[1], ENT_QUOTES, 'UTF-8' ) );
            }
            if ( preg_match( '#<img[^>]+(?:data-src|src)=[\'"]([^\'"]+)[\'"]#i', $block, $im ) ) {
                $thumb = esc_url_raw( html_entity_decode( $im[1], ENT_QUOTES, 'UTF-8' ) );
            }
            if ( ! $link || ! $title || ! $thumb ) continue;

            $albums[] = array(
                'title' => $title,
                'link'  => $link,
                'thumb' => $thumb,
                'count' => $count,
            );
        }
        return $albums;
    }
}

if ( ! function_exists( 'jn_load_albums' ) ) {
    function jn_load_albums(): array {
        $cache = get_transient( 'jn_albums_v8' );
        if ( is_array( $cache ) && ! empty( $cache ) ) return $cache;

        $attempts = array(
            'https://jaunradesnams.lv/wp-json/wp/v2/pages/15?_fields=slug,link,content',
            'https://jaunradesnams.lv/wp-json/wp/v2/pages?slug=galerija&per_page=10&_fields=slug,link,content',
        );

        $albums = array();
        foreach ( $attempts as $url ) {
            $r = wp_remote_get( $url, array( 'timeout' => 15, 'user-agent' => 'VJN/1.0' ) );
            if ( is_wp_error( $r ) || 200 !== wp_remote_retrieve_response_code( $r ) ) continue;

            $data = json_decode( wp_remote_retrieve_body( $r ), true );
            if ( ! is_array( $data ) ) continue;

            $pages = isset( $data['slug'] ) ? array( $data ) : $data;
            foreach ( $pages as $page ) {
                $link = $page['link'] ?? '';
                if ( strpos( $link, '/planetarijs/' ) !== false ) continue;
                if ( strpos( $link, '/galerija' ) === false ) continue;
                $found = jn_parse_gallery_html( $page['content']['rendered'] ?? '' );
                if ( ! empty( $found ) ) { $albums = $found; break 2; }
            }
        }

        if ( empty( $albums ) ) {
            $r = wp_remote_get( 'https://jaunradesnams.lv/jaunrades-nams/galerija/', array(
                'timeout' => 15, 'user-agent' => 'Mozilla/5.0 (compatible; VJN/1.0)',
            ) );
            if ( ! is_wp_error( $r ) && 200 === wp_remote_retrieve_response_code( $r ) ) {
                $albums = jn_parse_gallery_html( wp_remote_retrieve_body( $r ) );
            }
        }

        if ( ! empty( $albums ) ) {
            set_transient( 'jn_albums_v8', $albums, 15 * MINUTE_IN_SECONDS );
        }
        return $albums;
    }
}




$album_param = isset( $_GET['albums'] ) ? wp_unslash( $_GET['albums'] ) : '';
$album_url   = $album_param ? urldecode( $album_param ) : '';
$is_detail   = false;
$album_title = 'Albums';

if ( $album_url ) {
    $parsed = wp_parse_url( $album_url );
    if ( $parsed
         && ! empty( $parsed['host'] )
         && strtolower( $parsed['host'] ) === 'jaunradesnams.lv'
         && ! empty( $parsed['path'] )
         && strpos( $parsed['path'], '/galerija/' ) !== false ) {
        $is_detail = true;
    }
}

if ( $is_detail ) {
    $cache_key = 'jn_album_images_v11_' . md5( $album_url );
    $images    = get_transient( $cache_key );

    if ( ! is_array( $images ) || empty( $images ) ) {
        $images = array();
        $r = wp_remote_get( $album_url, array(
            'timeout' => 25, 'user-agent' => 'Mozilla/5.0 (compatible; VJN/1.0)',
        ) );

        if ( ! is_wp_error( $r ) && 200 === wp_remote_retrieve_response_code( $r ) ) {
            $html = wp_remote_retrieve_body( $r );



            if ( preg_match_all(
                '#<a\s[^>]*href=["\']([^"\']+\.(?:jpe?g|png|gif|webp))["\'][^>]*>#i',
                $html, $anchors, PREG_OFFSET_CAPTURE
            ) ) {
                $seen  = array();
                $total = count( $anchors[0] );

                for ( $i = 0; $i < $total; $i++ ) {
                    $a_html = $anchors[0][ $i ][0];
                    $a_pos  = $anchors[0][ $i ][1];
                    $full   = esc_url_raw( html_entity_decode( $anchors[1][ $i ][0], ENT_QUOTES, 'UTF-8' ) );

                    if ( ! $full || strpos( $full, '/gallery/' ) === false ) continue;
                    if ( isset( $seen[ $full ] ) ) continue;
                    $seen[ $full ] = true;

                    $window_end = $total > $i + 1 ? $anchors[0][ $i + 1 ][1] : strlen( $html );
                    $window     = substr( $html, $a_pos + strlen( $a_html ), $window_end - ( $a_pos + strlen( $a_html ) ) );

                    if ( ! preg_match( '#<img[^>]+(?:data-src|src)=["\']([^"\']+)["\']#i', $window, $im ) ) continue;
                    $thumb = esc_url_raw( html_entity_decode( $im[1], ENT_QUOTES, 'UTF-8' ) );
                    if ( ! $thumb ) continue;

                    $mid = preg_replace( '#-(\d+)x(\d+)x(\d+)-#', '-640x480x100-', $thumb );

                    $images[] = array(
                        'thumb' => $mid ?: $thumb,
                        'full'  => $full,
                    );
                }
            }
        }

        if ( ! empty( $images ) ) {
            set_transient( $cache_key, $images, 15 * MINUTE_IN_SECONDS );
        }
    }

    $index = get_transient( 'jn_albums_v8' );
    if ( is_array( $index ) ) {
        foreach ( $index as $a ) {
            if ( $a['link'] === $album_url ) { $album_title = $a['title']; break; }
        }
    }
    if ( 'Albums' === $album_title ) {
        $slug        = basename( rtrim( (string) wp_parse_url( $album_url, PHP_URL_PATH ), '/' ) );
        $album_title = ucwords( str_replace( array( '-', '_' ), ' ', urldecode( $slug ) ) );
    }

    $index_url = get_permalink() ?: home_url( '/jaunrades-nams/galerija/' );

    get_header();
    ?>
    <main id="main" class="site-main" tabindex="-1">
        <section class="page-header" aria-labelledby="page-title">
            <div class="container">
                <nav class="breadcrumb" aria-label="Atrašanās vieta">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a>
                    <span aria-hidden="true">/</span>
                    <a href="<?php echo esc_url( $index_url ); ?>">Galerija</a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page"><?php echo esc_html( wp_trim_words( $album_title, 6, '…' ) ); ?></span>
                </nav>
                <h1 id="page-title" class="page-header__title"><?php echo esc_html( $album_title ); ?></h1>
                <?php if ( ! empty( $images ) ) : ?>
                    <p class="page-header__lead"><?php echo count( $images ); ?> foto</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <?php if ( empty( $images ) ) : ?>
                    <p class="notice">Albuma bildes šobrīd nevar ielādēt. Lūdzu, mēģiniet vēlāk.</p>
                <?php else : ?>
                    <div class="album-grid" id="jn-album-grid">
                        <?php foreach ( $images as $i => $img ) : ?>
                            <a class="album-grid__item"
                               href="<?php echo esc_url( $img['full'] ); ?>"
                               data-index="<?php echo (int) $i; ?>"
                               data-full="<?php echo esc_url( $img['full'] ); ?>"
                               aria-label="Atvērt foto <?php echo (int) ( $i + 1 ); ?>"
                               rel="noopener">
                                <img src="<?php echo esc_url( $img['thumb'] ); ?>"
                                     alt="" loading="lazy" referrerpolicy="no-referrer">
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="util-center">
                    <a class="btn btn--ghost" href="<?php echo esc_url( $index_url ); ?>">← Atpakaļ uz galeriju</a>
                </div>
            </div>
        </section>

        <div class="jn-lightbox" id="jn-lightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Galerijas slaidrāde">
            <button class="jn-lightbox__close" type="button" aria-label="Aizvērt">×</button>
            <button class="jn-lightbox__nav jn-lightbox__nav--prev" type="button" aria-label="Iepriekšējais">‹</button>
            <button class="jn-lightbox__nav jn-lightbox__nav--next" type="button" aria-label="Nākamais">›</button>
            <figure class="jn-lightbox__figure">
                <img class="jn-lightbox__img" src="" alt="">
            </figure>
            <div class="jn-lightbox__counter" aria-live="polite"></div>
        </div>

        <script>
        (function () {
            var box     = document.getElementById('jn-lightbox');
            var grid    = document.getElementById('jn-album-grid');
            if (!box || !grid) return;

            var items    = Array.prototype.slice.call(grid.querySelectorAll('.album-grid__item'));
            var img      = box.querySelector('.jn-lightbox__img');
            var counter  = box.querySelector('.jn-lightbox__counter');
            var btnPrev  = box.querySelector('.jn-lightbox__nav--prev');
            var btnNext  = box.querySelector('.jn-lightbox__nav--next');
            var btnClose = box.querySelector('.jn-lightbox__close');

            var urls = items.map(function (el) { return el.getAttribute('data-full'); });
            var current = 0;
            var lastFocused = null;

            function render() {
                img.src = urls[current];
                counter.textContent = (current + 1) + ' / ' + urls.length;
                btnPrev.disabled = current === 0;
                btnNext.disabled = current === urls.length - 1;
            }

            function trapFocus(e) {
                if (e.key !== 'Tab') return;
                var focusable = box.querySelectorAll('button');
                var first = focusable[0], last = focusable[focusable.length - 1];
                if (e.shiftKey && document.activeElement === first) { last.focus(); e.preventDefault(); }
                else if (!e.shiftKey && document.activeElement === last) { first.focus(); e.preventDefault(); }
            }

            function open(index) {
                current = index; render();
                lastFocused = document.activeElement;
                box.classList.add('is-open');
                box.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                btnClose.focus();
                box.addEventListener('keydown', trapFocus);
            }

            function close() {
                box.classList.remove('is-open');
                box.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                box.removeEventListener('keydown', trapFocus);
                if (lastFocused) lastFocused.focus();
            }

            function prev() { if (current > 0) { current--; render(); } }
            function next() { if (current < urls.length - 1) { current++; render(); } }

            items.forEach(function (el, i) {
                el.addEventListener('click', function (e) { e.preventDefault(); open(i); });
            });

            btnPrev.addEventListener('click', function (e) { e.stopPropagation(); prev(); });
            btnNext.addEventListener('click', function (e) { e.stopPropagation(); next(); });
            btnClose.addEventListener('click', close);

            box.addEventListener('click', function (e) {
                if (e.target === box || e.target.classList.contains('jn-lightbox__figure')) close();
            });

            document.addEventListener('keydown', function (e) {
                if (!box.classList.contains('is-open')) return;
                if (e.key === 'Escape')     close();
                if (e.key === 'ArrowLeft')  prev();
                if (e.key === 'ArrowRight') next();
            });
        })();
        </script>
    </main>
    <?php
    get_footer();
    return;
}




$albums   = jn_load_albums();
$per_page = 24;
$current  = isset( $_GET['strana'] ) ? max( 1, (int) $_GET['strana'] ) : 1;
$total    = count( $albums );
$pages    = max( 1, (int) ceil( $total / $per_page ) );
$current  = min( $current, $pages );
$slice    = array_slice( $albums, ( $current - 1 ) * $per_page, $per_page );
$base     = get_permalink() ?: home_url( '/jaunrades-nams/galerija/' );

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

    <section class="page-header" aria-labelledby="page-title">
        <div class="container">
            <nav class="breadcrumb" aria-label="Atrašanās vieta">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Galerija</span>
            </nav>
            <h1 id="page-title" class="page-header__title">Galerija</h1>
            <p class="page-header__lead">Pasākumu, nodarbību un koncertu foto mirkļi.</p>
        </div>
    </section>

    <section class="section">
        <div class="container">

            <?php if ( empty( $albums ) ) : ?>
                <p class="notice">Galerija šobrīd nevar ielādēt.</p>
            <?php else : ?>

                <div class="gallery-grid">
                    <?php foreach ( $slice as $album ) :
                        $internal = add_query_arg( 'albums', rawurlencode( $album['link'] ), $base );
                        ?>
                        <a class="gallery-card" href="<?php echo esc_url( $internal ); ?>">
                            <div class="gallery-card__media">
                                <img src="<?php echo esc_url( $album['thumb'] ); ?>"
                                     alt="<?php echo esc_attr( $album['title'] ); ?>"
                                     loading="lazy" referrerpolicy="no-referrer">
                                <span class="gallery-card__count"><?php echo esc_html( $album['count'] ); ?> foto</span>
                            </div>
                            <div class="gallery-card__body">
                                <h2 class="gallery-card__title"><?php echo esc_html( $album['title'] ); ?></h2>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php if ( $pages > 1 ) :
                    $range = jn_pagination_range( $current, $pages );
                    ?>
                    <nav class="pagination" aria-label="Lapu navigācija">

                        <?php if ( $current > 1 ) : ?>
                            <a class="pagination__arrow" href="<?php echo esc_url( jn_page_url( $base, $current - 1 ) ); ?>" rel="prev"><span aria-hidden="true">←</span></a>
                        <?php else : ?>
                            <span class="pagination__arrow is-disabled" aria-hidden="true">←</span>
                        <?php endif; ?>

                        <?php foreach ( $range as $pg ) : ?>
                            <?php if ( -1 === $pg ) : ?>
                                <span class="pagination__gap" aria-hidden="true">…</span>
                            <?php elseif ( $pg === $current ) : ?>
                                <span class="pagination__num is-current" aria-current="page"><?php echo esc_html( $pg ); ?></span>
                            <?php else : ?>
                                <a class="pagination__num" href="<?php echo esc_url( jn_page_url( $base, $pg ) ); ?>"><?php echo esc_html( $pg ); ?></a>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if ( $current < $pages ) : ?>
                            <a class="pagination__arrow" href="<?php echo esc_url( jn_page_url( $base, $current + 1 ) ); ?>" rel="next"><span aria-hidden="true">→</span></a>
                        <?php else : ?>
                            <span class="pagination__arrow is-disabled" aria-hidden="true">→</span>
                        <?php endif; ?>

                    </nav>
                    <p class="pagination__meta">Lapa <?php echo esc_html( $current ); ?> no <?php echo esc_html( $pages ); ?></p>
                <?php endif; ?>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php get_footer(); ?>