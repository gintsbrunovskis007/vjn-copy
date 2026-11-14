<?php
$slug = sanitize_title( get_query_var( 'jn_single_post' ) );
$post = $slug ? jn_get_single_post( $slug ) : null;

if ( ! is_array( $post ) ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
	get_template_part( '404' );
	return;
}

$title   = wp_strip_all_tags( $post['title']['rendered'] ?? '' );
$date    = $post['date'] ?? '';
$content = jn_clean_html( $post['content']['rendered'] ?? '' );
$link    = $post['remote_link'] ?? '';
$media   = $post['media'] ?? array();
$hero    = $media['src'] ?? ( $media['url'] ?? '' );
$alt     = $media['alt'] ?? $title;

$content = preg_replace( '#<p>\s*Publicēts?:.*?</p>#is', '', $content );
$content = preg_replace( '~<p>\s*(?:&nbsp;|\s)*</p>~i', '', $content );

$categories          = array_map( 'intval', (array) ( $post['categories'] ?? array() ) );
$is_planetarium_post = in_array( 7, $categories, true );

if ( $is_planetarium_post ) {
	$GLOBALS['is_planetarium_post'] = true;
}

$back_url  = $is_planetarium_post ? home_url( '/planetarijs/aktualitates/' ) : home_url( '/jaunrades-nams/aktualitates/' );
$back_text = $is_planetarium_post ? '← Atpakaļ uz Planetārija aktualitātēm' : '← Atpakaļ uz aktualitātēm';
$cat_name  = $is_planetarium_post ? 'Planetārijs' : 'Aktualitātes';

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

	<article class="single-post">

		<header class="single-post__header<?php echo $hero ? ' single-post__header--with-image' : ''; ?>"
			<?php if ( $hero ) : ?>style="--hero-image: url('<?php echo esc_url( $hero ); ?>');"<?php endif; ?>>
			<div class="container">
				<?php
				jn_breadcrumb_render(
					array(
						array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
						array( 'name' => $cat_name, 'url' => $back_url ),
						array( 'name' => wp_trim_words( $title, 6, '…' ), 'url' => '' ),
					),
					'breadcrumb breadcrumb--light'
				);
				?>

				<?php if ( $date ) : ?>
					<p class="single-post__date">
						<time datetime="<?php echo esc_attr( $date ); ?>">
							<?php echo esc_html( jn_date_lv( $date ) ); ?>
						</time>
					</p>
				<?php endif; ?>

				<h1 class="single-post__title"><?php echo esc_html( $title ); ?></h1>
			</div>
		</header>

		<div class="container single-post__body-wrap">

			<?php if ( $hero ) : ?>
				<figure class="single-post__hero-img">
					<img src="<?php echo esc_url( $hero ); ?>"
						alt="<?php echo esc_attr( $alt ); ?>"
						<?php if ( ! empty( $media['width'] ) ) : ?>width="<?php echo (int) $media['width']; ?>"<?php endif; ?>
						<?php if ( ! empty( $media['height'] ) ) : ?>height="<?php echo (int) $media['height']; ?>"<?php endif; ?>
						<?php if ( ! empty( $media['srcset'] ) ) : ?>srcset="<?php echo esc_attr( $media['srcset'] ); ?>" sizes="(max-width: 900px) 100vw, 900px"<?php endif; ?>
						decoding="async">
				</figure>
			<?php endif; ?>

			<div class="single-post__content">
				<?php echo $content; ?>
			</div>

			<div class="single-post__back">
				<a class="btn btn--ghost" href="<?php echo esc_url( $back_url ); ?>"><?php echo esc_html( $back_text ); ?></a>
				<?php if ( $link ) : ?>
					<a class="single-post__source" href="<?php echo esc_url( $link ); ?>" rel="noopener" target="_blank">
						Skatīt oriģinālā ↗
					</a>
				<?php endif; ?>
			</div>

		</div>

	</article>

</main>

<?php get_footer(); ?>
