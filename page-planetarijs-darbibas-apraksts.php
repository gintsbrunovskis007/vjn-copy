<?php
$content = jn_remote_page_html( 'darbibas-apraksts' );

if ( '' !== $content ) {
	$content = preg_replace( '~<style[^>]*>.*?</style>~is', '', $content );
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
					array( 'name' => 'Darbības apraksts', 'url' => '' ),
				)
			);
			?>
			<h1 id="page-title" class="page-header__title">Darbības apraksts</h1>
			<p class="page-header__lead">Iepazīstieties ar mūsu Planetāriju un Observatoriju.</p>
		</div>
	</section>

	<section class="section section--tight">
		<div class="container darbibas-wrap">
			<?php if ( '' === $content ) : ?>
				<p class="notice">Informācija šobrīd nav pieejama. Lūdzu, mēģiniet vēlāk.</p>
			<?php else : ?>
				<?php echo $content; ?>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
