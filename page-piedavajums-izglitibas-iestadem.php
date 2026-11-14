<?php
$html = jn_remote_page_html( 'piedavajums-izglitibas-iestadem' );

if ( '' !== $html ) {
	$html = preg_replace( '#<center>(.*?)</center>#is', '<div class="offer-price">$1</div>', $html );
	$html = jn_clean_html( $html );
	$html = '<div class="jn-offer-wrap">' . $html . '</div>';
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
					array( 'name' => 'Resursi', 'url' => home_url( '/jaunrades-nams/dokumenti/' ) ),
					array( 'name' => 'Piedāvājums izglītības iestādēm', 'url' => '' ),
				)
			);
			?>
			<h1 id="page-title" class="page-header__title">Piedāvājums izglītības iestādēm</h1>
			<p class="page-header__lead">Ekskursijas, laboratorijas darbi un planetārija seansi skolēnu grupām.</p>
		</div>
	</section>

	<section class="section">
		<div class="container">
			<?php if ( '' !== $html ) : ?>
				<?php echo $html; ?>
			<?php else : ?>
				<p class="notice">Informācija šobrīd nav pieejama.</p>
			<?php endif; ?>

			<div class="util-center mt-64">
				<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/jaunrades-nams/dokumenti/' ) ); ?>">← Atpakaļ uz resursiem</a>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
