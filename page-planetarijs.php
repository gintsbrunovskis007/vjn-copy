<?php
$data  = jn_get_posts( 1, 3, 7 );
$posts = $data['posts'];

get_header();
?>

<main id="main" class="site-main" tabindex="-1">
	<div class="planetarium-hero">
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
				<p class="page-header__lead page-header__lead--single-line">Baltijā modernākais digitālais planetārijs un observatorija ar modernu teleskopu.</p>
			</div>
		</section>

		<section class="section planetarium-hero__visual">
			<div class="container">
				<div class="planetarium-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" focusable="false">
						<path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"></path>
						<path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"></path>
						<path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"></path>
						<path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"></path>
					</svg>
				</div>
			</div>
		</section>
	</div>

	<section class="section section--latest-news">
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
