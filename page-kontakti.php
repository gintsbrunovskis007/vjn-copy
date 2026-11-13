<?php
/**
 * Template Name: Kontakti
 */

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

    <section class="page-header" aria-labelledby="page-title">
        <div class="container">
            <nav class="breadcrumb" aria-label="Atrašanās vieta">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Kontakti</span>
            </nav>
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
                        Maiznieku iela 11,<br>
                        Ventspilī, LV-3601
                    </address>
                </article>

                <article class="contact-card">
                    <h2 class="contact-card__title">Saziņai</h2>
                    <ul class="contact-list">
                        <li>
                            <span class="contact-list__label">Tālrunis / Fakss</span>
                            <a href="tel:+37163622805">+371 636 22805</a>
                        </li>
                        <li>
                            <span class="contact-list__label">E-pasts</span>
                            <a href="mailto:jaunrades.nams@ventspils.lv">jaunrades.nams@ventspils.lv</a>
                            <a href="mailto:planetarijs.observatorija@ventspils.lv">planetarijs.observatorija@ventspils.lv</a>
                        </li>
                        <li>
                            <span class="contact-list__label">E-rēķiniem</span>
                            <a href="mailto:EINVOICE@40900005880">EINVOICE@40900005880</a>
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
                        Darba laiks spēkā no 2025. gada 1. septembra līdz 2026. gada 31. maijam.
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

                    <?php
                    $staff = array(
                        array(
                            'role'  => 'Direktore',
                            'name'  => 'Vita Hermansone',
                            'phone' => '+371 636 22805',
                            'email' => 'vita.hermansone@ventspils.lv',
                        ),
                        array(
                            'role'  => 'Direktores vietniece interešu izglītības jomā',
                            'name'  => 'Ilona Klāviņa',
                            'phone' => '+371 636 22805',
                            'email' => 'ilona.klavina@ventspils.lv',
                        ),
                        array(
                            'role'  => 'Direktores vietniece jaunrades jomā',
                            'name'  => 'Ineta Kalniņa',
                            'phone' => '+371 636 22805',
                            'email' => 'ineta.kalnina@ventspils.lv',
                        ),
                        array(
                            'role'  => 'Direktores vietniece jaunrades jomā',
                            'name'  => 'Sandra Gūtmane',
                            'phone' => '+371 636 22805',
                            'email' => 'sandra.gutmane@ventspils.lv',
                        ),
                        array(
                            'role'  => 'Saimniecības vadītāja',
                            'name'  => 'Aiva Vilcāne',
                            'phone' => '+371 636 22805',
                            'email' => 'aiva.vilcane@ventspils.lv',
                        ),
                        array(
                            'role'  => 'Lietvedības sekretāre',
                            'name'  => 'Laura Baķe',
                            'phone' => '+371 636 22805',
                            'email' => 'laura.bake@ventspils.lv',
                        ),
                    );

                    foreach ( $staff as $person ) :
                        ?>
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