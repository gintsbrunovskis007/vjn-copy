<?php
/**
 * Template Name: Par mums
 */

$hero_img = jn_asset( 'img/hero-1200.jpg' );

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

    <section class="about-hero" aria-labelledby="page-title"
             style="--hero-image: url('<?php echo esc_url( $hero_img ); ?>');">
        <div class="container">
            <?php
            jn_breadcrumb_render(
                array(
                    array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
                    array( 'name' => 'Par mums', 'url' => '' ),
                ),
                'breadcrumb breadcrumb--light'
            );
            ?>
            <h1 id="page-title" class="about-hero__title">Par mums</h1>
            <p class="about-hero__lead">
                Ventspils Jaunrades nams — vieta, kur bērni un jaunieši atklāj sevi jau kopš 1955. gada.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="container about-narrow">

            <p class="about-lead">
                Ventspils Jaunrades nama, kā interešu izglītības iestādes, vēsture ir interesanta un daudzveidīga.
                Mēs esam vieta, kur bērni un jaunieši no 3 līdz 25 gadu vecumam atrod savu aicinājumu —
                mākslā, mūzikā, zinātnē, tehnoloģijās un sportā.
            </p>

            <div class="about-stats">
                <div class="about-stat">
                    <span class="about-stat__num">1955</span>
                    <span class="about-stat__label">Dibināšanas gads</span>
                </div>
                <div class="about-stat">
                    <span class="about-stat__num">55+</span>
                    <span class="about-stat__label">Pulciņi</span>
                </div>
                <div class="about-stat">
                    <span class="about-stat__num">3–25</span>
                    <span class="about-stat__label">Audzēkņu vecums</span>
                </div>
                <div class="about-stat">
                    <span class="about-stat__num">2010</span>
                    <span class="about-stat__label">Jaunās telpās</span>
                </div>
            </div>

        </div>
    </section>

    <section class="about-section">
        <div class="container about-narrow">

            <h2 class="about-heading">Vēsture</h2>

            <p>
                1955. gada rudenī māja Lielā ielā 7, kas būvēta pagājušā gadsimta 30. gados, tika atdota bērniem,
                un savu darbību tajā uzsāka <strong>Pionieru nams</strong>. Sākotnēji namā darbojās Amatnieku biedrība,
                kara laikā — hospitālis, bet pēc tam neilgu laiku tā bijusi mājvieta partijas komitejai.
                Laika gaitā ēku pārbūvēja, uzceļot trešo stāvu un pārveidojot iekštelpas atbilstoši laikmeta prasībām.
            </p>

            <p>
                Pirmās direktores <strong>Valentīnas Krisovas</strong> vadībā iestādē strādāja 5 pedagogi un
                pirmie pulciņi bija <em>baleta, šūšanas, foto un tehniskās modelēšanas</em> pulciņš.
                55 gadus interešu izglītības iestāde atradās Lielā ielā 7.
            </p>

            <p>
                Mainoties paaudzēm un ideoloģijai, 1990. gadā Pionieru nams pārtapa par
                <strong>Skolēnu jaunrades namu</strong>. Šajā laikā iestādi vadījuši 9 direktori,
                no kuriem visilgāk — 31 gadu — namiņa darbu vadīja <strong>Rasma Jakoviča</strong>.
                2020. gada maijā direktores amatā stājās <strong>Vita Hermansone</strong>.
            </p>

        </div>
    </section>

    <section class="about-section about-section--alt">
        <div class="container about-narrow">

            <h2 class="about-heading">Ēka Maiznieku ielā 11</h2>

            <p>
                Kopš 2010. gada iestāde atrodas jaunās telpās — <strong>Maiznieku ielā 11</strong>.
                Arhitekta E. Baula projektētās ēkas pamatakmens tika ielikts 1911. gada 10. novembrī,
                un pēc gada, 1912. gada 10. un 11. novembrī, notika celtnes iesvētīšanas svētki.
            </p>

            <p>
                Ēka tika celta īpaši vietējās <strong>Latviešu biedrības</strong> vajadzībām un ilgu laiku bijusi
                pilsētas kultūras un sabiedriskās dzīves centrs. Padomju gados šeit atradās okupācijas armijas
                virsnieku nams, bet pēc Latvijas neatkarības atjaunošanas reprezentatīvā būve vecpilsētā atkal
                kļuva par Ventspils Latviešu biedrības īpašumu.
            </p>

            <p>
                Spiedīgo finansiālo apstākļu dēļ nespējot ēku kapitāli izremontēt un ar grūtībām to tikai uzturot,
                2007. gadā biedrība izlēma namu <strong>dāvināt pilsētai</strong>, pretī saņemot solījumu
                simtgadīgo celtni kapitāli atjaunot.
            </p>

            <p>
                2010. gadā ar Eiropas, valsts un pašvaldības līdzekļiem ēka tika rekonstruēta, un projekta ietvaros
                tai uzbūvēta piebūve. Ēkas pārbūves projektu no 2008. līdz 2010. gadam izstrādāja arhitektu birojs
                <strong>SIA "Virtu"</strong>.
            </p>

        </div>
    </section>

    <section class="about-section">
        <div class="container about-narrow">

            <h2 class="about-heading">Šodien</h2>

            <p>
                Mainot Skolēnu jaunrades nama atrašanās vietu, adresi un daļēji arī uzdevumus —
                iestāde mainījusi arī savu nosaukumu: tā kļuvusi par <strong>Ventspils Jaunrades namu</strong>,
                jo interešu izglītības iestāde atvērta bērniem un jauniešiem no <strong>3 līdz 25 gadu vecumam</strong>.
            </p>

            <p>
                Šajā mācību gadā bērniem un jauniešiem Ventspils Jaunrades nams piedāvā jau
                <strong>55 dažāda nosaukuma pulciņus</strong>. Turklāt Ventspils Jaunrades nams piedāvā apmeklēt arī
                <strong>Baltijā modernāko un lielāko digitālo planetāriju</strong>, kā arī ar modernu teleskopu
                aprīkotu <strong>observatoriju</strong>.
            </p>

            <div class="about-cta">
                <a class="btn btn--primary" href="<?php echo esc_url( home_url( '/jaunrades-nams/pulcini/' ) ); ?>">Apskati nodarbības</a>
                <a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/jaunrades-nams/kontakti/' ) ); ?>">Sazinies ar mums</a>
            </div>

        </div>
    </section>

</main>

<?php get_footer(); ?>