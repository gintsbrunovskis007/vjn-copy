<?php
/**
 * Template Name: Resursi
 */




if ( ! defined( 'JN_TEACHER_PASSWORD' ) ) {
    define( 'JN_TEACHER_PASSWORD', 'venta' );
}
if ( ! defined( 'JN_TEACHER_TTL' ) ) {
    define( 'JN_TEACHER_TTL', 8 * HOUR_IN_SECONDS );
}




function jn_teacher_cookie_name(): string {
    return 'jn_teacher_auth';
}

function jn_teacher_sign( int $expires ): string {
    return $expires . '.' . hash_hmac( 'sha256', 'jn_teacher_auth|' . $expires, wp_salt( 'auth' ) );
}

function jn_teacher_verify(): bool {
    if ( empty( $_COOKIE[ jn_teacher_cookie_name() ] ) ) return false;
    $raw = (string) $_COOKIE[ jn_teacher_cookie_name() ];
    if ( ! preg_match( '/^(\d{10})\.([a-f0-9]{64})$/', $raw, $m ) ) return false;
    if ( (int) $m[1] < time() ) return false;
    $expected = hash_hmac( 'sha256', 'jn_teacher_auth|' . $m[1], wp_salt( 'auth' ) );
    return hash_equals( $expected, $m[2] );
}

function jn_teacher_grant(): void {
    $expires = time() + JN_TEACHER_TTL;
    $sig     = jn_teacher_sign( $expires );
    setcookie(
        jn_teacher_cookie_name(),
        $sig,
        array(
            'expires'  => $expires,
            'path'     => COOKIEPATH ?: '/',
            'domain'   => COOKIE_DOMAIN,
            'secure'   => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax',
        )
    );
    $_COOKIE[ jn_teacher_cookie_name() ] = $sig;
}

function jn_teacher_revoke(): void {
    setcookie( jn_teacher_cookie_name(), '', time() - 3600, COOKIEPATH ?: '/', COOKIE_DOMAIN );
    unset( $_COOKIE[ jn_teacher_cookie_name() ] );
}

function jn_teacher_rate_key(): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    return 'jn_teacher_rl_' . md5( $ip );
}




$is_auth   = jn_teacher_verify();
$error_msg = '';

if ( isset( $_POST['jn_login'] ) ) {
    check_admin_referer( 'jn_teacher_login', 'jn_teacher_nonce' );

    $attempts = (int) get_transient( jn_teacher_rate_key() );
    if ( $attempts >= 5 ) {
        $error_msg = 'Pārāk daudz mēģinājumu. Lūdzu, uzgaidiet 15 minūtes.';
    } elseif ( hash_equals( JN_TEACHER_PASSWORD, (string) ( $_POST['jn_password'] ?? '' ) ) ) {
        delete_transient( jn_teacher_rate_key() );
        jn_teacher_grant();
        wp_safe_redirect( add_query_arg( 'ok', '1', get_permalink() ) . '#pedagogiem' );
        exit;
    } else {
        set_transient( jn_teacher_rate_key(), $attempts + 1, 15 * MINUTE_IN_SECONDS );
        $error_msg = 'Nepareiza parole. Lūdzu, mēģiniet vēlreiz.';
    }
    $is_auth = jn_teacher_verify();
}

if ( isset( $_GET['logout'] ) ) {
    jn_teacher_revoke();
    wp_safe_redirect( get_permalink() . '#pedagogiem' );
    exit;
}




