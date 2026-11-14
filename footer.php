<?php
$is_planetarium = jn_is_planetarium();
$info           = jn_site_info();

$home_link  = $is_planetarium ? home_url( '/planetarijs/' ) : home_url( '/' );
$site_logo  = $is_planetarium ? jn_asset( 'img/planetarijs-logo.png' ) : jn_asset( 'logo.png' );
$logo_width = $is_planetarium ? 320 : 207;
$logo_height = $is_planetarium ? 187 : 121;
$site_name  = $is_planetarium ? $info['planetarium_name'] : $info['name'];
$tagline    = $is_planetarium
	? 'Baltijā modernākais digitālais planetārijs un observatorija Ventspilī.'
	: 'Vieta, kur bērni un jaunieši atklāj sevi kopš 1955. gada — mākslā, zinātnē, tehnoloģijās un sportā.';

$footer_links = jn_footer_links();
$switch_url   = jn_switch_url();
$switch_label = $is_planetarium ? 'Uz Jaunrades namu' : 'Uz Planetāriju';
?>
<footer class="site-footer">

	<div class="footer-main">
		<div class="footer-inner">

			<div class="footer-top">

				<div class="footer-brand">
					<a class="footer-brand__logo" href="<?php echo esc_url( $home_link ); ?>" aria-label="<?php echo esc_attr( $site_name ); ?>">
						<img src="<?php echo esc_url( $site_logo ); ?>"
							alt="<?php echo esc_attr( $site_name ); ?>"
							width="<?php echo (int) $logo_width; ?>"
							height="<?php echo (int) $logo_height; ?>"
							loading="lazy"
							decoding="async">
					</a>
					<p class="footer-brand__tagline"><?php echo esc_html( $tagline ); ?></p>
				</div>

				<nav class="footer-nav" aria-labelledby="footer-nav-title">
					<h2 id="footer-nav-title">Ātrās saites</h2>
					<ul>
						<?php foreach ( $footer_links as $link ) : ?>
							<li><a href="<?php echo esc_url( home_url( $link[0] ) ); ?>"><?php echo esc_html( $link[1] ); ?></a></li>
						<?php endforeach; ?>
						<li><a href="<?php echo esc_url( $switch_url ); ?>"><?php echo esc_html( $switch_label ); ?></a></li>
					</ul>
				</nav>

				<div class="footer-column">
					<h2 id="footer-contact">Sazināties</h2>
					<div class="footer-contact-list">
						<a class="footer-phone" href="tel:<?php echo esc_attr( $info['phone_href'] ); ?>"><?php echo esc_html( $info['phone'] ); ?></a>
						<a href="mailto:<?php echo esc_attr( $info['email'] ); ?>"><?php echo esc_html( $info['email'] ); ?></a>
						<a href="mailto:<?php echo esc_attr( $info['planetarium_email'] ); ?>"><?php echo esc_html( $info['planetarium_email'] ); ?></a>
					</div>
				</div>

				<div class="footer-column">
					<h2 id="footer-address">Atrodi mūs</h2>
					<address>
						<?php echo esc_html( $info['street'] ); ?>,<br>
						<?php echo esc_html( $info['city'] ); ?>, <?php echo esc_html( $info['postcode'] ); ?>
					</address>
				</div>

			</div>

			<div class="footer-partners">
				<span class="footer-partners__label">Atbalsta</span>
				<div class="footer-partners__logos">
					<img src="<?php echo esc_url( jn_asset( 'img/ventspils.png' ) ); ?>"
						alt="Ventspils" loading="lazy" decoding="async" width="240" height="145">
					<img src="<?php echo esc_url( jn_asset( 'img/europe.webp' ) ); ?>"
						alt="Eiropas Savienība" loading="lazy" decoding="async" width="960" height="640">
				</div>
			</div>

		</div>
	</div>

	<div class="footer-bottom">
		<div class="footer-bottom-container">

			<p>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( $site_name ); ?></p>

			<a class="footer-top-link" href="#main">Uz augšu <span aria-hidden="true">↑</span></a>

			<ul class="footer-legal">
				<li><a href="<?php echo esc_url( home_url( '/pieklistamibas-pazinojums/' ) ); ?>">Piekļūstamības paziņojums</a></li>
				<li><a href="<?php echo esc_url( home_url( '/jaunrades-nams/dokumenti/' ) ); ?>">Dokumenti</a></li>
			</ul>

		</div>
	</div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
