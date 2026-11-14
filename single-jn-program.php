<?php
$slug = sanitize_title( get_query_var( 'jn_single_program' ) );

$kat      = '';
$raw_uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
$req_path = trim( (string) wp_parse_url( $raw_uri, PHP_URL_PATH ), '/' );
$segments = $req_path ? explode( '/', $req_path ) : array();
$pos      = array_search( 'nodarbiba', $segments, true );

if ( false !== $pos && isset( $segments[ $pos + 2 ] ) ) {
	$kat = sanitize_key( $segments[ $pos + 1 ] );
}

$hubs     = jn_hubs();
$cat_name = isset( $hubs[ $kat ] ) ? $hubs[ $kat ]['name'] : ucfirst( str_replace( '-', ' ', $kat ) );

$page = $slug ? jn_get_single_post( $slug ) : null;

if ( ! is_array( $page ) ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
	get_template_part( '404' );
	return;
}

$title   = wp_strip_all_tags( $page['title']['rendered'] ?? '' );
$content = $page['content']['rendered'] ?? '';
$link    = $page['remote_link'] ?? '';
$media   = $page['media'] ?? array();

$hero = $media['src'] ?? ( $media['url'] ?? '' );
$alt  = $media['alt'] ?? $title;

if ( $hero ) {
	$content = preg_replace( '~<img[^>]+' . preg_quote( $hero, '~' ) . '[^>]*>~i', '', $content, 1 );
}

$content = preg_replace( '~<div[^>]*fusion-separator[^>]*>.*?</div>~is', '', $content );
$content = preg_replace( '~<div[^>]*fusion-sep-clear[^>]*>\s*</div>~is', '', $content );
$content = preg_replace( '~<div[^>]*fusion-clearfix[^>]*>\s*</div>~is', '', $content );
$content = preg_replace( '~<p>\s*(?:&nbsp;|\s)*</p>~i', '', $content );
$content = jn_clean_html( $content );

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

	<article class="single-program">

		<header class="single-program__header<?php echo $hero ? ' single-program__header--with-image' : ''; ?>"
			<?php if ( $hero ) : ?>style="--hero-image: url('<?php echo esc_url( $hero ); ?>');"<?php endif; ?>>
			<div class="container">
				<?php
				$breadcrumb = array(
					array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
					array( 'name' => 'Nodarbības', 'url' => home_url( '/jaunrades-nams/pulcini/' ) ),
				);
				if ( $kat ) {
					$breadcrumb[] = array(
						'name' => $cat_name,
						'url'  => add_query_arg( 'kat', $kat, home_url( '/jaunrades-nams/pulcini/' ) ),
					);
				}
				$breadcrumb[] = array( 'name' => $title, 'url' => '' );
				jn_breadcrumb_render( $breadcrumb, 'breadcrumb breadcrumb--light' );
				?>

				<h1 class="single-program__title"><?php echo esc_html( $title ); ?></h1>
			</div>
		</header>

		<div class="container single-program__body-wrap">

			<?php if ( $hero ) : ?>
				<figure class="single-program__hero-img">
					<img src="<?php echo esc_url( $hero ); ?>"
						alt="<?php echo esc_attr( $alt ); ?>"
						<?php if ( ! empty( $media['width'] ) ) : ?>width="<?php echo (int) $media['width']; ?>"<?php endif; ?>
						<?php if ( ! empty( $media['height'] ) ) : ?>height="<?php echo (int) $media['height']; ?>"<?php endif; ?>
						<?php if ( ! empty( $media['srcset'] ) ) : ?>srcset="<?php echo esc_attr( $media['srcset'] ); ?>" sizes="(max-width: 900px) 100vw, 900px"<?php endif; ?>
						decoding="async">
				</figure>
			<?php endif; ?>

			<div class="single-program__content">
				<?php echo $content; ?>
			</div>

			<div class="single-program__back">
				<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/jaunrades-nams/pulcini/' ) ); ?>">← Atpakaļ uz nodarbībām</a>
				<?php if ( $link ) : ?>
					<a class="single-program__source" href="<?php echo esc_url( $link ); ?>" rel="noopener" target="_blank">
						Skatīt oriģinālā ↗
					</a>
				<?php endif; ?>
			</div>

		</div>

	</article>

</main>

<?php get_footer(); ?>
