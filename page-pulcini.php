<?php
$hubs     = jn_hubs();
$kat      = isset( $_GET['kat'] ) ? sanitize_key( $_GET['kat'] ) : '';
$per_page = 12;
$current  = isset( $_GET['strana'] ) ? max( 1, (int) $_GET['strana'] ) : 1;

$changes  = jn_load_lesson_changes();
$schedule = jn_schedule_pdf();

$hub_pages = jn_get_pages_by_slugs( array_keys( $hubs ), 'slug,content' );
$hub_data  = array();

foreach ( array_keys( $hubs ) as $hub_slug ) {
	$hub_data[ $hub_slug ] = jn_parse_hub_page( $hub_pages[ $hub_slug ] ?? array(), $hub_slug );
}

$catalog = array();
foreach ( $hub_data as $data ) {
	foreach ( $data['categories'] as $cat_slug => $cat ) {
		if ( ! isset( $catalog[ $cat_slug ] ) ) {
			$catalog[ $cat_slug ] = array( 'name' => $cat['name'], 'programs' => array() );
		}
		$catalog[ $cat_slug ]['programs'] = array_merge( $catalog[ $cat_slug ]['programs'], $cat['programs'] );
	}
}

if ( $kat && isset( $hub_data[ $kat ] ) ) {
	$all = array();
	foreach ( $hub_data[ $kat ]['categories'] as $cat ) {
		$all = array_merge( $all, $cat['programs'] );
	}
	$catalog[ $kat ] = array( 'name' => $hubs[ $kat ]['name'], 'programs' => $all );
}

$detail = $kat && isset( $catalog[ $kat ] );

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

<?php if ( ! $detail ) : ?>

	<section class="page-header" aria-labelledby="page-title">
		<div class="container">
			<?php
			jn_breadcrumb_render(
				array(
					array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
					array( 'name' => 'Nodarbības', 'url' => '' ),
				)
			);
			?>
			<h1 id="page-title" class="page-header__title">Nodarbības</h1>
			<p class="page-header__lead">Izvēlies jomu, kas tev interesē.</p>
		</div>
	</section>

	<section class="section pulcini-top-section">
		<div class="container">

			<div class="pulcini-toolbar">
				<span class="pulcini-toolbar__label">Saraksts</span>
				<div class="pulcini-toolbar__text">
					<strong>Nodarbības <?php echo esc_html( jn_school_year() ); ?></strong>
					<?php if ( ! empty( $schedule['date'] ) ) : ?>
						<span class="pulcini-toolbar__date">atjaunots <?php echo esc_html( $schedule['date'] ); ?></span>
					<?php endif; ?>
				</div>
				<a class="pulcini-toolbar__btn"
					href="<?php echo esc_url( $schedule['url'] ); ?>"
					target="_blank" rel="noopener">
					Lejupielādēt PDF
					<span aria-hidden="true">↓</span>
				</a>
			</div>

			<?php if ( ! empty( $changes['items'] ) ) : ?>
				<div class="pulcini-changes-note">
					<div class="pulcini-changes-note__head">
						<span class="pulcini-changes-note__badge">Izmaiņas</span>
						<?php if ( $changes['modified'] ) : ?>
							<span class="pulcini-changes-note__updated">
								<?php echo esc_html( wp_date( 'd.m.Y · H:i', strtotime( $changes['modified'] ) ) ); ?>
							</span>
						<?php endif; ?>
					</div>

					<ul class="pulcini-changes-note__list">
						<?php foreach ( $changes['items'] as $item ) : ?>
							<li class="pulcini-changes-note__item">
								<?php if ( $item['prefix'] ) : ?>
									<span class="pulcini-changes-note__change"><?php echo esc_html( $item['prefix'] ); ?></span>
								<?php endif; ?>
								<?php if ( $item['course'] || $item['teacher'] ) : ?>
									<span class="pulcini-changes-note__meta">
										<?php if ( $item['course'] ) : ?>
											<strong><?php echo esc_html( $item['course'] ); ?></strong>
										<?php endif; ?>
										<?php if ( $item['course'] && $item['teacher'] ) : ?><span aria-hidden="true"> · </span><?php endif; ?>
										<?php if ( $item['teacher'] ) : ?><?php echo esc_html( $item['teacher'] ); ?><?php endif; ?>
									</span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

		</div>
	</section>

	<section class="section">
		<div class="container">
			<div class="hub-grid">
				<?php foreach ( $hubs as $hub_slug => $hub_meta ) :
					$img        = $hub_data[ $hub_slug ]['image'] ?? '';
					$prog_count = 0;
					foreach ( $hub_data[ $hub_slug ]['categories'] as $cat ) {
						$prog_count += count( $cat['programs'] );
					}
					?>
					<a class="hub-card<?php echo $img ? '' : ' hub-card--no-image'; ?>"
						href="<?php echo esc_url( jn_cat_url( $hub_slug ) ); ?>"
						style="--hub-color: <?php echo esc_attr( $hub_meta['color'] ); ?>;">

						<?php if ( $img ) : ?>
							<div class="hub-card__media">
								<img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy" decoding="async">
							</div>
						<?php endif; ?>

						<div class="hub-card__content">
							<p class="hub-card__count"><?php echo esc_html( $prog_count ); ?> programmas</p>
							<h2 class="hub-card__title"><?php echo esc_html( $hub_meta['name'] ); ?></h2>
							<p class="hub-card__sub"><?php echo esc_html( $hub_meta['sub'] ); ?></p>
							<span class="hub-card__arrow" aria-hidden="true">→</span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

