<?php
$data  = jn_get_posts( 1, 3, 8 );
$posts = $data['posts'];

$news_url = home_url( '/jaunrades-nams/aktualitates/' );
?>
<?php get_header(); ?>

<main id="main" class="site-main" tabindex="-1">

	<div class="site-effect" aria-hidden="true"
		style="background-image: url('<?php echo esc_url( jn_asset( 'effect.svg' ) ); ?>');"></div>

	<section class="hero" aria-labelledby="hero-title">
		<div class="hero__inner">
			<div class="hero__content">
				<p class="hero__eyebrow">Laipni lūdzam</p>
				<h1 id="hero-title" class="hero__title">
					Ventspils
					<em>Jaunrades nams</em>
				</h1>
				<p class="hero__lead">
					Bērns ir ne tikai tas, kas viņš ir šodien, bet arī tas, par ko viņš var kļūt rīt.
					<cite>— Ventspils Jaunrades nams</cite>
				</p>
				<div class="hero__actions">
					<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/jaunrades-nams/pulcini/' ) ); ?>">Apskati nodarbības</a>
					<a class="btn btn--ghost" href="<?php echo esc_url( $news_url ); ?>">Aktualitātes</a>
				</div>
				<div class="hero__stats">
					<div class="hero__stat">
						<strong>1955</strong>
						<span>Kopš dibināšanas</span>
					</div>
					<div class="hero__stat">
						<strong>55+</strong>
						<span>Pulciņi</span>
					</div>
					<div class="hero__stat">
						<strong>3–25</strong>
						<span>Gadu vecums</span>
					</div>
				</div>
			</div>
			<div class="hero__visual">
				<div class="hero__image">
					<img src="<?php echo esc_url( jn_asset( 'img/hero-1200.jpg' ) ); ?>"
						srcset="<?php echo esc_attr( jn_asset( 'img/hero-480.jpg' ) ); ?> 480w, <?php echo esc_attr( jn_asset( 'img/hero-800.jpg' ) ); ?> 800w, <?php echo esc_attr( jn_asset( 'img/hero-1200.jpg' ) ); ?> 1200w"
						sizes="(max-width: 720px) 100vw, (max-width: 1100px) 50vw, 600px"
						alt="Ventspils Jaunrades nams"
						width="1200"
						height="666"
						loading="eager"
						fetchpriority="high"
						decoding="async">
				</div>
			</div>
		</div>
	</section>

	<section class="section section--flush">
		<div class="container">
			<div class="planetarium-banner">
				<div class="planetarium-banner__content">
					<span class="planetarium-banner__tag">Ventspils Planetārijs</span>
					<h2 class="planetarium-banner__title">Atklāj Visumu</h2>
					<p class="planetarium-banner__desc">Apmeklē Baltijā modernāko digitālo planetāriju un observatoriju.</p>
				</div>
				<a href="<?php echo esc_url( home_url( '/planetarijs/' ) ); ?>" class="btn planetarium-banner__btn">
					Uzzināt vairāk <span aria-hidden="true">→</span>
				</a>
			</div>
		</div>
	</section>

	<section class="section" aria-labelledby="news-title">
		<div class="container">
			<div class="section__head">
				<h2 id="news-title">Aktualitātes</h2>
				<p>Jaunākie notikumi, nodarbības un pasākumi Ventspils Jaunrades namā.</p>
			</div>

			<?php if ( empty( $posts ) ) : ?>
				<p class="notice">
					Aktualitātes šobrīd nevar ielādēt.
					<a href="<?php echo esc_url( $news_url ); ?>">Skatīt aktualitāšu lapu</a>.
				</p>
			<?php else : ?>
				<div class="jn-latest-posts">
					<?php foreach ( $posts as $post ) : ?>
						<?php jn_post_card( $post, array( 'heading' => 'h3' ) ); ?>
					<?php endforeach; ?>
				</div>

				<div class="jn-all-wrap">
					<a class="jn-all-link" href="<?php echo esc_url( $news_url ); ?>">
						Skatīt visas aktualitātes <span aria-hidden="true">→</span>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
