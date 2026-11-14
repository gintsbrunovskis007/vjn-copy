<?php
$album_url = isset( $_GET['albums'] ) ? jn_gallery_validate_album_url( urldecode( wp_unslash( $_GET['albums'] ) ) ) : '';
$index_url = get_permalink() ?: home_url( '/jaunrades-nams/galerija/' );

if ( $album_url ) {
	$albums      = jn_gallery_load_albums( 'main' );
	$images      = jn_gallery_load_album_images( $album_url );
	$album_title = jn_gallery_album_title( $album_url, $albums );

	get_header();
	?>
	<main id="main" class="site-main" tabindex="-1">
		<section class="page-header" aria-labelledby="page-title">
			<div class="container">
				<?php
				jn_breadcrumb_render(
					array(
						array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
						array( 'name' => 'Galerija', 'url' => $index_url ),
						array( 'name' => wp_trim_words( $album_title, 6, '…' ), 'url' => '' ),
					)
				);
				?>
				<h1 id="page-title" class="page-header__title"><?php echo esc_html( $album_title ); ?></h1>
				<?php if ( ! empty( $images ) ) : ?>
					<p class="page-header__lead"><?php echo esc_html( count( $images ) ); ?> foto</p>
				<?php endif; ?>
			</div>
		</section>

		<section class="section">
			<div class="container">
				<?php if ( empty( $images ) ) : ?>
					<p class="notice">Albuma bildes šobrīd nevar ielādēt. Lūdzu, mēģiniet vēlāk.</p>
				<?php else : ?>
					<div class="album-grid" id="jn-album-grid">
						<?php foreach ( $images as $index => $image ) : ?>
							<a class="album-grid__item"
								href="<?php echo esc_url( $image['full'] ); ?>"
								data-index="<?php echo (int) $index; ?>"
								data-full="<?php echo esc_url( $image['full'] ); ?>"
								aria-label="Atvērt foto <?php echo (int) ( $index + 1 ); ?>"
								rel="noopener">
								<img src="<?php echo esc_url( $image['thumb'] ); ?>" alt="" loading="lazy" decoding="async">
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<div class="util-center">
					<a class="btn btn--ghost" href="<?php echo esc_url( $index_url ); ?>">← Atpakaļ uz galeriju</a>
				</div>
			</div>
		</section>

		<?php jn_lightbox_markup(); ?>
	</main>
	<?php
	get_footer();
	return;
}

$albums   = jn_gallery_load_albums( 'main' );
$per_page = 24;
$current  = isset( $_GET['strana'] ) ? max( 1, (int) $_GET['strana'] ) : 1;
$total    = count( $albums );
$pages    = max( 1, (int) ceil( $total / $per_page ) );
$current  = min( $current, $pages );
$slice    = array_slice( $albums, ( $current - 1 ) * $per_page, $per_page );

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

	<section class="page-header" aria-labelledby="page-title">
		<div class="container">
			<?php
			jn_breadcrumb_render(
				array(
					array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
					array( 'name' => 'Galerija', 'url' => '' ),
				)
			);
			?>
			<h1 id="page-title" class="page-header__title">Galerija</h1>
			<p class="page-header__lead">Pasākumu, nodarbību un koncertu foto mirkļi.</p>
		</div>
	</section>

	<section class="section">
		<div class="container">

			<?php if ( empty( $albums ) ) : ?>
				<p class="notice">Galerija šobrīd nevar ielādēt.</p>
			<?php else : ?>

				<div class="gallery-grid">
					<?php foreach ( $slice as $album ) : ?>
						<a class="gallery-card" href="<?php echo esc_url( add_query_arg( 'albums', rawurlencode( $album['link'] ), $index_url ) ); ?>">
							<div class="gallery-card__media">
								<img src="<?php echo esc_url( $album['thumb'] ); ?>"
									alt="<?php echo esc_attr( $album['title'] ); ?>"
									loading="lazy" decoding="async">
								<span class="gallery-card__count"><?php echo esc_html( $album['count'] ); ?> foto</span>
							</div>
							<div class="gallery-card__body">
								<h2 class="gallery-card__title"><?php echo esc_html( $album['title'] ); ?></h2>
							</div>
						</a>
					<?php endforeach; ?>
				</div>

				<?php
				jn_pagination_render(
					$current,
					$pages,
					function ( $page ) use ( $index_url ) {
						return jn_page_url( $index_url, $page );
					}
				);
				?>

			<?php endif; ?>

		</div>
	</section>

</main>

<?php get_footer(); ?>
