<?php
$request_uri    = $_SERVER['REQUEST_URI'] ?? '';
$is_planetarium = ( strpos( $request_uri, '/planetarijs/' ) !== false || is_page( 'planetarijs' ) || !empty( $GLOBALS['is_planetarium_post'] ) );

$home_link  = $is_planetarium ? home_url( '/planetarijs/' ) : home_url( '/' );
$site_logo  = $is_planetarium
    ? get_template_directory_uri() . '/assets/planetarijs-logo.png'
    : get_template_directory_uri() . '/assets/logo.png';
$site_name  = $is_planetarium ? 'Ventspils Planetārijs' : 'Ventspils Jaunrades nams';
$tagline    = $is_planetarium
    ? 'Baltijā modernākais digitālais planetārijs un observatorija Ventspilī.'
    : 'Vieta, kur bērni un jaunieši atklāj sevi kopš 1955. gada — mākslā, zinātnē, tehnoloģijās un sportā.';

$footer_links = $is_planetarium
    ? array(
        array( '/planetarijs/',                   'Sākums' ),
        array( '/planetarijs/aktualitates/',      'Aktualitātes' ),
        array( '/planetarijs/repertuars/',        'Repertuārs' ),
        array( '/planetarijs/seansu-laiki/',      'Seansu laiki' ),
        array( '/planetarijs/cenas/',             'Cenas' ),
        array( '/planetarijs/noteikumi/',         'Noteikumi' ),
    )
    : array(
        array( '/jaunrades-nams/pulcini/',        'Nodarbības' ),
        array( '/jaunrades-nams/aktualitates/',   'Aktualitātes' ),
        array( '/jaunrades-nams/galerija/',       'Galerija' ),
        array( '/jaunrades-nams/dokumenti/',      'Resursi' ),
        array( '/jaunrades-nams/par-mums/',       'Par mums' ),
        array( '/jaunrades-nams/kontakti/',       'Kontakti' ),
    );

$switch_url   = $is_planetarium ? home_url( '/' ) : home_url( '/planetarijs/' );
$switch_label = $is_planetarium ? 'Uz Jaunrades namu' : 'Uz Planetāriju';
?>
<footer class="site-footer">

    <div class="footer-main">
        <div class="footer-inner">

            <div class="footer-top">

                <div class="footer-brand">
                    <a class="footer-brand__logo" href="<?php echo esc_url( $home_link ); ?>" aria-label="<?php echo esc_attr( $site_name ); ?>">
                        <img src="<?php echo esc_url( $site_logo ); ?>" alt="<?php echo esc_attr( $site_name ); ?>" loading="lazy">
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
                        <a class="footer-phone" href="tel:+37163622805">+371 636 22805</a>
                        <a href="mailto:jaunrades.nams@ventspils.lv">jaunrades.nams@ventspils.lv</a>
                        <a href="mailto:planetarijs.observatorija@ventspils.lv">planetarijs.observatorija@ventspils.lv</a>
                    </div>
                </div>

                <div class="footer-column">
                    <h2 id="footer-address">Atrodi mūs</h2>
                    <address>
                        Maiznieku iela 11,<br>
                        Ventspils, LV-3601
                    </address>
                </div>

            </div>

            <div class="footer-partners">
                <span class="footer-partners__label">Atbalsta</span>
                <div class="footer-partners__logos">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/ventspils.png' ); ?>"
                         alt="Ventspils" loading="lazy" width="150" height="42">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/europe.png' ); ?>"
                         alt="Eiropas Savienība" loading="lazy" width="150" height="42">
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
            </ul>

        </div>
    </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
