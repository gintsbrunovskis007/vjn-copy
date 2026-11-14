<?php
$per_page     = 9;
$current_page = isset( $_GET['strana'] ) ? max( 1, (int) $_GET['strana'] ) : 1;

$data        = jn_get_posts( $current_page, $per_page, 8 );
$posts       = $data['posts'];
$total_pages = max( 1, (int) $data['total_pages'] );
$error       = $data['error'];

$base = get_permalink() ?: home_url( '/jaunrades-nams/aktualitates/' );

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

	<section class="page-header" aria-labelledby="page-title">
		<div class="container">
			<?php
			jn_breadcrumb_render(
				array(
					array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
					array( 'name' => 'Aktualitātes', 'url' => '' ),
				)
			);
			?>

			<h1 id="page-title" class="page-header__title">Aktualitātes</h1>
			<p class="page-header__lead">Jaunākie notikumi, nodarbības un pasākumi Ventspils Jaunrades namā.</p>
		</div>
	</section>

	<section class="section">
		<div class="container">

			<?php if ( empty( $posts ) ) : ?>

				<p class="notice">
					<?php if ( 'out_of_range' === $error && $current_page > 1 ) : ?>
						Šī lapa neeksistē — <a href="<?php echo esc_url( jn_page_url( $base, 1 ) ); ?>">atgriezties uz pirmo lapu</a>.
					<?php elseif ( ! empty( $error ) ) : ?>
						Aktualitātes šobrīd nevar ielādēt.
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Atgriezties uz sākumlapu</a>.
					<?php else : ?>
						Šeit vēl nav publicētu aktualitāšu.
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Atgriezties uz sākumlapu</a>.
					<?php endif; ?>
				</p>

			<?php else : ?>

				<div class="jn-latest-posts">
					<?php foreach ( $posts as $post ) : ?>
						<?php jn_post_card( $post ); ?>
					<?php endforeach; ?>
				</div>

				<?php
				jn_pagination_render(
					$current_page,
					$total_pages,
					function ( $page ) use ( $base ) {
						return jn_page_url( $base, $page );
					}
				);
				?>

			<?php endif; ?>

		</div>
	</section>

</main>

<?php get_footer(); ?>
