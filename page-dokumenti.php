<?php
$login = jn_teacher_process_login();
$is_auth = $login['auth'];
$error_msg = $login['error'];

$teacher_content = '';
if ( $is_auth ) {
	$page = jn_get_page_by_id( 3892 );
	if ( is_array( $page ) && ! empty( $page['content']['rendered'] ) ) {
		$teacher_content = wp_kses_post( $page['content']['rendered'] );
	}
	if ( '' === $teacher_content ) {
		$teacher_content = jn_teacher_fallback_html();
	}
}

$documents = jn_documents();
$materials = jn_materials();

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

	<section class="page-header" aria-labelledby="page-title">
		<div class="container">
			<?php
			jn_breadcrumb_render(
				array(
					array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
					array( 'name' => 'Resursi', 'url' => '' ),
				)
			);
			?>
			<h1 id="page-title" class="page-header__title">Resursi</h1>
			<p class="page-header__lead">Metodiskie materiāli, dokumenti un informācija izglītības iestādēm.</p>
		</div>
	</section>

	<section class="section">
		<div class="container">

			<div class="resursi-banner">
				<div class="resursi-banner__text">
					<h2>Piedāvājums izglītības iestādēm</h2>
					<p>Ekskursijas, planetārija seansi un interaktīvās nodarbības skolēnu grupām.</p>
				</div>
				<a href="<?php echo esc_url( home_url( '/jaunrades-nams/piedavajums-izglitibas-iestadem/' ) ); ?>" class="btn btn--primary">
					Uzzināt vairāk <span aria-hidden="true">→</span>
				</a>
			</div>

			<details class="resursi-accordion">
				<summary class="resursi-accordion__summary">
					<h2 class="resursi-accordion__title">Metodiskie materiāli</h2>
					<span class="resursi-accordion__icon" aria-hidden="true"></span>
				</summary>
				<div class="resursi-accordion__content">
					<?php foreach ( $materials as $group ) : ?>
						<div class="resursi-group">
							<h3 class="resursi-group__title"><?php echo esc_html( $group['group'] ); ?></h3>
							<div class="resursi-grid">
								<?php foreach ( $group['subgroups'] as $subgroup ) : ?>
									<div class="resursi-card">
										<?php if ( ! empty( $subgroup['image'] ) ) : ?>
											<div class="resursi-card__media">
												<img src="<?php echo esc_url( $subgroup['image'] ); ?>" alt="" loading="lazy" decoding="async">
											</div>
										<?php endif; ?>
										<div class="resursi-card__body">
											<h4 class="resursi-card__title"><?php echo esc_html( $subgroup['name'] ); ?></h4>
											<ul class="resursi-links">
												<?php foreach ( $subgroup['files'] as $file ) : ?>
													<li>
														<a href="<?php echo esc_url( $file['url'] ); ?>" class="resursi-link" target="_blank" rel="noopener">
															<span class="resursi-link__icon" aria-hidden="true">📄</span>
															<span class="resursi-link__text"><?php echo esc_html( $file['title'] ); ?></span>
														</a>
													</li>
												<?php endforeach; ?>
											</ul>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</details>

			<details class="resursi-accordion">
				<summary class="resursi-accordion__summary">
					<h2 class="resursi-accordion__title">Dokumenti</h2>
					<span class="resursi-accordion__icon" aria-hidden="true"></span>
				</summary>
				<div class="resursi-accordion__content">
					<ul class="doc-list">
						<?php foreach ( $documents as $document ) : ?>
							<li>
								<a href="<?php echo esc_url( $document['url'] ); ?>" class="teacher-btn" target="_blank" rel="noopener">
									📄 <?php echo esc_html( $document['title'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</details>

			<details class="resursi-accordion" id="pedagogiem" <?php echo isset( $_GET['ok'] ) ? 'open' : ''; ?>>
				<summary class="resursi-accordion__summary">
					<h2 class="resursi-accordion__title">
						Informācija pedagogiem
						<?php if ( $is_auth ) : ?>
							<span class="resursi-accordion__badge">Piekļuve atvērta</span>
						<?php else : ?>
							<span class="resursi-accordion__badge resursi-accordion__badge--locked">🔒 Nepieciešama parole</span>
						<?php endif; ?>
					</h2>
					<span class="resursi-accordion__icon" aria-hidden="true"></span>
				</summary>
				<div class="resursi-accordion__content">
					<?php if ( ! $is_auth ) : ?>
						<div class="teacher-login">
							<div class="teacher-login__icon" aria-hidden="true">🔒</div>
							<h3 class="teacher-login__title">Ierobežota piekļuve</h3>
							<p class="teacher-login__desc">Lūdzu, ievadiet paroli, lai apskatītu informāciju pedagogiem.</p>

							<?php if ( $error_msg ) : ?>
								<div class="teacher-login__error" role="alert"><?php echo esc_html( $error_msg ); ?></div>
							<?php endif; ?>

							<form method="post" action="<?php echo esc_url( get_permalink() . '#pedagogiem' ); ?>" class="teacher-login__form">
								<?php wp_nonce_field( 'jn_teacher_login', 'jn_teacher_nonce' ); ?>
								<input type="hidden" name="jn_login" value="1">
								<label for="jn_password" class="screen-reader-text">Parole</label>
								<input type="password" id="jn_password" name="jn_password" placeholder="Parole" class="teacher-login__input" required autocomplete="current-password">
								<button type="submit" class="btn btn--primary">Piekļūt</button>
							</form>
						</div>
					<?php else : ?>
						<div class="teacher-info-wrap">
							<p class="teacher-info-wrap__logout">
								<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'logout', '1', get_permalink() ), 'jn_teacher_logout', '_jn_nonce' ) ); ?>">← Iziet no pedagoga sadaļas</a>
							</p>
							<?php echo $teacher_content; ?>
						</div>
					<?php endif; ?>
				</div>
			</details>

		</div>
	</section>

</main>

<?php get_footer(); ?>
