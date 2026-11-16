<?php
$content = jn_remote_page_html( 'seansu-laiki' );

if ( '' !== $content ) {
	$content = preg_replace( '~<style[^>]*>.*?</style>~is', '', $content );
	$content = preg_replace( '~<div[^>]*class="[^"]*fusion-clearfix[^"]*"[^>]*>\s*</div>~is', '', $content );
	$content = preg_replace( '~<div[^>]*class="[^"]*fusion-layout-column[^"]*"[^>]*>\s*<div[^>]*class="[^"]*fusion-column-wrapper[^"]*"[^>]*>\s*</div>\s*</div>~is', '', $content );
	$content = preg_replace( '~<div[^>]*class="[^"]*fusion-button-wrapper[^"]*"[^>]*>.*?<a[^>]*href="([^"]+)"[^>]*>.*?<span[^>]*>(.*?)</span>.*?</a>.*?</div>~is', '<div class="schedule-btn-wrap"><a href="$1" class="btn btn--primary" target="_blank" rel="noopener">$2</a></div>', $content );
	$content = preg_replace( '~<p[^>]*>\s*<strong>\s*Pirmdiena\s*[-–]\s*slēgts\s*<br\s*/?>\s*Otrdiena\s*[-–]\s*piektdiena\s*</strong>\s*:?\s*</p>~iu', '<h3 class="schedule-day schedule-day--closed">Pirmdiena – slēgts</h3><h3 class="schedule-day">Otrdiena – piektdiena</h3>', $content );
	$content = preg_replace( '~<p[^>]*>\s*<strong>\s*(Sestdiena\s*[-–]\s*svētdiena)\s*</strong>\s*:?\s*(?:&nbsp;|\s)*</p>~iu', '<h3 class="schedule-day">$1</h3>', $content );
	$content = preg_replace( '~<strong><u>(\d{2}:\d{2})<\/u>.*?<\/strong>~is', '<span class="schedule-time">$1</span>', $content );
	$content = preg_replace( '~<span[^>]*text-decoration:\s*underline;?[^>]*><strong>(\d{2}:\d{2})<\/strong><\/span>~is', '<span class="schedule-time">$1</span>', $content );
	$content = preg_replace( '~<span class="has-inline-color has-vivid-red-color">\s*(Jaunums!)\s*<\/span>~is', '<span class="schedule-new">$1</span>', $content );
	$content = preg_replace( '~<span class="has-inline-color has-vivid-red-color">\s*\*\s*<\/span>~is', '', $content );
	$content = preg_replace( '~(?<=Observatorija)\*~u', '', $content );
	$content = preg_replace( '~<p([^>]*)>\s*\*\s*~iu', '<p$1>', $content );
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
					array( 'name' => 'Seansu laiki', 'url' => '' ),
				)
			);
			?>
			<h1 id="page-title" class="page-header__title">Seansu laiki</h1>
			<p class="page-header__lead">Planetārija un Observatorijas filmu grafiks.</p>
		</div>
	</section>

	<section class="section">
		<div class="container">

			<?php if ( '' === $content ) : ?>
				<p class="notice">Seansu laiki šobrīd nav pieejami. Lūdzu, mēģiniet vēlāk.</p>
			<?php else : ?>
				<div class="schedule-wrap">
					<?php echo $content; ?>
				</div>
			<?php endif; ?>

		</div>
	</section>
</main>

<?php get_footer(); ?>
