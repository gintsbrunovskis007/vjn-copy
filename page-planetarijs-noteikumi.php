<?php
$content = jn_remote_page_html( 'noteikumi' );

if ( '' !== $content ) {
	$content = preg_replace( '~<style[^>]*>.*?</style>~is', '', $content );
	$content = preg_replace( '~<div class="wp-block-image">.*?</div>~is', '', $content );
	$content = preg_replace( '~<div[^>]*class="[^"]*fusion-title[^"]*"[^>]*>.*?<h1[^>]*>(.*?)</h1>.*?</div>~is', '', $content );
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
					array( 'name' => 'Noteikumi', 'url' => '' ),
				)
			);
			?>
			<h1 id="page-title" class="page-header__title">Noteikumi</h1>
			<p class="page-header__lead">Iekšējās kārtības un drošības noteikumi.</p>
		</div>
	</section>

	<section class="section section--tight">
		<div class="container">
			<?php if ( '' === $content ) : ?>
				<p class="notice">Noteikumi šobrīd nav pieejami. Lūdzu, mēģiniet vēlāk.</p>
			<?php else : ?>
				<div class="rules-wrap">
					<?php echo $content; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