$teacher_content = '';
if ( $is_auth ) {
    $page = jn_api_cache(
        'page_3892_content',
        'pages/3892',
        array( '_fields' => 'content' ),
        15 * MINUTE_IN_SECONDS
    );
    if ( is_array( $page ) && ! empty( $page['content']['rendered'] ) ) {
        $teacher_content = $page['content']['rendered'];
    }

    if ( '' === $teacher_content ) {
        ob_start(); ?>
        <h3><strong>Aktualitātes:</strong></h3>
        <p><strong>Ventspils Jaunrades nama kalendārs mācību gadam</strong> aplūkojams
            <a href="https://docs.google.com/spreadsheets/d/1IjJe04ph9lnV-1Z_7w3X-Rcq1ZtuHrpN37pLJJ60QEY/edit#gid=15019426" target="_blank" rel="noopener">šeit</a>
            vai ierakstot adresi <strong>ej.uz/VJNkalendars</strong></p>
        <h3><strong>Faili skolotājiem:</strong></h3>
        <a href="https://jaunradesnams.lv/wp-content/uploads/darba_kartibas_noteikumi_2026.pdf" class="teacher-btn" target="_blank" rel="noopener">📄 Darba kārtības noteikumi</a>
        <p class="mt-24"><strong>Iesniegums audzēkņu uzņemšanai u.c. dokumenti</strong>
            <a href="<?php echo esc_url( home_url( '/jaunrades-nams/dokumenti/' ) ); ?>">šeit</a></p>
        <h3><strong>Pieprasījums ārpusnodarbību pasākumam:</strong></h3>
        <p><em>Šie abi dokumenti ir saistoši pedagogiem, kuri vēlas rīkot pasākumus Jaunrades namā un arī ārpus tā. Tehniskais pieteikums jāiesniedz ne vēlāk kā mēnesi pirms pasākuma, bet programmas pieteikums – ne vēlāk kā trīs darba dienas pēc pasākuma.</em></p>
        <a href="https://jaunradesnams.lv/wp-content/uploads/programmas_pazinojums_AKKA-LAA.doc" class="teacher-btn" target="_blank" rel="noopener">📄 Programmas paziņojums</a>
        <a href="https://jaunradesnams.lv/wp-content/uploads/tehniskais_pieteikums_JN_kolektiviem.docx" class="teacher-btn" target="_blank" rel="noopener">📄 Pasākuma tehniskais pieteikums</a>
        <h3><strong>Dokumenti komandējumiem:</strong></h3>
        <a href="https://jaunradesnams.lv/wp-content/uploads/komandejuma_pieprasijums_1.docx" class="teacher-btn" target="_blank" rel="noopener">📄 Komandējuma pieprasījums</a>
        <a href="https://jaunradesnams.lv/wp-content/uploads/transporta_pieprasijums_2026_1.docx" class="teacher-btn" target="_blank" rel="noopener">📄 Transporta pieprasījums</a>
        <a href="https://jaunradesnams.lv/wp-content/uploads/komandejuma_parskats_2026_1.doc" class="teacher-btn" target="_blank" rel="noopener">📄 Komandējuma pārskats</a>
        <?php
        $teacher_content = ob_get_clean();
    }
}




