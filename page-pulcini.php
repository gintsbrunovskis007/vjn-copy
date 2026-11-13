<?php
/**
 * Template Name: Pulciņi
 * Description: Nodarbības — kompakta PDF josla + izmaiņu bloks + 4 kategorijas.
 */

// ---------------------------------------------------------------------------
// 1) Nodarbību izmaiņas (izmantojot DOMDocument, lai pareizi saliktu kopā)
// ---------------------------------------------------------------------------
if ( ! function_exists( 'jn_load_lesson_changes' ) ) {
    function jn_load_lesson_changes() {
        $cache = get_transient( 'jn_changes_pulcini_v2' );
        if ( is_array( $cache ) ) {
            return $cache;
        }

        $r = wp_remote_get(
            'https://jaunradesnams.lv/wp-json/wp/v2/pages/23901?_fields=modified,content',
            array( 'timeout' => 20, 'user-agent' => 'VJN/1.0' )
        );
        if ( is_wp_error( $r ) || 200 !== wp_remote_retrieve_response_code( $r ) ) {
            return array( 'modified' => '', 'items' => array() );
        }

        $data = json_decode( wp_remote_retrieve_body( $r ), true );
        if ( ! is_array( $data ) || empty( $data['content']['rendered'] ) ) {
            return array( 'modified' => '', 'items' => array() );
        }

        $html     = $data['content']['rendered'];
        $modified = $data['modified'] ?? '';
        $items    = array();

        if ( class_exists( 'DOMDocument' ) ) {
            $doc = new DOMDocument();
            libxml_use_internal_errors( true );
            $doc->loadHTML( '<?xml encoding="UTF-8"><div id="jn-changes-wrap">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
            libxml_clear_errors();
            
            $xpath = new DOMXPath( $doc );
            $top_uls = $xpath->query( '//div[@id="jn-changes-wrap"]/ul' );
            if ( $top_uls->length > 0 ) {
                $top_ul = $top_uls->item( 0 );
                foreach ( $top_ul->childNodes as $li ) {
                    if ( $li->nodeName !== 'li' ) { continue; }
                    
                    $prefix_html = '';
                    $teacher = '';
                    $course = '';
                    
                    $nested_ul = null;
                    foreach ( $li->childNodes as $child ) {
                        if ( $child->nodeName === 'ul' ) {
                            $nested_ul = $child;
                            break;
                        }
                        $prefix_html .= $doc->saveHTML( $child );
                    }
                    
                    $prefix_text = trim( html_entity_decode( wp_strip_all_tags( $prefix_html ), ENT_QUOTES, 'UTF-8' ) );
                    $prefix_text = preg_replace( '/\s+/u', ' ', $prefix_text );
                    
                    if ( $nested_ul ) {
                        foreach ( $nested_ul->childNodes as $t_li ) {
                            if ( $t_li->nodeName !== 'li' ) { continue; }
                            
                            $strongs = $t_li->getElementsByTagName( 'strong' );
                            if ( $strongs->length > 0 ) {
                                $teacher = trim( $strongs->item( 0 )->textContent );
                            }
                            
                            $course_uls = $t_li->getElementsByTagName( 'ul' );
                            if ( $course_uls->length > 0 ) {
                                foreach ( $course_uls->item( 0 )->childNodes as $c_li ) {
                                    if ( $c_li->nodeName === 'li' ) {
                                        $course = trim( $c_li->textContent );
                                    }
                                }
                            }
                            break;
                        }
                    }
                    
                    if ( $prefix_text || $teacher || $course ) {
                        $items[] = array(
                            'prefix'  => $prefix_text,
                            'teacher' => $teacher,
                            'course'  => $course,
                        );
                    }
                }
            }
        }

        $result = array( 'modified' => $modified, 'items' => $items );
        if ( ! empty( $items ) ) {
            set_transient( 'jn_changes_pulcini_v2', $result, 5 * MINUTE_IN_SECONDS );
        }
        return $result;
    }
}

// ---------------------------------------------------------------------------
// 2) PDF saraksta info
// ---------------------------------------------------------------------------
if ( ! function_exists( 'jn_schedule_pdf' ) ) {
    function jn_schedule_pdf() {
        $pdf_url = 'https://jaunradesnams.lv/wp-content/uploads/nodarbiibu-saraksts_2026_2027_3.11.2026_1.pdf';

        $date_str = '';
        $filename = basename( wp_parse_url( $pdf_url, PHP_URL_PATH ) );
        if ( preg_match( '/(\d{1,2}\.\d{1,2}\.\d{4})/', $filename, $dm ) ) {
            $date_str = $dm[1];
        }

        return array(
            'url'  => $pdf_url,
            'date' => $date_str,
        );
    }
}

// ---------------------------------------------------------------------------
// 3) Kategorijas un programmas
// ---------------------------------------------------------------------------
if ( ! function_exists( 'jn_hubs' ) ) {
    function jn_hubs() {
        return array(
            'kulturizglitibas-programmas'     => array( 'name' => 'Kultūrizglītība',    'sub' => 'Mūzika, deja, teātris, māksla',           'color' => '#b3123a' ),
            'stem'                            => array( 'name' => 'STEM',                'sub' => 'Inženierzinātnes, dabaszinātnes',         'color' => '#1a5fa8' ),
            'sports-un-fiziskas-aktivitates'  => array( 'name' => 'Sports',              'sub' => 'Galda spēles, dejas, fitnesa',            'color' => '#2b7a3d' ),
            'socialas-un-pilsoniskas-jomas'   => array( 'name' => 'Sociālās programmas', 'sub' => 'Valodas, kulturoloģija, uzņēmējdarbība',   'color' => '#8a4a00' ),
        );
    }
}

if ( ! function_exists( 'jn_load_hub' ) ) {
    function jn_load_hub( $hub_slug ) {
        $cache_key = 'jn_hub_data_' . $hub_slug . '_v4';
        $cached    = get_transient( $cache_key );
        if ( is_array( $cached ) ) { return $cached; }

        $url = 'https://jaunradesnams.lv/wp-json/wp/v2/pages?slug=' . rawurlencode( $hub_slug ) . '&_fields=content';
        $r   = wp_remote_get( $url, array( 'timeout' => 15, 'user-agent' => 'VJN/1.0' ) );
        if ( is_wp_error( $r ) || 200 !== wp_remote_retrieve_response_code( $r ) ) {
            return array( 'image' => '', 'categories' => array() );
        }
        $pages = json_decode( wp_remote_retrieve_body( $r ), true );
        if ( empty( $pages[0]['content']['rendered'] ) ) {
            return array( 'image' => '', 'categories' => array() );
        }
        $html = $pages[0]['content']['rendered'];

        $image = '';
        if ( preg_match_all( '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $imgs ) ) {
            foreach ( $imgs[1] as $src ) {
                if ( strpos( $src, 'teatris_mes_logo' ) !== false ) { continue; }
                if ( strpos( $src, 'Lego-Mindstorms' ) !== false )  { $image = $src; break; }
                if ( strpos( $src, 'hockey' ) !== false )           { $image = $src; break; }
                if ( strpos( $src, 'kulturizglitiba' ) !== false )  { $image = $src; break; }
                if ( strpos( $src, 'greenhands' ) !== false )       { $image = $src; break; }
                if ( ! $image ) { $image = $src; }
            }
        }

        $categories = array();
        if ( preg_match_all( '#https://jaunradesnams\.lv/jaunrades-nams/pulcini/([^/"\']+)/([^/"\']+)/#', $html, $m, PREG_SET_ORDER ) ) {
            foreach ( $m as $row ) {
                $cat_slug  = $row[1];
                $prog_slug = $row[2];
                if ( ! isset( $categories[ $cat_slug ] ) ) {
                    $categories[ $cat_slug ] = array( 'name' => ucfirst( str_replace( '-', ' ', $cat_slug ) ), 'programs' => array() );
                }
                $categories[ $cat_slug ]['programs'][ $prog_slug ] = $row[0];
            }
        }

        if ( preg_match_all( '#https://jaunradesnams\.lv/([^/"\']+)/#', $html, $m2, PREG_SET_ORDER ) ) {
            $skip = array( 'jaunrades-nams', 'stem', 'kulturizglitibas-programmas', 'sports-un-fiziskas-aktivitates', 'socialas-un-pilsoniskas-jomas', 'pulcini', 'planetarijs', 'dokumenti', 'par-mums', 'kontakti', 'galerija', 'ceni', 'schedule', 'ticket-prices', 'about-us', 'useful-information', 'o-nas', 'poljeznaja-informacia', 'pieklustamibas-pazinojums', 'izglitibas-iestadem', 'piedavajums-izglitibas-iestadem' );
            foreach ( $m2 as $row ) {
                $slug = $row[1];
                if ( in_array( $slug, $skip, true ) ) { continue; }
                if ( strpos( $row[0], '/jaunrades-nams/pulcini/' ) !== false ) { continue; }
                $cat_slug = 'citas';
                if ( ! isset( $categories[ $cat_slug ] ) ) {
                    $categories[ $cat_slug ] = array( 'name' => 'Citas programmas', 'programs' => array() );
                }
                $categories[ $cat_slug ]['programs'][ $slug ] = $row[0];
            }
        }

        $result = array( 'image' => $image, 'categories' => $categories );
        set_transient( $cache_key, $result, 15 * MINUTE_IN_SECONDS );
        return $result;
    }
}

if ( ! function_exists( 'jn_program_details' ) ) {
    function jn_program_details( $slugs ) {
        $slugs = array_values( array_unique( $slugs ) );
        if ( empty( $slugs ) ) { return array(); }

        $hash   = md5( implode( ',', $slugs ) );
        $key    = 'jn_prog_details_v2_' . $hash;
        $cached = get_transient( $key );
        if ( is_array( $cached ) ) { return $cached; }

        $out = array();
        foreach ( array_chunk( $slugs, 20 ) as $chunk ) {
            $url = add_query_arg( array(
                'slug'     => implode( ',', $chunk ),
                'per_page' => count( $chunk ),
                '_fields'  => 'slug,title,link,content',
            ), 'https://jaunradesnams.lv/wp-json/wp/v2/pages' );
            $r = wp_remote_get( $url, array( 'timeout' => 20, 'user-agent' => 'VJN/1.0' ) );
            if ( is_wp_error( $r ) || 200 !== wp_remote_retrieve_response_code( $r ) ) { continue; }
            $batch = json_decode( wp_remote_retrieve_body( $r ), true );
            if ( ! is_array( $batch ) ) { continue; }
            foreach ( $batch as $page ) {
                $out[ $page['slug'] ] = jn_parse_program( $page );
            }
        }

        set_transient( $key, $out, 15 * MINUTE_IN_SECONDS );
        return $out;
    }
}

if ( ! function_exists( 'jn_parse_program' ) ) {
    function jn_parse_program( $page ) {
        $html  = $page['content']['rendered'] ?? '';
        $link  = $page['link'] ?? '#';
        $slug  = $page['slug'] ?? '';
        $title = wp_strip_all_tags( $page['title']['rendered'] ?? '' );

        $image = ''; $alt = '';
        if ( preg_match( '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $m ) ) {
            $image = $m[1];
            if ( preg_match( '/alt=["\']([^"\']*)["\']/i', $m[0], $a ) ) { $alt = $a[1]; }
        }

        $text = preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES, 'UTF-8' ) );
        $age = ''; $teacher = '';
        if ( preg_match( '/(?:Dal[īi]bniek\w*|Vecums)\s*[:\-]?\s*([0-9][0-9\s\-\–\—\.līdz]{0,20}?gad[^\.,;]{0,12})/iu', $text, $m ) ) { $age = trim( $m[1] ); }
        if ( preg_match( '/Skolot[āa]j[as]?\s*[:\-]\s*([\p{L}][\p{L}\.\-\s]{1,60})/u', $text, $m ) ) { $teacher = trim( preg_replace( '/\s+(Dal|Skol|Vec).*$/u', '', $m[1] ) ); }

        $excerpt = '';
        if ( preg_match_all( '/<p[^>]*>(.*?)<\/p>/is', $html, $pm ) ) {
            foreach ( $pm[1] as $p ) {
                $clean = trim( html_entity_decode( wp_strip_all_tags( $p ), ENT_QUOTES, 'UTF-8' ) );
                if ( mb_strlen( $clean ) > 50 && false === stripos( $clean, 'Dalībniek' ) && false === stripos( $clean, 'Skolotāj' ) && false === stripos( $clean, 'Vecums' ) ) {
                    $excerpt = wp_trim_words( $clean, 24 );
                    break;
                }
            }
        }

        return array(
            'title'   => $title,
            'link'    => $link,
            'slug'    => $slug,
            'image'   => $image,
            'alt'     => $alt ?: $title,
            'age'     => $age,
            'teacher' => $teacher,
            'excerpt' => $excerpt,
        );
    }
}

if ( ! function_exists( 'jn_cat_url' ) ) {
    function jn_cat_url( $hub = '', $page = 1 ) {
        $base = get_permalink() ?: home_url( '/jaunrades-nams/pulcini/' );
        $args = array();
        if ( $hub )     { $args['kat'] = $hub; }
        if ( $page > 1 ){ $args['strana'] = (int) $page; }
        return empty( $args ) ? $base : add_query_arg( $args, $base );
    }
}

if ( ! function_exists( 'jn_pg_range' ) ) {
    function jn_pg_range( $cur, $tot, $w = 1 ) {
        $cur = max( 1, min( (int) $cur, (int) $tot ) );
        $k = array();
        for ( $i = 1; $i <= $tot; $i++ ) {
            if ( 1 === $i || $tot === $i || ( $i >= $cur - $w && $i <= $cur + $w ) ) { $k[] = $i; }
        }
        $out = array(); $prev = 0;
        foreach ( $k as $p ) {
            if ( $prev && $p - $prev > 1 ) { $out[] = -1; }
            $out[] = $p; $prev = $p;
        }
        return $out;
    }
}

// ---------------------------------------------------------------------------
// State
// ---------------------------------------------------------------------------
$hubs       = jn_hubs();
$kat        = isset( $_GET['kat'] ) ? sanitize_key( $_GET['kat'] ) : '';
$per_page   = 12;
$current    = isset( $_GET['strana'] ) ? max( 1, (int) $_GET['strana'] ) : 1;

$changes    = jn_load_lesson_changes();
$schedule   = jn_schedule_pdf();

$hub_data = array();
foreach ( array_keys( $hubs ) as $hub_slug ) {
    $hub_data[ $hub_slug ] = jn_load_hub( $hub_slug );
}

$catalog = array();
foreach ( $hub_data as $data ) {
    foreach ( $data['categories'] as $cat_slug => $cat ) {
        if ( ! isset( $catalog[ $cat_slug ] ) ) {
            $catalog[ $cat_slug ] = array( 'name' => $cat['name'], 'programs' => array() );
        }
        $catalog[ $cat_slug ]['programs'] = array_merge( $catalog[ $cat_slug ]['programs'], $cat['programs'] );
    }
}

if ( $kat && isset( $hub_data[ $kat ] ) ) {
    $all = array();
    foreach ( $hub_data[ $kat ]['categories'] as $cat ) {
        $all = array_merge( $all, $cat['programs'] );
    }
    $catalog[ $kat ] = array( 'name' => $hubs[ $kat ]['name'], 'programs' => $all );
}

$detail = $kat && isset( $catalog[ $kat ] );

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

<?php if ( ! $detail ) : ?>

    <section class="page-header" aria-labelledby="page-title">
        <div class="container">
            <nav class="breadcrumb" aria-label="Atrašanās vieta">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Nodarbības</span>
            </nav>
            <h1 id="page-title" class="page-header__title">Nodarbības</h1>
            <p class="page-header__lead">Izvēlies jomu, kas tev interesē.</p>
        </div>
    </section>

    <section class="section pulcini-top-section">
        <div class="container">

            <div class="pulcini-toolbar">
                <span class="pulcini-toolbar__label">Saraksts</span>
                <div class="pulcini-toolbar__text">
                    <strong>Nodarbības 2026./2027.</strong>
                    <?php if ( $schedule['date'] ) : ?>
                        <span class="pulcini-toolbar__date">atjaunots <?php echo esc_html( $schedule['date'] ); ?></span>
                    <?php endif; ?>
                </div>
                <a class="pulcini-toolbar__btn"
                   href="<?php echo esc_url( $schedule['url'] ); ?>"
                   target="_blank" rel="noopener">
                    Lejupielādēt PDF
                    <span aria-hidden="true">↓</span>
                </a>
            </div>

            <?php if ( ! empty( $changes['items'] ) ) : ?>
                <div class="pulcini-changes-note">
                    <div class="pulcini-changes-note__head">
                        <span class="pulcini-changes-note__badge">Izmaiņas</span>
                        <?php if ( $changes['modified'] ) : ?>
                            <span class="pulcini-changes-note__updated">
                                <?php echo esc_html( date_i18n( 'd.m.Y · H:i', strtotime( $changes['modified'] ) ) ); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <ul class="pulcini-changes-note__list">
                        <?php foreach ( $changes['items'] as $item ) : ?>
                            <li class="pulcini-changes-note__item">
                                <?php if ( $item['prefix'] ) : ?>
                                    <span class="pulcini-changes-note__change"><?php echo esc_html( $item['prefix'] ); ?></span>
                                <?php endif; ?>
                                <?php if ( $item['course'] || $item['teacher'] ) : ?>
                                    <span class="pulcini-changes-note__meta">
                                        <?php if ( $item['course'] ) : ?>
                                            <strong><?php echo esc_html( $item['course'] ); ?></strong>
                                        <?php endif; ?>
                                        <?php if ( $item['course'] && $item['teacher'] ) : ?><span aria-hidden="true"> · </span><?php endif; ?>
                                        <?php if ( $item['teacher'] ) : ?><?php echo esc_html( $item['teacher'] ); ?><?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="hub-grid">
                <?php foreach ( $hubs as $hub_slug => $hub_meta ) :
                    $img = $hub_data[ $hub_slug ]['image'] ?? '';
                    $prog_count = 0;
                    foreach ( $hub_data[ $hub_slug ]['categories'] as $cat ) { $prog_count += count( $cat['programs'] ); }
                    ?>
                    <a class="hub-card<?php echo $img ? '' : ' hub-card--no-image'; ?>"
                       href="<?php echo esc_url( jn_cat_url( $hub_slug ) ); ?>"
                       style="--hub-color: <?php echo esc_attr( $hub_meta['color'] ); ?>;">

                        <?php if ( $img ) : ?>
                            <div class="hub-card__media">
                                <img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy">
                            </div>
                        <?php endif; ?>

                        <div class="hub-card__content">
                            <p class="hub-card__count"><?php echo esc_html( $prog_count ); ?> programmas</p>
                            <h2 class="hub-card__title"><?php echo esc_html( $hub_meta['name'] ); ?></h2>
                            <p class="hub-card__sub"><?php echo esc_html( $hub_meta['sub'] ); ?></p>
                            <span class="hub-card__arrow" aria-hidden="true">→</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<?php else : ?>

    <?php
    $cat       = $catalog[ $kat ];
    $slug_map  = $cat['programs'];
    $details   = jn_program_details( array_keys( $slug_map ) );
    $items     = array_values( array_filter( $details ) );
    usort( $items, function ( $a, $b ) { return strcoll( $a['title'], $b['title'] ); } );

    $total    = count( $items );
    $pages    = max( 1, (int) ceil( $total / $per_page ) );
    $current  = min( $current, $pages );
    $slice    = array_slice( $items, ( $current - 1 ) * $per_page, $per_page );

    $hub_meta = $hubs[ $kat ] ?? array( 'name' => $cat['name'], 'sub' => '', 'color' => '#b3123a' );
    $hub_img  = $hub_data[ $kat ]['image'] ?? '';
    ?>

    <section class="page-header page-header--hub" aria-labelledby="page-title"
             <?php if ( $hub_img ) : ?>style="background-image: linear-gradient(rgba(92,8,28,0.72), rgba(92,8,28,0.85)), url('<?php echo esc_url( $hub_img ); ?>');"<?php endif; ?>>
        <div class="container">
            <nav class="breadcrumb breadcrumb--light" aria-label="Atrašanās vieta">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a>
                <span aria-hidden="true">/</span>
                <a href="<?php echo esc_url( jn_cat_url() ); ?>">Nodarbības</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page"><?php echo esc_html( $hub_meta['name'] ); ?></span>
            </nav>
            <h1 id="page-title" class="page-header__title page-header__title--light"><?php echo esc_html( $hub_meta['name'] ); ?></h1>
            <?php if ( ! empty( $hub_meta['sub'] ) ) : ?>
                <p class="page-header__lead page-header__lead--light"><?php echo esc_html( $hub_meta['sub'] ); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <section class="section">
        <div class="container">

            <?php if ( empty( $slice ) ) : ?>
                <p class="notice">Šajā kategorijā šobrīd nav pieejamu nodarbību.</p>
            <?php else : ?>

                <div class="jn-latest-posts">
                    <?php foreach ( $slice as $p ) :
                        $slug_val = isset( $p['slug'] ) ? $p['slug'] : '';
                        if ( ! $slug_val ) {
                            $path = wp_parse_url( $p['link'], PHP_URL_PATH );
                            $slug_val = $path ? basename( rtrim( $path, '/' ) ) : '';
                        }
                        $internal = $slug_val ? jn_single_url_program( $slug_val, $kat ) : $p['link'];
                        ?>
                        <article class="jn-post">
                            <?php if ( ! empty( $p['image'] ) ) : ?>
                                <div class="jn-post__thumb">
                                    <img src="<?php echo esc_url( $p['image'] ); ?>" alt="<?php echo esc_attr( $p['alt'] ); ?>" loading="lazy">
                                </div>
                            <?php endif; ?>
                            <div class="jn-post__body">
                                <?php if ( ! empty( $p['age'] ) || ! empty( $p['teacher'] ) ) : ?>
                                    <p class="jn-post__date"><?php echo esc_html( implode( ' · ', array_filter( array( $p['age'], $p['teacher'] ) ) ) ); ?></p>
                                <?php endif; ?>
                                <h2 class="jn-post__title">
                                    <a href="<?php echo esc_url( $internal ); ?>"><?php echo esc_html( $p['title'] ); ?></a>
                                </h2>
                                <?php if ( ! empty( $p['excerpt'] ) ) : ?>
                                    <p class="jn-post__excerpt"><?php echo esc_html( $p['excerpt'] ); ?></p>
                                <?php endif; ?>
                                <span class="jn-post__more" aria-hidden="true">Uzzināt vairāk</span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ( $pages > 1 ) : $range = jn_pg_range( $current, $pages ); ?>
                    <nav class="pagination" aria-label="Lapu navigācija">
                        <?php if ( $current > 1 ) : ?>
                            <a class="pagination__arrow" href="<?php echo esc_url( jn_cat_url( $kat, $current - 1 ) ); ?>" rel="prev"><span aria-hidden="true">←</span></a>
                        <?php else : ?>
                            <span class="pagination__arrow is-disabled" aria-hidden="true">←</span>
                        <?php endif; ?>

                        <?php foreach ( $range as $pg ) : ?>
                            <?php if ( -1 === $pg ) : ?>
                                <span class="pagination__gap" aria-hidden="true">…</span>
                            <?php elseif ( $pg === $current ) : ?>
                                <span class="pagination__num is-current" aria-current="page"><?php echo esc_html( $pg ); ?></span>
                            <?php else : ?>
                                <a class="pagination__num" href="<?php echo esc_url( jn_cat_url( $kat, $pg ) ); ?>"><?php echo esc_html( $pg ); ?></a>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if ( $current < $pages ) : ?>
                            <a class="pagination__arrow" href="<?php echo esc_url( jn_cat_url( $kat, $current + 1 ) ); ?>" rel="next"><span aria-hidden="true">→</span></a>
                        <?php else : ?>
                            <span class="pagination__arrow is-disabled" aria-hidden="true">→</span>
                        <?php endif; ?>
                    </nav>
                    <p class="pagination__meta">Lapa <?php echo esc_html( $current ); ?> no <?php echo esc_html( $pages ); ?></p>
                <?php endif; ?>

            <?php endif; ?>

            <div style="text-align:center; margin-top:48px;">
                <a class="btn btn--ghost" href="<?php echo esc_url( jn_cat_url() ); ?>">← Atpakaļ uz kategorijām</a>
            </div>

        </div>
    </section>

<?php endif; ?>

</main>

<?php get_footer(); ?>