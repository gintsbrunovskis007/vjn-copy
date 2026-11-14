<?php
get_header();
?>

<main id="main" class="site-main" tabindex="-1">

	<section class="page-header page-header--404" aria-labelledby="page-title">
		<div class="container">
			<p class="page-header__eyebrow">404</p>
			<h1 id="page-title" class="page-header__title">Lapa nav atrasta</h1>
			<p class="page-header__lead">Meklētā lapa ir pārvietota, dzēsta vai nekad nav pastāvējusi.</p>
		</div>
	</section>

	<section class="section">
		<div class="container util-center">
			<div class="error-actions">
				<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">Uz sākumlapu</a>
				<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/jaunrades-nams/aktualitates/' ) ); ?>">Aktualitātes</a>
			</div>

			<div class="error-search">
				<h2>Meklēt vietnē</h2>
				<?php get_search_form(); ?>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
