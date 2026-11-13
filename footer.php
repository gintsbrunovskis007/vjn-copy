<?php
$social_links = array(
    'Facebook' => '',
    'YouTube'  => '',
);
?>
<footer class="site-footer">

    <div class="footer-main">
        <div class="footer-inner">

            <div class="footer-grid">

                <section class="footer-column footer-column--contact" aria-labelledby="footer-contact">
                    <h2 id="footer-contact">Sazinieties ar mums</h2>
                    <a class="footer-phone" href="tel:+37163622805">+371 636 22805</a>
                    <a href="mailto:jaunrades.nams@ventspils.lv">jaunrades.nams@ventspils.lv</a>
                    <a href="mailto:planetarijs.observatorija@ventspils.lv">planetarijs.observatorija@ventspils.lv</a>
                </section>

                <section class="footer-column" aria-labelledby="footer-address">
                    <h2 id="footer-address">Mūsu adrese</h2>
                    <address>
                        Maiznieku iela 11,<br>
                        Ventspils, LV-3601
                    </address>
                </section>

            </div>

            <div class="footer-partners">
                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/ventspils.png' ); ?>"
                     alt="Ventspils" loading="lazy" width="180" height="56">
                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/europe.png' ); ?>"
                     alt="Eiropas Savienība" loading="lazy" width="180" height="56">
            </div>

        </div>
    </div>

    <div class="footer-bottom">
        <div class="footer-bottom-container">

            <p>© <?php echo esc_html( wp_date( 'Y' ) ); ?> Ventspils Jaunrades nams</p>

            <ul class="footer-legal">
                <li><a href="<?php echo esc_url( home_url( '/pieklistamibas-pazinojums/' ) ); ?>">Piekļūstamības paziņojums</a></li>
                <?php foreach ( $social_links as $name => $url ) : if ( ! $url ) continue; ?>
                    <li>
                        <a href="<?php echo esc_url( $url ); ?>" rel="noopener" target="_blank">
                            <?php echo esc_html( $name ); ?>
                            <span class="screen-reader-text">(atveras jaunā cilnē)</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

        </div>
    </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>