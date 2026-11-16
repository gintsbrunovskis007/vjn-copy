<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jn_site_info() {
	return array(
		'name'              => 'Ventspils Jaunrades nams',
		'planetarium_name'  => 'Ventspils Planetārijs',
		'phone'             => '+371 636 22805',
		'phone_href'        => '+37163622805',
		'email'             => 'jaunrades.nams@ventspils.lv',
		'planetarium_email' => 'planetarijs.observatorija@ventspils.lv',
		'invoice_email'     => 'EINVOICE@40900005880',
		'street'            => 'Maiznieku iela 11',
		'city'              => 'Ventspils',
		'postcode'          => 'LV-3601',
		'map_url'           => 'https://www.google.com/maps/search/?api=1&query=Maiznieku+iela+11+Ventspils',
	);
}

function jn_hubs() {
	return array(
		'kulturizglitibas-programmas'    => array( 'name' => 'Kultūrizglītība', 'sub' => 'Mūzika, deja, teātris, māksla', 'color' => '#e8711f' ),
		'stem'                           => array( 'name' => 'STEM', 'sub' => 'Inženierzinātnes, dabaszinātnes', 'color' => '#1a5fa8' ),
		'sports-un-fiziskas-aktivitates' => array( 'name' => 'Sports', 'sub' => 'Galda spēles, dejas, fitnesa', 'color' => '#2b7a3d' ),
		'socialas-un-pilsoniskas-jomas'  => array( 'name' => 'Sociālās programmas', 'sub' => 'Valodas, kulturoloģija, uzņēmējdarbība', 'color' => '#8a4a00' ),
	);
}

function jn_schedule_pdf() {
	$pdf_url  = 'https://jaunradesnams.lv/wp-content/uploads/nodarbiibu-saraksts_2026_2027_3.11.2026_1.pdf';
	$date_str = '';
	$filename = basename( (string) wp_parse_url( $pdf_url, PHP_URL_PATH ) );

	if ( preg_match( '/(\d{1,2}\.\d{1,2}\.\d{4})/', $filename, $dm ) ) {
		$date_str = $dm[1];
	}

	return array( 'url' => $pdf_url, 'date' => $date_str );
}

