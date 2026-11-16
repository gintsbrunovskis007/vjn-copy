<?php
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
		</div>
	</section>

	<section class="section">
		<div class="container">

			<?php if ( have_posts() ) : ?>
				<div class="jn-latest-posts">
					<?php while ( have_posts() ) : the_post(); ?>
						<article class="jn-post">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="jn-post__thumb">
									<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
										<?php the_post_thumbnail( 'jn-card', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
									</a>
								</div>
							<?php endif; ?>
							<div class="jn-post__body">
								<p class="jn-post__date">
									<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
								</p>
								<h2 class="jn-post__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<p class="jn-post__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
								<span class="jn-post__more" aria-hidden="true"><span class="jn-post__more-label">Lasīt vairāk</span></span>
							</div>
						</article>
					<?php endwhile; ?>
				</div>

				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 1,
						'prev_text' => '←',
						'next_text' => '→',
						'class'     => 'pagination',
					)
				);
				?>
			<?php else : ?>
				<p class="notice">Šeit vēl nav publicētu ierakstu.</p>
			<?php endif; ?>

		</div>
	</section>

</main>

<?php get_footer(); ?>
