<?php
$content = jn_remote_page_html( 'cenas' );

if ( '' !== $content ) {
	$content = preg_replace( '~<style[^>]*>.*?</style>~is', '', $content );
	$content = preg_replace( '~<div class="wp-block-image">.*?</div>~is', '', $content );
	$content = preg_replace( '~<div[^>]*class="[^"]*fusion-title[^"]*"[^>]*>.*?<h3[^>]*>(.*?)</h3>.*?</div>~is', '<h3 class="price-heading">$1</h3>', $content );
	$content = preg_replace( '~<div[^>]*class="[^"]*fusion-button-wrapper[^"]*"[^>]*>.*?<a[^>]*href="([^"]+)"[^>]*>.*?<span[^>]*>(.*?)</span>.*?</a>.*?</div>~is', '<a href="$1" class="btn btn--primary" target="_blank" rel="noopener">$2</a>', $content );
	$content = str_replace( array( 'class="fusion-table table-1"', 'class="fusion-table"' ), 'class="price-table-wrap"', $content );
	$content = str_replace( '<table', '<table class="price-table"', $content );
	$content = preg_replace( '~<p>\s*(?:&nbsp;|\s)*</p>~i', '', $content );
	$content = wp_kses_post( $content );
}

get_header();
?>

<main id="main" class="site-main" tabindex="-1">
	<section class="page-header" aria-labelledby="page-title">
		<div class="container">
			<?php
			jn_breadcrumb_render(
				array(
					array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
					array( 'name' => 'Planetārijs', 'url' => home_url( '/planetarijs/' ) ),
					array( 'name' => 'Cenas', 'url' => '' ),
				)
			);
			?>
			<h1 id="page-title" class="page-header__title">Biļešu cenas</h1>
			<p class="page-header__lead">Planetārija un Observatorijas apmeklējuma izcenojumi.</p>
		</div>
	</section>

	<section class="section">
		<div class="container price-content-wrap">

			<?php if ( '' === $content ) : ?>
				<p class="notice">Cenas šobrīd nav pieejamas. Lūdzu, mēģiniet vēlāk.</p>
			<?php else : ?>
				<?php echo $content; ?>
			<?php endif; ?>

		</div>
	</section>
</main>

<?php get_footer(); ?>
