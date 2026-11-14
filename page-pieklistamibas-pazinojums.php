<?php
$info = jn_site_info();

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

	<section class="page-header" aria-labelledby="page-title">
		<div class="container">
			<?php
			jn_breadcrumb_render(
				array(
					array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
					array( 'name' => 'Piekļūstamības paziņojums', 'url' => '' ),
				)
			);
			?>
			<h1 id="page-title" class="page-header__title">Piekļūstamības paziņojums</h1>
			<p class="page-header__lead">Informācija par šīs vietnes piekļūstamību un tās uzlabošanu.</p>
		</div>
	</section>

	<section class="section">
		<div class="container entry-content">

			<h2>Saistības</h2>
			<p>
				<?php echo esc_html( $info['name'] ); ?> cenšas nodrošināt, lai šī vietne būtu piekļūstama ikvienam lietotājam,
				neatkarīgi no izmantotās tehnoloģijas vai iespējām. Mēs orientējamies uz Pasaules tīmekļa konsorcija
				izstrādātajām <strong>WCAG 2.1 AA līmeņa</strong> vadlīnijām.
			</p>

			<h2>Ko mēs darām</h2>
			<ul>
				<li>Nodrošinām semantisku HTML struktūru un loģisku virsrakstu hierarhiju.</li>
				<li>Nodrošinām pilnu lietošanu ar klaviatūru un redzamu fokusu.</li>
				<li>Pievienojam attēliem alternatīvos tekstus un dekoratīvajiem elementiem — tukšus <code>alt</code> atribūtus.</li>
				<li>Izmantojam pietiekamu krāsu kontrastu un saprotamu saskarnes valodu.</li>
				<li>Cienām lietotāja sistēmas iestatījumus, piemēram, samazinātas kustības režīmu.</li>
			</ul>

			<h2>Zināmie ierobežojumi</h2>
			<p>
				Daļa satura tiek pārņemta no citas vietnes. Mēs regulāri pārbaudām šo saturu, taču atsevišķi
				trešo pušu ievietotie dokumenti vai video var neatbilst visām piekļūstamības prasībām.
			</p>

			<h2>Sazināties ar mums</h2>
			<p>
				Ja saskaraties ar piekļūstamības problēmu, lūdzu, informējiet mūs:
			</p>
			<ul>
				<li>Tālrunis: <a href="tel:<?php echo esc_attr( $info['phone_href'] ); ?>"><?php echo esc_html( $info['phone'] ); ?></a></li>
				<li>E-pasts: <a href="mailto:<?php echo esc_attr( $info['email'] ); ?>"><?php echo esc_html( $info['email'] ); ?></a></li>
				<li>Adrese: <?php echo esc_html( $info['street'] ); ?>, <?php echo esc_html( $info['city'] ); ?>, <?php echo esc_html( $info['postcode'] ); ?></li>
			</ul>
			<p>
				Mēs centīsimies atbildēt un novērst problēmu pēc iespējas ātrāk.
			</p>

			<p class="entry-meta">Paziņojums pēdējo reizi atjaunināts: <?php echo esc_html( wp_date( 'd.m.Y' ) ); ?>.</p>

		</div>
	</section>

</main>

<?php get_footer(); ?>
