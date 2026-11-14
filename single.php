<?php
get_header();
?>

<main id="main" class="site-main" tabindex="-1">

	<?php while ( have_posts() ) : the_post(); ?>
		<article class="single-post">

			<header class="single-post__header<?php echo has_post_thumbnail() ? ' single-post__header--with-image' : ''; ?>"
				<?php if ( has_post_thumbnail() ) : ?>style="--hero-image: url('<?php echo esc_url( get_the_post_thumbnail_url( null, 'full' ) ); ?>');"<?php endif; ?>>
				<div class="container">
					<?php
					jn_breadcrumb_render(
						array(
							array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
							array( 'name' => 'Aktualitātes', 'url' => home_url( '/jaunrades-nams/aktualitates/' ) ),
							array( 'name' => wp_trim_words( get_the_title(), 6, '…' ), 'url' => '' ),
						),
						'breadcrumb breadcrumb--light'
					);
					?>

					<p class="single-post__date">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
							<?php echo esc_html( get_the_date() ); ?>
						</time>
					</p>

					<h1 class="single-post__title"><?php the_title(); ?></h1>
				</div>
			</header>

			<div class="container single-post__body-wrap">

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="single-post__hero-img">
						<?php the_post_thumbnail( 'large', array( 'decoding' => 'async' ) ); ?>
					</figure>
				<?php endif; ?>

				<div class="single-post__content">
					<?php the_content(); ?>
				</div>

				<div class="single-post__back">
					<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/jaunrades-nams/aktualitates/' ) ); ?>">← Atpakaļ uz aktualitātēm</a>
				</div>

			</div>

		</article>
	<?php endwhile; ?>

</main>

<?php get_footer(); ?>
