<?php
$is_planetarium = jn_is_planetarium();

$home_link    = $is_planetarium ? home_url( '/planetarijs/' ) : home_url( '/' );
$site_logo    = $is_planetarium ? jn_asset( 'img/planetarijs-logo.png' ) : jn_asset( 'logo.png' );
$logo_width   = $is_planetarium ? 320 : 207;
$logo_height  = $is_planetarium ? 187 : 121;
$site_name    = $is_planetarium ? 'Ventspils Planetārijs' : 'Ventspils Jaunrades nams';
$switch_url   = jn_switch_url();
$switch_label = $is_planetarium ? 'Jaunrades nams' : 'Planetārijs';
$nav_items    = $is_planetarium ? jn_planetarium_nav_items() : jn_nav_items();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="<?php echo $is_planetarium ? '#071426' : '#e8711f'; ?>">
	<script>document.documentElement.classList.remove('no-js');document.documentElement.classList.add('js');</script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main">Pāriet uz saturu</a>

<header class="site-header">
	<div class="header-container">

		<a class="brand" href="<?php echo esc_url( $home_link ); ?>" aria-label="<?php echo esc_attr( $site_name ); ?>">
			<img class="brand__logo"
				src="<?php echo esc_url( $site_logo ); ?>"
				alt="<?php echo esc_attr( $site_name ); ?>"
				width="<?php echo (int) $logo_width; ?>"
				height="<?php echo (int) $logo_height; ?>"
				decoding="async">
		</a>

		<nav class="main-nav" id="main-navigation" aria-label="Galvenā navigācija">
			<?php foreach ( $nav_items as $item ) :
				$is_current = jn_nav_is_current( $item );
				$classes    = trim( ( $item['class'] ?? '' ) . ( $is_current ? ' is-current' : '' ) );
				?>
				<a href="<?php echo esc_url( home_url( $item['path'] ) ); ?>"
					<?php echo $classes ? 'class="' . esc_attr( $classes ) . '"' : ''; ?>
					<?php echo $is_current ? 'aria-current="page"' : ''; ?>>
					<?php echo esc_html( $item['label'] ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<div class="header-tools">
			<a class="theme-switch" href="<?php echo esc_url( $switch_url ); ?>" aria-label="<?php echo esc_attr( $switch_label ); ?>">
				<?php if ( $is_planetarium ) : ?>
					<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<circle cx="13.5" cy="6.5" r=".5" fill="currentColor"></circle>
						<circle cx="17.5" cy="10.5" r=".5" fill="currentColor"></circle>
						<circle cx="8.5" cy="7.5" r=".5" fill="currentColor"></circle>
						<circle cx="6.5" cy="12.5" r=".5" fill="currentColor"></circle>
						<path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"></path>
					</svg>
				<?php else : ?>
					<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"></path>
						<path d="M12 15l-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"></path>
						<path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"></path>
						<path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"></path>
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
