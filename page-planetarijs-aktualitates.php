<?php
$page = isset( $_GET['strana'] ) ? max( 1, (int) $_GET['strana'] ) : 1;
$data = jn_get_posts( $page, 9, 7 );

$posts       = $data['posts'];
$total_pages = max( 1, (int) $data['total_pages'] );
$error       = $data['error'];

$base = get_permalink() ?: home_url( '/planetarijs/aktualitates/' );

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
					array( 'name' => 'Aktualitātes', 'url' => '' ),
				)
			);
			?>
			<h1 id="page-title" class="page-header__title">Planetārija aktualitātes</h1>
			<p class="page-header__lead">Jaunākās ziņas un notikumi no Planetārija un Observatorijas.</p>
		</div>
	</section>

	<section class="section">
		<div class="container">

			<?php if ( ! empty( $error ) && empty( $posts ) ) : ?>
				<p class="notice">
					<?php if ( 'out_of_range' === $error && $page > 1 ) : ?>
						Šī lapa neeksistē — <a href="<?php echo esc_url( jn_page_url( $base, 1 ) ); ?>">atgriezties uz pirmo lapu</a>.
					<?php else : ?>
						Aktualitātes šobrīd nevar ielādēt. Lūdzu, mēģiniet vēlāk.
					<?php endif; ?>
				</p>
			<?php elseif ( empty( $posts ) ) : ?>
				<p class="notice">Šeit vēl nav publicētu aktualitāšu.</p>
			<?php else : ?>

				<div class="jn-latest-posts">
					<?php foreach ( $posts as $post ) : ?>
						<?php jn_post_card( $post ); ?>
					<?php endforeach; ?>
				</div>

				<?php
				jn_pagination_render(
					$page,
					$total_pages,
					function ( $n ) use ( $base ) {
						return jn_page_url( $base, $n );
					}
				);
				?>

			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer(); ?>
