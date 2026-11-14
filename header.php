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
