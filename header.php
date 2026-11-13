<?php
if ( ! function_exists( 'jn_nav_items' ) ) {
    /** Main menu items, shared by the header and the footer. */
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
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f28a2e">
    <script>document.documentElement.classList.add('js');</script>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main">Pāriet uz saturu</a>

<header class="site-header">
    <div class="header-container">

        <div class="site-logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <img
                    src="<?php echo esc_url( get_template_directory_uri() . '/assets/logo.png' ); ?>"
                    alt="Ventspils Jaunrades nams – uz sākumlapu"
                >
            </a>
        </div>

        <nav class="main-nav" id="main-navigation" aria-label="Galvenā navigācija">
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