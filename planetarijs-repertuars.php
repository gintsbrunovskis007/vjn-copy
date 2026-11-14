<?php
$content = jn_remote_page_html( 'repertuars' );

$movies     = array();
$info_boxes = array();

if ( '' !== $content ) {
	if ( preg_match_all( '/<div id="post-(\d+)".*?<a href="([^"]+)">.*?<img.*?src="([^"]+)".*?<h2 class="entry-title"><a href="([^"]+)">(.*?)<\/a><\/h2>.*?<div class="fusion-post-content-container"><p>(.*?)<\/p>/is', $content, $matches, PREG_SET_ORDER ) ) {
		foreach ( $matches as $m ) {
			$title = trim( wp_strip_all_tags( $m[5] ) );
			if ( '' === $title ) {
				continue;
			}

			$path = wp_parse_url( $m[4], PHP_URL_PATH );
			$slug = $path ? sanitize_title( basename( rtrim( $path, '/' ) ) ) : '';

			$movies[] = array(
				'img'     => esc_url_raw( $m[3] ),
				'title'   => $title,
				'excerpt' => wp_trim_words( wp_strip_all_tags( $m[6] ), 18 ),
				'link'    => $slug ? jn_single_url_post( $slug ) : esc_url_raw( $m[4] ),
			);
		}
	}

	if ( preg_match_all( '/<div class="reading-box-additional">(.*?)<\/div>/is', $content, $boxes ) ) {
		foreach ( $boxes[1] as $box ) {
			$info_boxes[] = trim( wp_strip_all_tags( $box ) );
		}
	}
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
					array( 'name' => 'Repertuārs', 'url' => '' ),
				)
			);
			?>
			<h1 id="page-title" class="page-header__title">Repertuārs</h1>
			<p class="page-header__lead">Planetārija filmu un šovu katalogs.</p>
		</div>
	</section>

	<section class="section section--tight">
		<div class="container">

			<?php if ( empty( $movies ) && empty( $info_boxes ) ) : ?>
				<p class="notice">Repertuārs šobrīd nav pieejams. Lūdzu, mēģiniet vēlāk.</p>
			<?php else : ?>

				<div class="repertuars-layout">
					<div class="movies-section">
						<div class="movies-grid">
							<?php foreach ( $movies as $movie ) : ?>
								<a href="<?php echo esc_url( $movie['link'] ); ?>" class="movie-card">
									<img src="<?php echo esc_url( $movie['img'] ); ?>" class="movie-card__img" alt="<?php echo esc_attr( $movie['title'] ); ?>" loading="lazy" decoding="async">
									<div class="movie-card__body">
										<h3 class="movie-card__title"><?php echo esc_html( $movie['title'] ); ?></h3>
										<p class="movie-card__excerpt"><?php echo esc_html( $movie['excerpt'] ); ?></p>
									</div>
								</a>
							<?php endforeach; ?>
						</div>
					</div>

					<?php if ( ! empty( $info_boxes ) ) : ?>
						<div class="info-sidebar">
							<h2 class="info-sidebar-title">Informācija</h2>
							<?php foreach ( $info_boxes as $box ) : ?>
								<div class="info-box">
									<?php echo esc_html( $box ); ?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

			<?php endif; ?>

		</div>
	</section>
</main>

<?php get_footer(); ?>
