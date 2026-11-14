<?php
$data  = jn_get_posts( 1, 3, 7 );
$posts = $data['posts'];

get_header();
?>

<main id="main" class="site-main" tabindex="-1">
	<section class="page-header" aria-labelledby="page-title">
		<div class="container">
			<?php
			jn_breadcrumb_render(
				array(
					array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
					array( 'name' => 'Planetārijs', 'url' => '' ),
				)
			);
			?>
			<h1 id="page-title" class="page-header__title">Planetārijs un Observatorija</h1>
			<p class="page-header__lead">Baltijā modernākais digitālais planetārijs un observatorija ar modernu teleskopu.</p>
		</div>
	</section>

	<section class="section">
		<div class="container">
			<div class="hub-grid hub-grid--planet">
				<a class="hub-card hub-card--no-image" href="<?php echo esc_url( home_url( '/planetarijs/aktualitates/' ) ); ?>">
					<div class="hub-card__content">
						<h2 class="hub-card__title">Aktualitātes</h2>
						<p class="hub-card__sub">Jaunākās ziņas un notikumi</p>
						<span class="hub-card__arrow" aria-hidden="true">→</span>
					</div>
				</a>
				<a class="hub-card hub-card--no-image" href="<?php echo esc_url( home_url( '/planetarijs/galerija/' ) ); ?>">
					<div class="hub-card__content">
						<h2 class="hub-card__title">Galerija</h2>
						<p class="hub-card__sub">Foto no pasākumiem un observatorijas</p>
						<span class="hub-card__arrow" aria-hidden="true">→</span>
					</div>
				</a>
				<a class="hub-card hub-card--no-image" href="<?php echo esc_url( home_url( '/planetarijs/seansu-laiki/' ) ); ?>">
					<div class="hub-card__content">
						<h2 class="hub-card__title">Seansu laiki</h2>
						<p class="hub-card__sub">Apskati filmu un observatorijas grafiku</p>
						<span class="hub-card__arrow" aria-hidden="true">→</span>
					</div>
				</a>
				<a class="hub-card hub-card--no-image" href="<?php echo esc_url( home_url( '/planetarijs/cenas/' ) ); ?>">
					<div class="hub-card__content">
						<h2 class="hub-card__title">Biļešu cenas</h2>
						<p class="hub-card__sub">Informācija par biļešu iegādi</p>
						<span class="hub-card__arrow" aria-hidden="true">→</span>
					</div>
				</a>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="container">
			<div class="section__head section__head--left">
				<h2>Jaunākās aktualitātes</h2>
			</div>

			<?php if ( empty( $posts ) ) : ?>
				<p class="notice">Aktualitātes šobrīd nevar ielādēt.</p>
			<?php else : ?>
				<div class="jn-latest-posts">
					<?php foreach ( $posts as $post ) : ?>
						<?php jn_post_card( $post, array( 'heading' => 'h3' ) ); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer(); ?>