function jn_documents() {
	return array(
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
}

function jn_materials() {
	return array(
		array(
			'group'     => 'Kultūrizglītības programmas',
			'subgroups' => array(
				array(
					'name'  => 'Mūzika',
					'image' => 'https://jaunradesnams.lv/wp-content/uploads/kids-many-hands-on-piano-150x150.jpg',
					'files' => array(
						array( 'title' => 'Ģitārspēles studija', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_GITARSPELES_STUDIJA.docx' ),
						array( 'title' => 'Skaņskatu mākslas studija – Vokāli instrumentālais ansamblis', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_SKANSKATU_MAKSLAS_STUDIJA-VOKALI_INSTRUMENTALAIS_ANSAMBLIS.docx' ),
						array( 'title' => 'Solo dziedāšanas mācību klase', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_SOLODZIEDASANAS_MACIBU_KLASE.docx' ),
						array( 'title' => 'Vokālā studija “Ķipari un Pipari”', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_VOKALA_STUDIJA_KIPARI_UN_PIPARI.docx' ),
					),
				),
				array(
					'name'  => 'Dejas',
					'image' => 'https://jaunradesnams.lv/wp-content/uploads/54-150x150.jpg',
					'files' => array(
						array( 'title' => 'Mūsdienu deju studija “Skorpions”', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_Musdienu_deju_studija_SKORPIONS.docx' ),
						array( 'title' => 'TDK “Vilnītis”', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_TDK_VILNITIS.docx' ),
					),
				),
				array(
					'name'  => 'Folklora',
					'image' => 'https://jaunradesnams.lv/wp-content/uploads/p1030219-150x150.jpg',
					'files' => array(
						array( 'title' => 'Folkloras vācelīte', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_FOLKLORAS_VACELITE.docx' ),
					),
				),
				array(
					'name'  => 'Teātra māksla',
					'image' => 'https://jaunradesnams.lv/wp-content/uploads/teatris_mes_logo-150x145.jpg',
					'files' => array(
						array( 'title' => 'Teātra sports – improvizācija', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_TEATRA_SPORTS_IMPROVIZACIJA.docx' ),
					),
				),
			),
		),
		array(
			'group'     => 'Tehnoloģiju joma',
			'subgroups' => array(
				array(
					'name'  => 'Tehniskā jaunrade',
					'image' => 'https://jaunradesnams.lv/wp-content/uploads/Koka-auto-2016_Kurzemes-novads-1-400x265.jpg',
					'files' => array(
						array( 'title' => 'Trases automodelisms', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_Trases_automodelisms.docx' ),
						array( 'title' => 'Tehniskā modelēšana', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_TEHNISKA_MODELESANA.docx' ),
						array( 'title' => 'Radošā kokapstrāde', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_radosa_kokapstrade.docx' ),
						array( 'title' => 'Radioelektronikas laboratorija', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_radioelektronikas_laboratorija.docx' ),
					),
				),
				array(
					'name'  => 'Vizuāli plastiskā māksla',
					'image' => 'https://jaunradesnams.lv/wp-content/uploads/CAM00620-150x150.jpg',
					'files' => array(
						array( 'title' => 'Polimērmāla veidošanas studija', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_POLIMERMALA_VEIDOSANAS_STUDIJA_7-18g.v.docx' ),
						array( 'title' => 'Radošo darbu studija – Mīļlietiņas', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_radoso_darbu_studija_MILLIETINAS.docx' ),
						array( 'title' => 'Veidošanas studija (4-6. g.v.)', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_VEIDOSANAS_STUDIJA_4-6-g.v.docx' ),
						array( 'title' => 'Arhitektūra', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_arhitektura.docx' ),
					),
				),
				array(
					'name'  => 'Zinātne, Dabaszinātne',
					'image' => 'https://jaunradesnams.lv/wp-content/uploads/Lego-Mindstorms-robot-009-150x150.jpg',
					'files' => array(
						array( 'title' => 'Mazo gudrinieku laboratorija', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_mazo_gudrinieku_laboratorija.docx' ),
						array( 'title' => 'Interaktīvā nodarbība – Mēness', 'url' => 'https://jaunradesnams.lv/wp-content/uploads/metodika_interaktiva_nodarbiba_meness.docx' ),
					),
				),
			),
		),
		array(
			'group'     => 'Sporta joma',
			'subgroups' => array(
				array(
					'name'  => 'Sports un fiziskās aktivitātes',
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
			'group'     => 'Citas mācību jomas',
			'subgroups' => array(
				array(
					'name'  => 'Valodas, uzņēmējdarbība un citi',
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
}

function jn_staff() {
	return array(
		array( 'role' => 'Direktore', 'name' => 'Vita Hermansone', 'phone' => '+371 636 22805', 'email' => 'vita.hermansone@ventspils.lv' ),
		array( 'role' => 'Direktores vietniece interešu izglītības jomā', 'name' => 'Ilona Klāviņa', 'phone' => '+371 636 22805', 'email' => 'ilona.klavina@ventspils.lv' ),
		array( 'role' => 'Direktores vietniece jaunrades jomā', 'name' => 'Ineta Kalniņa', 'phone' => '+371 636 22805', 'email' => 'ineta.kalnina@ventspils.lv' ),
		array( 'role' => 'Direktores vietniece jaunrades jomā', 'name' => 'Sandra Gūtmane', 'phone' => '+371 636 22805', 'email' => 'sandra.gutmane@ventspils.lv' ),
		array( 'role' => 'Saimniecības vadītāja', 'name' => 'Aiva Vilcāne', 'phone' => '+371 636 22805', 'email' => 'aiva.vilcane@ventspils.lv' ),
		array( 'role' => 'Lietvedības sekretāre', 'name' => 'Laura Baķe', 'phone' => '+371 636 22805', 'email' => 'laura.bake@ventspils.lv' ),
	);
}

function jn_teacher_fallback_html() {
	ob_start();
	?>
	<h3><strong>Aktualitātes:</strong></h3>
	<p><strong>Ventspils Jaunrades nama kalendārs mācību gadam</strong> aplūkojams
		<a href="https://docs.google.com/spreadsheets/d/1IjJe04ph9lnV-1Z_7w3X-Rcq1ZtuHrpN37pLJJ60QEY/edit#gid=15019426" target="_blank" rel="noopener">šeit</a>
		vai ierakstot adresi <strong>ej.uz/VJNkalendars</strong></p>
	<h3><strong>Faili skolotājiem:</strong></h3>
	<a href="https://jaunradesnams.lv/wp-content/uploads/darba_kartibas_noteikumi_2026.pdf" class="teacher-btn" target="_blank" rel="noopener">• Darba kārtības noteikumi</a>
	<p class="mt-24"><strong>Iesniegums audzēkņu uzņemšanai u.c. dokumenti</strong>
		<a href="<?php echo esc_url( home_url( '/jaunrades-nams/dokumenti/' ) ); ?>">šeit</a></p>
	<h3><strong>Pieprasījums ārpusnodarbību pasākumam:</strong></h3>
	<p><em>Šie abi dokumenti ir saistoši pedagogiem, kuri vēlas rīkot pasākumus Jaunrades namā un arī ārpus tā. Tehniskais pieteikums jāiesniedz ne vēlāk kā mēnesi pirms pasākuma, bet programmas pieteikums – ne vēlāk kā trīs darba dienas pēc pasākuma.</em></p>
	<a href="https://jaunradesnams.lv/wp-content/uploads/programmas_pazinojums_AKKA-LAA.doc" class="teacher-btn" target="_blank" rel="noopener">• Programmas paziņojums</a>
	<a href="https://jaunradesnams.lv/wp-content/uploads/tehniskais_pieteikums_JN_kolektiviem.docx" class="teacher-btn" target="_blank" rel="noopener">• Pasākuma tehniskais pieteikums</a>
	<h3><strong>Dokumenti komandējumiem:</strong></h3>
	<a href="https://jaunradesnams.lv/wp-content/uploads/komandejuma_pieprasijums_1.docx" class="teacher-btn" target="_blank" rel="noopener">• Komandējuma pieprasījums</a>
	<a href="https://jaunradesnams.lv/wp-content/uploads/transporta_pieprasijums_2026_1.docx" class="teacher-btn" target="_blank" rel="noopener">• Transporta pieprasījums</a>
	<a href="https://jaunradesnams.lv/wp-content/uploads/komandejuma_parskats_2026_1.doc" class="teacher-btn" target="_blank" rel="noopener">• Komandējuma pārskats</a>
	<?php
	return ob_get_clean();
}