$documents = array(
    array( 'title' => 'VENTSPILS JAUNRADES NAMA NOLIKUMS', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/vjn-nolikums_2022.pdf' ),
    array( 'title' => 'NOTEIKUMI AUDZĒKŅIEM PAR DARBA KĀRTĪBU UN DARBA DROŠĪBU NODARBĪBĀS 2026./2027.', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/ieksejas_kartibas_noteikumi_2026.pdf' ),
    array( 'title' => 'IESNIEGUMS UZŅEMT INTEREŠU IZGLĪTĪBAS PROGRAMMĀ NEPILNGADĪGAJIEM', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/iesniegums-nepilngadiigajiem-2026_1.docx' ),
    array( 'title' => 'IESNIEGUMS UZŅEMT INTEREŠU IZGLĪTĪBAS PROGRAMMĀ PILNGADĪGAJIEM', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/iesniegums-pilngadiigajiem-2026_2.docx' ),
    array( 'title' => 'Iesniegums – NODARBĪBU APMEKLĒŠANAS PĀRTRAUKŠANA', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/iesn_izstaashanaas-no-pulc-2020.docx' ),
    array( 'title' => 'Privātuma politika', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/Privatuma_politika_VJN_un_VJM_2026.pdf' ),
    array( 'title' => 'Elektroniskā žurnāla lietošanas kārtība', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/elektroniska_zurnala_lietosanas_kartiba.pdf' ),
    array( 'title' => 'Kārtība, kādā iestādē uzturas nepiederošas personas', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/kartiba_ka_iestade_uzturas_nepiederosas_personas.pdf' ),
    array( 'title' => 'Kārtība, kā risina domstarpības starp pedagogu un izglītojamā ģimeni', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/kartiba_ka_risina_domstarpibas.pdf' ),
    array( 'title' => 'Rīcības kārtība Ventspils Jaunrades namā gaisa telpas apdraudējuma gadījumā', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/VJN_ricibas_kartiba_gaisa_telpas_apdraudejuma_gadijuma.pdf' ),
);

$materials = array(
    array(
        'group' => 'Kultūrizglītības programmas',
        'subgroups' => array(
            array(
                'name' => 'Mūzika',
                'image' => 'https://jaunradesnams.lv/wp-content/uploads/kids-many-hands-on-piano-150x150.jpg',
                'files' => array(
                    array( 'title' => 'Ģitārspēles studija', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_GITARSPELES_STUDIJA.docx' ),
                    array( 'title' => 'Skaņskatu mākslas studija – Vokāli instrumentālais ansamblis', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_SKANSKATU_MAKSLAS_STUDIJA-VOKALI_INSTRUMENTALAIS_ANSAMBLIS.docx' ),
                    array( 'title' => 'Solo dziedāšanas mācību klase', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_SOLODZIEDASANAS_MACIBU_KLASE.docx' ),
                    array( 'title' => 'Vokālā studija “Ķipari un Pipari”', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_VOKALA_STUDIJA_KIPARI_UN_PIPARI.docx' ),
                ),
            ),
            array(
                'name' => 'Dejas',
                'image' => 'https://jaunradesnams.lv/wp-content/uploads/54-150x150.jpg',
                'files' => array(
                    array( 'title' => 'Mūsdienu deju studija “Skorpions”', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_Musdienu_deju_studija_SKORPIONS.docx' ),
                    array( 'title' => 'TDK “Vilnītis”', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_TDK_VILNITIS.docx' ),
                ),
            ),
            array(
                'name' => 'Folklora',
                'image' => 'https://jaunradesnams.lv/wp-content/uploads/p1030219-150x150.jpg',
                'files' => array(
                    array( 'title' => 'Folkloras vācelīte', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_FOLKLORAS_VACELITE.docx' ),
                ),
            ),
            array(
                'name' => 'Teātra māksla',
                'image' => 'https://jaunradesnams.lv/wp-content/uploads/teatris_mes_logo-150x145.jpg',
                'files' => array(
                    array( 'title' => 'Teātra sports – improvizācija', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_TEATRA_SPORTS_IMPROVIZACIJA.docx' ),
                ),
            ),
        ),
    ),
    array(
        'group' => 'Tehnoloģiju joma',
        'subgroups' => array(
            array(
                'name' => 'Tehniskā jaunrade',
                'image' => 'https://jaunradesnams.lv/wp-content/uploads/Koka-auto-2016_Kurzemes-novads-1-400x265.jpg',
                'files' => array(
                    array( 'title' => 'Trases automodelisms', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_Trases_automodelisms.docx' ),
                    array( 'title' => 'Tehniskā modelēšana', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_TEHNISKA_MODELESANA.docx' ),
                    array( 'title' => 'Radošā kokapstrāde', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_radosa_kokapstrade.docx' ),
                    array( 'title' => 'Radioelektronikas laboratorija', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_radioelektronikas_laboratorija.docx' ),
                ),
            ),
            array(
                'name' => 'Vizuāli plastiskā māksla',
                'image' => 'https://jaunradesnams.lv/wp-content/uploads/CAM00620-150x150.jpg',
                'files' => array(
                    array( 'title' => 'Polimērmāla veidošanas studija', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_POLIMERMALA_VEIDOSANAS_STUDIJA_7-18g.v.docx' ),
                    array( 'title' => 'Radošo darbu studija – Mīļlietiņas', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_radoso_darbu_studija_MILLIETINAS.docx' ),
                    array( 'title' => 'Veidošanas studija (4-6. g.v.)', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_VEIDOSANAS_STUDIJA_4-6-g.v.docx' ),
                    array( 'title' => 'Arhitektūra', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_arhitektura.docx' ),
                ),
            ),
            array(
                'name' => 'Zinātne, Dabaszinātne',
                'image' => 'https://jaunradesnams.lv/wp-content/uploads/Lego-Mindstorms-robot-009-150x150.jpg',
                'files' => array(
                    array( 'title' => 'Mazo gudrinieku laboratorija', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_mazo_gudrinieku_laboratorija.docx' ),
                    array( 'title' => 'Interaktīvā nodarbība – Mēness', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_interaktiva_nodarbiba_meness.docx' ),
                ),
            ),
        ),
    ),
    array(
        'group' => 'Sporta joma',
        'subgroups' => array(
            array(
                'name' => 'Sports un fiziskās aktivitātes',
                'image' => 'https://jaunradesnams.lv/wp-content/uploads/hockey-150x150.jpg',
                'files' => array(
                    array( 'title' => 'Fitness (4-6 g.v.)', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_FITNESS_4-6g.v.docx' ),
                    array( 'title' => 'Fitness', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_FITNESS.docx' ),
                    array( 'title' => 'Galda hokejs', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_GALDA_HOKEJS.docx' ),
                ),
            ),
        ),
    ),
    array(
        'group' => 'Citas mācību jomas',
        'subgroups' => array(
            array(
                'name' => 'Valodas, uzņēmējdarbība un citi',
                'image' => 'https://jaunradesnams.lv/wp-content/uploads/greenhands-200x240.png',
                'files' => array(
                    array( 'title' => 'Angļu valoda pamatskolas skolēniem – ēdieni, dzērieni', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_anglu_valoda_pamatskolas_skoleniem_edieni_dzerieni.docx' ),
                    array( 'title' => 'Angļu valoda pamatskolas skolēniem – dzīvnieki', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_ANGLU_VALODA_PAMATSKOLAS_SKOLENIEM.docx' ),
                    array( 'title' => 'Jaunais uzņēmējs', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_JAUNAIS_UZNEMEJS.docx' ),
                    array( 'title' => 'Labo garšu studija', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_LABO_GARSU_STUDIJA.docx' ),
                    array( 'title' => 'Palīgs angļu valodā', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_paligs_anglu_valoda.docx' ),
                    array( 'title' => 'Pirmie jautrie soļi angļu valodā', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_pirmie_jautrie_soli_anglu_valoda.docx' ),
                    array( 'title' => 'Radošās darbnīcas bērniem un jauniešiem ar funkcionāliem traucejumiem', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_Radosas_darbnicas_berniem_un_jauniesiem_ar_funkcionaliem_traucejumiem.docx' ),
                    array( 'title' => 'Mūzikas ansamblis bērniem un jauniešiem ar funkcionāliem traucējumiem “Cimdiņš”', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_muzikas_ansamblis_cimdins.docx' ),
                    array( 'title' => 'Keramikas radošā darbnīca bērniem un jauniešiem ar funkcionāliem traucējumiem', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_KERAMIKA.docx' ),
                    array( 'title' => 'Rīta skoliņa', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_RITA-SKOLINA.docx' ),
                ),
            ),
        ),
    ),
);

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

    <section class="page-header" aria-labelledby="page-title">
        <div class="container">
            <nav class="breadcrumb" aria-label="Atrašanās vieta">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Sākums</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Resursi</span>
            </nav>
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
                                <?php foreach ( $group['subgroups'] as $sg ) : ?>
                                    <div class="resursi-card">
                                        <?php if ( ! empty( $sg['image'] ) ) : ?>
                                            <div class="resursi-card__media">
                                                <img src="<?php echo esc_url( $sg['image'] ); ?>" alt="" loading="lazy" referrerpolicy="no-referrer">
                                            </div>
                                        <?php endif; ?>
                                        <div class="resursi-card__body">
                                            <h4 class="resursi-card__title"><?php echo esc_html( $sg['name'] ); ?></h4>
                                            <ul class="resursi-links">
                                                <?php foreach ( $sg['files'] as $file ) : ?>
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
                        <?php foreach ( $documents as $doc ) : ?>
                            <li>
                                <a href="<?php echo esc_url( $doc['url'] ); ?>" class="teacher-btn" target="_blank" rel="noopener">
                                    📄 <?php echo esc_html( $doc['title'] ); ?>
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
                                <a href="<?php echo esc_url( add_query_arg( 'logout', '1', get_permalink() ) ); ?>">← Iziet no pedagoga sadaļas</a>
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