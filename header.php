<?php
if ( ! function_exists( 'jn_nav_items' ) ) {
    function jn_nav_items() {
        return array(
            array( 'slug' => 'aktualitates', 'path' => '/jaunrades-nams/aktualitates/', 'label' => 'Aktualitātes' ),
            array( 'slug' => 'pulcini',      'path' => '/jaunrades-nams/pulcini/',      'label' => 'Nodarbības' ),
            array( 'slug' => 'galerija',     'path' => '/jaunrades-nams/galerija/',     'label' => 'Galerija' ),
            array( 'slug' => 'dokumenti',    'path' => '/jaunrades-nams/dokumenti/',    'label' => 'Resursi' ),
            array( 'slug' => 'par-mums',     'path' => '/jaunrades-nams/par-mums/',     'label' => 'Par mums' ),
            array( 'slug' => 'kontakti',     'path' => '/jaunrades-nams/kontakti/',     'label' => 'Kontakti', 'class' => 'nav-contact' ),
        );
    }
}

if ( ! function_exists( 'jn_planetarium_nav_items' ) ) {
    function jn_planetarium_nav_items() {
        return array(
            array( 'slug' => 'planetarijs',            'path' => '/planetarijs/',                      'label' => 'Sākums' ),
            array( 'slug' => 'aktualitates',           'path' => '/planetarijs/aktualitates/',         'label' => 'Aktualitātes' ),
            array( 'slug' => 'repertuars',             'path' => '/planetarijs/repertuars/',           'label' => 'Repertuārs' ),
            array( 'slug' => 'seansu-laiki',           'path' => '/planetarijs/seansu-laiki/',         'label' => 'Seansu laiki' ),
            array( 'slug' => 'galerija',               'path' => '/planetarijs/galerija/',             'label' => 'Galerija' ),
            array( 'slug' => 'cenas',                  'path' => '/planetarijs/cenas/',                'label' => 'Cenas' ),
            array( 'slug' => 'darbibas-apraksts',      'path' => '/planetarijs/darbibas-apraksts/',    'label' => 'Par mums' ),
            array( 'slug' => 'noteikumi',              'path' => '/planetarijs/noteikumi/',            'label' => 'Noteikumi' ),
        );
    }
}

$request_uri     = $_SERVER['REQUEST_URI'] ?? '';
$is_planetarium  = ( strpos( $request_uri, '/planetarijs/' ) !== false || is_page( 'planetarijs' ) || !empty( $GLOBALS['is_planetarium_post'] ) );

$home_link    = $is_planetarium ? home_url( '/planetarijs/' ) : home_url( '/' );
$site_logo    = $is_planetarium
    ? get_template_directory_uri() . '/assets/planetarijs-logo.png'
    : get_template_directory_uri() . '/assets/logo.png';
$site_name    = $is_planetarium ? 'Ventspils Planetārijs' : 'Ventspils Jaunrades nams';
$switch_url   = $is_planetarium ? home_url( '/' ) : home_url( '/planetarijs/' );
$switch_label = $is_planetarium ? 'Jaunrades nams' : 'Planetārijs';
$nav_items    = $is_planetarium ? jn_planetarium_nav_items() : jn_nav_items();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="<?php echo $is_planetarium ? '#071426' : '#e8711f'; ?>">
    <script>document.documentElement.classList.add('js');</script>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main">Pāriet uz saturu</a>

<header class="site-header">
    <div class="header-container">

        <a class="brand" href="<?php echo esc_url( $home_link ); ?>" aria-label="<?php echo esc_attr( $site_name ); ?>">
            <img class="brand__logo" src="<?php echo esc_url( $site_logo ); ?>" alt="<?php echo esc_attr( $site_name ); ?>">
        </a>

        <nav class="main-nav" id="main-navigation" aria-label="Galvenā navigācija">
            <?php foreach ( $nav_items as $item ) :
                if ( $is_planetarium ) {
                    $is_current = ( 'planetarijs' === $item['slug'] )
                        ? is_page( 'planetarijs' )
                        : ( false !== strpos( $request_uri, $item['path'] ) );
                } else {
                    $is_current = is_page( $item['slug'] );
                }
                $classes = trim( ( $item['class'] ?? '' ) . ( $is_current ? ' is-current' : '' ) );
                ?>
                <a href="<?php echo esc_url( home_url( $item['path'] ) ); ?>"
                   <?php echo $classes ? 'class="' . esc_attr( $classes ) . '"' : ''; ?>
                   <?php echo $is_current ? 'aria-current="page"' : ''; ?>>
                    <?php echo esc_html( $item['label'] ); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="header-tools">
            <a class="theme-switch" href="<?php echo esc_url( $switch_url ); ?>">
                <?php if ( $is_planetarium ) : ?>
                    <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.66 6.34l1.41-1.41"></path>
                    </svg>
                <?php else : ?>
                    <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M3 12h18"></path>
                        <path d="M12 3a15 15 0 0 1 0 18a15 15 0 0 1 0-18z"></path>
                    </svg>
                <?php endif; ?>
                <span class="theme-switch__label"><?php echo esc_html( $switch_label ); ?></span>
            </a>

            <button class="menu-toggle"
                    type="button"
                    aria-label="Atvērt navigāciju"
                    aria-expanded="false"
                    aria-controls="main-navigation">
                <span class="menu-toggle__bars" aria-hidden="true">
                    <span></span><span></span><span></span>
                </span>
            </button>
        </div>

    </div>
</header>
