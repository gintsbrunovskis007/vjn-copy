<?php
$info  = jn_site_info();
$staff = jn_staff();

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

	<section class="page-header" aria-labelledby="page-title">
		<div class="container">
			<?php
			jn_breadcrumb_render(
				array(
					array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
					array( 'name' => 'Kontakti', 'url' => '' ),
				)
			);
			?>
			<h1 id="page-title" class="page-header__title">Kontakti</h1>
			<p class="page-header__lead">Sazinies ar mums – mēs labprāt palīdzēsim.</p>
		</div>
	</section>

	<section class="section">
		<div class="container">

			<div class="contact-grid">

				<article class="contact-card">
					<h2 class="contact-card__title">Adrese</h2>
					<address class="contact-card__address">
						<?php echo esc_html( $info['street'] ); ?>,<br>
						<?php echo esc_html( $info['city'] ); ?>, <?php echo esc_html( $info['postcode'] ); ?>
					</address>
					<p class="contact-card__note">
						<a href="<?php echo esc_url( $info['map_url'] ); ?>" target="_blank" rel="noopener">Atvērt kartē →</a>
					</p>
				</article>

				<article class="contact-card">
					<h2 class="contact-card__title">Saziņai</h2>
					<ul class="contact-list">
						<li>
							<span class="contact-list__label">Tālrunis / Fakss</span>
							<a href="tel:<?php echo esc_attr( $info['phone_href'] ); ?>"><?php echo esc_html( $info['phone'] ); ?></a>
						</li>
						<li>
							<span class="contact-list__label">E-pasts</span>
							<a href="mailto:<?php echo esc_attr( $info['email'] ); ?>"><?php echo esc_html( $info['email'] ); ?></a>
							<a href="mailto:<?php echo esc_attr( $info['planetarium_email'] ); ?>"><?php echo esc_html( $info['planetarium_email'] ); ?></a>
						</li>
						<li>
							<span class="contact-list__label">E-rēķiniem</span>
							<a href="mailto:<?php echo esc_attr( $info['invoice_email'] ); ?>"><?php echo esc_html( $info['invoice_email'] ); ?></a>
						</li>
					</ul>
				</article>

				<article class="contact-card">
					<h2 class="contact-card__title">Darba laiks</h2>
					<ul class="contact-hours">
						<li>
							<span>Pirmdiena – Piektdiena</span>
							<strong>09.00 – 21.00</strong>
						</li>
						<li>
							<span>Sestdiena</span>
							<strong>09.00 – 18.00</strong>
						</li>
						<li>
							<span>Svētdiena</span>
							<strong>09.00 – 18.00</strong>
						</li>
					</ul>
					<p class="contact-card__note">
						Darba laiks var mainīties svētku dienās.
					</p>
				</article>

			</div>

			<article class="contact-block">
				<h2 class="contact-block__title">Rekvizīti norēķiniem</h2>
				<dl class="contact-dl">
					<dt>Nosaukums</dt>
					<dd>Ventspils valstspilsētas pašvaldības iestāde Ventspils pilsētas Izglītības pārvalde</dd>

					<dt>Reģ. Nr.</dt>
					<dd>90000052497</dd>

					<dt>Juridiskā adrese</dt>
					<dd>Raiņa iela 10, Ventspils, LV-3601</dd>

					<dt>Banka</dt>
					<dd>Luminor Bank AS</dd>

					<dt>Bankas kods</dt>
					<dd>RIKOLV2XXXX</dd>

					<dt>Konts</dt>
					<dd>LV30RIKO0000083619094</dd>

					<dt>Saņēmējs</dt>
					<dd>Ventspils Jaunrades nams, Maiznieku iela 11, Ventspils, LV-3601</dd>
				</dl>
			</article>

			<div class="staff-section">
				<h2 class="contact-block__title text-center">Darbinieki</h2>

				<div class="staff-grid">
					<?php foreach ( $staff as $person ) : ?>
						<article class="staff-card">
							<p class="staff-card__role"><?php echo esc_html( $person['role'] ); ?></p>
							<h3 class="staff-card__name"><?php echo esc_html( $person['name'] ); ?></h3>
							<ul class="staff-card__contacts">
								<li>
									<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $person['phone'] ) ); ?>">
										<?php echo esc_html( $person['phone'] ); ?>
									</a>
								</li>
								<li>
									<a href="mailto:<?php echo esc_attr( $person['email'] ); ?>">
										<?php echo esc_html( $person['email'] ); ?>
									</a>
								</li>
							</ul>
						</article>
					<?php endforeach; ?>
				</div>
			</div>

		</div>
	</section>

</main>

<?php get_footer(); ?>
