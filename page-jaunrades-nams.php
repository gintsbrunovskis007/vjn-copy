<?php
get_header();
?>

<main id="main" class="site-main" tabindex="-1">
	<section class="page-header" aria-labelledby="page-title">
		<div class="container">
			<?php
			jn_breadcrumb_render(
				array(
					array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
					array( 'name' => 'Jaunrades nams', 'url' => '' ),
				)
			);
			?>
			<h1 id="page-title" class="page-header__title">Ventspils Jaunrades nams</h1>
			<p class="page-header__lead">Vieta, kur bērni un jaunieši atklāj sevi kopš 1955. gada.</p>
		</div>
	</section>

	<section class="section">
		<div class="container">
			<div class="hub-grid">
				<a class="hub-card hub-card--no-image" href="<?php echo esc_url( home_url( '/jaunrades-nams/pulcini/' ) ); ?>">
					<div class="hub-card__content">
						<h2 class="hub-card__title">Nodarbības</h2>
						<p class="hub-card__sub">Pulciņi un programmas bērniem un jauniešiem</p>
						<span class="hub-card__arrow" aria-hidden="true">→</span>
					</div>
				</a>
				<a class="hub-card hub-card--no-image" href="<?php echo esc_url( home_url( '/jaunrades-nams/aktualitates/' ) ); ?>">
					<div class="hub-card__content">
						<h2 class="hub-card__title">Aktualitātes</h2>
						<p class="hub-card__sub">Jaunākie notikumi un paziņojumi</p>
						<span class="hub-card__arrow" aria-hidden="true">→</span>
					</div>
				</a>
				<a class="hub-card hub-card--no-image" href="<?php echo esc_url( home_url( '/jaunrades-nams/galerija/' ) ); ?>">
					<div class="hub-card__content">
						<h2 class="hub-card__title">Galerija</h2>
						<p class="hub-card__sub">Pasākumu, nodarbību un koncertu foto</p>
						<span class="hub-card__arrow" aria-hidden="true">→</span>
					</div>
				</a>
				<a class="hub-card hub-card--no-image" href="<?php echo esc_url( home_url( '/jaunrades-nams/dokumenti/' ) ); ?>">
					<div class="hub-card__content">
						<h2 class="hub-card__title">Dokumenti</h2>
						<p class="hub-card__sub">Noteikumi, nolikumi un veidlapas</p>
						<span class="hub-card__arrow" aria-hidden="true">→</span>
					</div>
				</a>
				<a class="hub-card hub-card--no-image" href="<?php echo esc_url( home_url( '/jaunrades-nams/par-mums/' ) ); ?>">
					<div class="hub-card__content">
						<h2 class="hub-card__title">Par mums</h2>
						<p class="hub-card__sub">Vēsture, misija un kolektīvs</p>
						<span class="hub-card__arrow" aria-hidden="true">→</span>
					</div>
				</a>
				<a class="hub-card hub-card--no-image" href="<?php echo esc_url( home_url( '/jaunrades-nams/kontakti/' ) ); ?>">
					<div class="hub-card__content">
						<h2 class="hub-card__title">Kontakti</h2>
						<p class="hub-card__sub">Sazinies ar mums</p>
						<span class="hub-card__arrow" aria-hidden="true">→</span>
					</div>
				</a>
				<a class="hub-card hub-card--no-image" href="<?php echo esc_url( home_url( '/jaunrades-nams/piedavajums-izglitibas-iestadem/' ) ); ?>">
					<div class="hub-card__content">
						<h2 class="hub-card__title">Izglītības iestādēm</h2>
						<p class="hub-card__sub">Piedāvājums klasēm un skolotājiem</p>
						<span class="hub-card__arrow" aria-hidden="true">→</span>
					</div>
				</a>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>
