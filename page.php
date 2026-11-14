<?php
get_header();
?>

<main id="main" class="site-main" tabindex="-1">

	<?php while ( have_posts() ) : the_post(); ?>
		<article class="single-page">

			<section class="page-header" aria-labelledby="page-title">
				<div class="container">
					<?php
					jn_breadcrumb_render(
						array(
							array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
							array( 'name' => get_the_title(), 'url' => '' ),
						)
					);
					?>
					<h1 id="page-title" class="page-header__title"><?php the_title(); ?></h1>
				</div>
			</section>

			<section class="section">
				<div class="container">
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				</div>
			</section>

		</article>
	<?php endwhile; ?>

</main>

<?php get_footer(); ?>