<?php else : ?>

	<?php
	$cat      = $catalog[ $kat ];
	$slug_map = $cat['programs'];
	$details  = jn_program_details( array_keys( $slug_map ) );
	$items    = array_values( array_filter( $details ) );
	usort(
		$items,
		function ( $a, $b ) {
			return strcoll( $a['title'], $b['title'] );
		}
	);

	$total   = count( $items );
	$pages   = max( 1, (int) ceil( $total / $per_page ) );
	$current = min( $current, $pages );
	$slice   = array_slice( $items, ( $current - 1 ) * $per_page, $per_page );

	$hub_meta = $hubs[ $kat ] ?? array( 'name' => $cat['name'], 'sub' => '', 'color' => '#e8711f' );
	$hub_img  = $hub_data[ $kat ]['image'] ?? '';
	?>

	<section class="page-header page-header--hub" aria-labelledby="page-title"
		<?php if ( $hub_img ) : ?>style="--hero-image: url('<?php echo esc_url( $hub_img ); ?>');"<?php endif; ?>>
		<div class="container">
			<?php
			jn_breadcrumb_render(
				array(
					array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
					array( 'name' => 'Nodarbības', 'url' => jn_cat_url() ),
					array( 'name' => $hub_meta['name'], 'url' => '' ),
				),
				'breadcrumb breadcrumb--light'
			);
			?>
			<h1 id="page-title" class="page-header__title page-header__title--light"><?php echo esc_html( $hub_meta['name'] ); ?></h1>
			<?php if ( ! empty( $hub_meta['sub'] ) ) : ?>
				<p class="page-header__lead page-header__lead--light"><?php echo esc_html( $hub_meta['sub'] ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="section">
		<div class="container">

			<?php if ( empty( $slice ) ) : ?>
				<p class="notice">Šajā kategorijā šobrīd nav pieejamu nodarbību.</p>
			<?php else : ?>

				<div class="jn-latest-posts">
					<?php foreach ( $slice as $program ) :
						$slug_value = $program['slug'] ?? '';
						if ( ! $slug_value ) {
							$path       = wp_parse_url( $program['link'], PHP_URL_PATH );
							$slug_value = $path ? basename( rtrim( $path, '/' ) ) : '';
						}
						$internal = $slug_value ? jn_single_url_program( $slug_value, $kat ) : $program['link'];
						?>
						<article class="jn-post">
							<?php if ( ! empty( $program['image'] ) ) : ?>
								<div class="jn-post__thumb">
									<img src="<?php echo esc_url( $program['image'] ); ?>"
										alt="<?php echo esc_attr( $program['alt'] ); ?>"
										loading="lazy" decoding="async">
								</div>
							<?php endif; ?>
							<div class="jn-post__body">
								<?php if ( ! empty( $program['age'] ) || ! empty( $program['teacher'] ) ) : ?>
									<p class="jn-post__date"><?php echo esc_html( implode( ' · ', array_filter( array( $program['age'], $program['teacher'] ) ) ) ); ?></p>
								<?php endif; ?>
								<h2 class="jn-post__title">
									<a href="<?php echo esc_url( $internal ); ?>"><?php echo esc_html( $program['title'] ); ?></a>
								</h2>
								<?php if ( ! empty( $program['excerpt'] ) ) : ?>
									<p class="jn-post__excerpt"><?php echo esc_html( $program['excerpt'] ); ?></p>
								<?php endif; ?>
								<span class="jn-post__more" aria-hidden="true">Uzzināt vairāk</span>
							</div>
						</article>
					<?php endforeach; ?>
				</div>

				<?php
				jn_pagination_render(
					$current,
					$pages,
					function ( $page ) use ( $kat ) {
						return jn_cat_url( $kat, $page );
					}
				);
				?>

			<?php endif; ?>

			<div class="util-center">
				<a class="btn btn--ghost" href="<?php echo esc_url( jn_cat_url() ); ?>">← Atpakaļ uz kategorijām</a>
			</div>

		</div>
	</section>

<?php endif; ?>

</main>

<?php get_footer(); ?>
