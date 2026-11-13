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

// Check if we are on any Planetarium page
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
$is_planetarium = ( strpos( $request_uri, '/planetarijs/' ) !== false || is_page( 'planetarijs' ) || !empty( $GLOBALS['is_planetarium_post'] ) );

$site_logo = $is_planetarium 
    ? 'https://jaunradesnams.lv/wp-content/uploads/2016/06/op-logo-home.jpg' 
    : get_template_directory_uri() . '/assets/logo.png';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="<?php echo $is_planetarium ? '#0b132b' : '#f28a2e'; ?>">
    <script>document.documentElement.classList.add('js');</script>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main">Pāriet uz saturu</a>

<header class="site-header">
    <div class="header-container">

        <div class="site-logo">
            <a href="<?php echo esc_url( $is_planetarium ? home_url( '/planetarijs/' ) : home_url( '/' ) ); ?>">
                <img src="<?php echo esc_url( $site_logo ); ?>" alt="Logo">
            </a>
        </div>

        <nav class="main-nav" id="main-navigation" aria-label="Galvenā navigācija">
            <?php if ( $is_planetarium ) : ?>
                <!-- Planetarium Menu -->
                <a href="<?php echo esc_url( home_url( '/planetarijs/' ) ); ?>" <?php echo is_page('planetarijs') ? 'class="is-current"' : ''; ?>>Sākums</a>
                <a href="<?php echo esc_url( home_url( '/planetarijs/aktualitates/' ) ); ?>" <?php echo (strpos($request_uri, '/planetarijs/aktualitates/') !== false) ? 'class="is-current"' : ''; ?>>Aktualitātes</a>
                <a href="<?php echo esc_url( home_url( '/planetarijs/repertuars/' ) ); ?>" <?php echo (strpos($request_uri, '/planetarijs/repertuars/') !== false) ? 'class="is-current"' : ''; ?>>Repertuārs</a>
                <a href="<?php echo esc_url( home_url( '/planetarijs/seansu-laiki/' ) ); ?>" <?php echo (strpos($request_uri, '/planetarijs/seansu-laiki/') !== false) ? 'class="is-current"' : ''; ?>>Seansu laiki</a>
                <a href="<?php echo esc_url( home_url( '/planetarijs/galerija/' ) ); ?>" <?php echo (strpos($request_uri, '/planetarijs/galerija/') !== false) ? 'class="is-current"' : ''; ?>>Galerija</a>
                <a href="<?php echo esc_url( home_url( '/planetarijs/cenas/' ) ); ?>" <?php echo (strpos($request_uri, '/planetarijs/cenas/') !== false) ? 'class="is-current"' : ''; ?>>Cenas</a>
                <a href="<?php echo esc_url( home_url( '/planetarijs/darbibas-apraksts/' ) ); ?>" <?php echo (strpos($request_uri, '/planetarijs/darbibas-apraksts/') !== false) ? 'class="is-current"' : ''; ?>>Par mums</a>
                <a href="<?php echo esc_url( home_url( '/planetarijs/noteikumi/' ) ); ?>" <?php echo (strpos($request_uri, '/planetarijs/noteikumi/') !== false) ? 'class="is-current"' : ''; ?>>Noteikumi</a>
                
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-contact" style="background: #4cc9f0; color: #0b132b;">Uz Jaunrades namu</a>
            <?php else : ?>
                <!-- Jaunrades Nams Menu -->
                <?php foreach ( jn_nav_items() as $item ) :
                    $is_current = is_page( $item['slug'] );
                    $classes    = trim( ( $item['class'] ?? '' ) . ( $is_current ? ' is-current' : '' ) );
                    ?>
                    <a href="<?php echo esc_url( home_url( $item['path'] ) ); ?>"
                       <?php echo $classes ? 'class="' . esc_attr( $classes ) . '"' : ''; ?>
                       <?php echo $is_current ? 'aria-current="page"' : ''; ?>>
                        <?php echo esc_html( $item['label'] ); ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </nav>

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
</header>