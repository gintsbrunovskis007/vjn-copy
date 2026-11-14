<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jn_seo_site_name() {
	return jn_is_planetarium() ? 'Ventspils Planetārijs' : 'Ventspils Jaunrades nams';
}

function jn_seo_title( $title ) {
	$site = jn_seo_site_name();

	$virtual_title = '';
	$slug          = get_query_var( 'jn_single_post' );
	if ( $slug ) {
		$post = jn_get_single_post( $slug );
		if ( $post ) {
			$virtual_title = wp_strip_all_tags( $post['title']['rendered'] );
		}
	}

	$program = get_query_var( 'jn_single_program' );
	if ( ! $virtual_title && $program ) {
		$page = jn_get_single_post( $program );
		if ( $page ) {
			$virtual_title = wp_strip_all_tags( $page['title']['rendered'] );
		}
	}

	if ( $virtual_title ) {
		return $virtual_title . ' | ' . $site;
	}

	if ( is_front_page() ) {
		return 'Ventspils Jaunrades nams — interešu izglītība bērniem un jauniešiem';
	}

	if ( is_404() ) {
		return 'Lapa nav atrasta | ' . $site;
	}

	if ( is_search() ) {
		return 'Meklēšana: ' . get_search_query() . ' | ' . $site;
	}

	$page_title = '';

	$slug = get_query_var( 'jn_single_post' );
	if ( $slug ) {
		$post = jn_get_single_post( $slug );
		if ( $post ) {
			$page_title = wp_strip_all_tags( $post['title']['rendered'] );
		}
	}

	$program = get_query_var( 'jn_single_program' );
	if ( ! $page_title && $program ) {
		$page = jn_get_single_post( $program );
		if ( $page ) {
			$page_title = wp_strip_all_tags( $page['title']['rendered'] );
		}
	}

	if ( ! $page_title && is_page() ) {
		$page_title = get_the_title();

		if ( is_page_template( array( 'page-galerija.php', 'page-planetarijs-galerija.php' ) ) && ! empty( $_GET['albums'] ) ) {
			$album = basename( (string) wp_parse_url( wp_unslash( $_GET['albums'] ), PHP_URL_PATH ) );
			if ( $album ) {
				$page_title = ucfirst( str_replace( '-', ' ', $album ) );
			}
		}

		if ( is_page( 'pulcini' ) && ! empty( $_GET['kat'] ) && function_exists( 'jn_hubs' ) ) {
			$hubs = jn_hubs();
			$kat  = sanitize_key( $_GET['kat'] );
			if ( isset( $hubs[ $kat ] ) ) {
				$page_title = $hubs[ $kat ]['name'] . ' — Nodarbības';
			}
		}
	}

	if ( ! $page_title && is_singular() ) {
		$page_title = get_the_title();
	}

	if ( ! $page_title ) {
		$page_title = $title;
	}

	$paged = max( (int) get_query_var( 'paged' ), isset( $_GET['strana'] ) ? (int) $_GET['strana'] : 1 );
	if ( $paged > 1 ) {
		$page_title .= ' — ' . $paged . '. lapa';
	}

	return trim( wp_strip_all_tags( $page_title ) ) . ' | ' . $site;
}
add_filter( 'pre_get_document_title', 'jn_seo_title', 20 );

function jn_seo_page_description() {
	if ( is_front_page() && ! get_query_var( 'jn_single_post' ) && ! get_query_var( 'jn_single_program' ) ) {
		return 'Interešu izglītība bērniem un jauniešiem Ventspilī: pulciņi, planetārijs, observatorija, radošās nodarbības un pasākumi.';
	}

	if ( is_404() ) {
		return 'Meklētā lapa nav atrasta. Atgriezieties sākumlapā vai izmantojiet navigāciju.';
	}

	if ( is_search() ) {
		return 'Meklēšanas rezultāti Ventspils Jaunrades nama mājaslapā.';
	}

	$slug = get_query_var( 'jn_single_post' );
	if ( $slug ) {
		$post = jn_get_single_post( $slug );
		if ( $post ) {
			$text = $post['excerpt']['rendered'] ?? '';
			if ( '' === trim( wp_strip_all_tags( $text ) ) ) {
				$text = $post['content']['rendered'] ?? '';
			}
			return jn_trim_text( $text, 158 );
		}
	}

	$program = get_query_var( 'jn_single_program' );
	if ( $program ) {
		$page = jn_get_single_post( $program );
		if ( $page ) {
			$text = $page['excerpt']['rendered'] ?? '';
			if ( '' === trim( wp_strip_all_tags( $text ) ) ) {
				$text = $page['content']['rendered'] ?? '';
			}
			return jn_trim_text( $text, 158 );
		}
	}

	if ( is_page() ) {
		$map = array(
			'planetarijs'                       => 'Ventspils Planetārijs un Observatorija — seansu laiki, biļešu cenas, repertuārs un aktualitātes.',
			'seansu-laiki'                      => 'Ventspils Planetārija seansu un observatorijas apmeklējumu laiki.',
			'repertuars'                        => 'Ventspils Planetārija filmu repertuārs un aktuālās programmas.',
			'cenas'                             => 'Ventspils Planetārija biļešu cenas un apmeklējuma izcenojumi.',
			'darbibas-apraksts'                 => 'Ventspils Planetārija un Observatorijas darbības apraksts.',
			'noteikumi'                         => 'Ventspils Planetārija iekšējās kārtības un drošības noteikumi.',
			'galerija'                          => 'Ventspils Jaunrades nama un Planetārija foto galerija.',
			'pulcini'                           => 'Interešu izglītības programmas un pulciņi Ventspils bērniem un jauniešiem.',
			'aktualitates'                      => 'Ventspils Jaunrades nama jaunākās aktualitātes, notikumi un paziņojumi.',
			'dokumenti'                         => 'Ventspils Jaunrades nama dokumenti, iesniegumi un resursi pedagogiem un vecākiem.',
			'par-mums'                          => 'Ventspils Jaunrades nama vēsture, misija un komanda.',
			'kontakti'                          => 'Ventspils Jaunrades nama kontakti, adrese un darba laiks.',
			'piedavajums-izglitibas-iestadem'   => 'Piedāvājums izglītības iestādēm — programmas un pasākumi Ventspils Jaunrades namā.',
			'pieklistamibas-pazinojums'         => 'Ventspils Jaunrades nama mājaslapas piekļūstamības paziņojums.',
		);

		$post = get_queried_object();
		if ( $post instanceof WP_Post && isset( $map[ $post->post_name ] ) ) {
			return $map[ $post->post_name ];
		}

		$ancestor = get_post_ancestors( get_queried_object_id() );
		if ( ! empty( $ancestor ) ) {
			$root = get_post_field( 'post_name', end( $ancestor ) );
			if ( isset( $map[ $root ] ) ) {
				return $map[ $root ];
			}
		}
	}

	$tagline = get_bloginfo( 'description' );
	return $tagline ? jn_trim_text( $tagline, 158 ) : 'Ventspils Jaunrades nams — vieta, kur bērni un jaunieši atklāj sevi.';
}

function jn_seo_canonical_url() {
	if ( is_admin() || is_feed() || is_404() || is_search() ) {
		return '';
	}

	if ( is_front_page() && ! get_query_var( 'jn_single_post' ) && ! get_query_var( 'jn_single_program' ) ) {
		return home_url( '/' );
	}

	$slug = get_query_var( 'jn_single_post' );
	if ( $slug ) {
		return jn_single_url_post( $slug );
	}

	$program = get_query_var( 'jn_single_program' );
	if ( $program ) {
		$path      = wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH );
		$path      = trim( (string) $path, '/' );
		$home_path = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
		if ( $home_path && 0 === strpos( $path, $home_path ) ) {
			$path = trim( substr( $path, strlen( $home_path ) ), '/' );
		}
		$path = $path ? $path : 'nodarbiba/' . $program;
		return home_url( user_trailingslashit( '/' . $path ) );
	}

	if ( is_page() ) {
		$base = get_permalink();
		$args = array();

		if ( ! empty( $_GET['albums'] ) ) {
			$args['albums'] = wp_unslash( $_GET['albums'] );
		}
		if ( ! empty( $_GET['kat'] ) ) {
			$args['kat'] = sanitize_key( $_GET['kat'] );
		}

		$paged = max( (int) get_query_var( 'paged' ), isset( $_GET['strana'] ) ? (int) $_GET['strana'] : 1 );
		if ( $paged > 1 ) {
			$args['strana'] = $paged;
		}

		return empty( $args ) ? $base : add_query_arg( $args, $base );
	}

	if ( is_singular() ) {
		return get_permalink();
	}

	return '';
}

function jn_seo_image() {
	$image = array(
		'url'    => jn_asset( 'logo.png' ),
		'width'  => 207,
		'height' => 121,
		'alt'    => jn_seo_site_name(),
	);

	if ( is_front_page() && ! get_query_var( 'jn_single_post' ) && ! get_query_var( 'jn_single_program' ) ) {
		return array(
			'url'    => jn_asset( 'img/hero-1200.jpg' ),
			'width'  => 1200,
			'height' => 666,
			'alt'    => 'Ventspils Jaunrades nams',
		);
	}

	if ( is_page( 'planetarijs' ) ) {
		return array(
			'url'    => jn_asset( 'img/planetarijs-logo.png' ),
			'width'  => 320,
			'height' => 187,
			'alt'    => 'Ventspils Planetārijs',
		);
	}

	$slug = get_query_var( 'jn_single_post' );
	if ( $slug ) {
		$post = jn_get_single_post( $slug );
		if ( $post && ! empty( $post['media']['url'] ) ) {
			return array(
				'url'    => $post['media']['url'],
				'width'  => (int) ( $post['media']['width'] ?? 0 ),
				'height' => (int) ( $post['media']['height'] ?? 0 ),
				'alt'    => $post['media']['alt'] ?? '',
			);
		}
	}

	$program = get_query_var( 'jn_single_program' );
	if ( $program ) {
		$page = jn_get_single_post( $program );
		if ( $page ) {
			$first = $page['media']['url'] ?? '';
			$first = $first ? array( 'url' => $first, 'width' => $page['media']['width'] ?? 0, 'height' => $page['media']['height'] ?? 0, 'alt' => $page['media']['alt'] ?? '' ) : jn_extract_first_image( $page['content']['rendered'] ?? '' );
			if ( ! empty( $first['url'] ) ) {
				return array(
					'url'    => $first['url'],
					'width'  => (int) ( $first['width'] ?? 0 ),
					'height' => (int) ( $first['height'] ?? 0 ),
					'alt'    => $first['alt'] ?? '',
				);
			}
		}
	}

	return $image;
}

function jn_seo_breadcrumb_items() {
	$items = array(
		array( 'name' => 'Sākums', 'url' => home_url( '/' ) ),
	);

	if ( jn_is_planetarium() ) {
		$items[] = array( 'name' => 'Planetārijs', 'url' => home_url( '/planetarijs/' ) );
	}

	$slug = get_query_var( 'jn_single_post' );
	if ( $slug ) {
		$items[] = array(
			'name' => jn_is_planetarium() ? 'Aktualitātes' : 'Aktualitātes',
			'url'  => jn_is_planetarium() ? home_url( '/planetarijs/aktualitates/' ) : home_url( '/jaunrades-nams/aktualitates/' ),
		);
		$post = jn_get_single_post( $slug );
		if ( $post ) {
			$items[] = array( 'name' => wp_strip_all_tags( $post['title']['rendered'] ), 'url' => '' );
		}
		return $items;
	}

	$program = get_query_var( 'jn_single_program' );
	if ( $program ) {
		$items[] = array( 'name' => 'Nodarbības', 'url' => home_url( '/jaunrades-nams/pulcini/' ) );
		$page    = jn_get_single_post( $program );
		if ( $page ) {
			$items[] = array( 'name' => wp_strip_all_tags( $page['title']['rendered'] ), 'url' => '' );
		}
		return $items;
	}

	if ( is_page() ) {
		$post_id = get_queried_object_id();
		$ancestors = array_reverse( get_post_ancestors( $post_id ) );

		foreach ( $ancestors as $ancestor_id ) {
			$ancestor_slug = get_post_field( 'post_name', $ancestor_id );
			if ( 'jaunrades-nams' === $ancestor_slug || 'planetarijs' === $ancestor_slug ) {
				continue;
			}
			$items[] = array(
				'name' => get_the_title( $ancestor_id ),
				'url'  => get_permalink( $ancestor_id ),
			);
		}

		$items[] = array( 'name' => get_the_title( $post_id ), 'url' => '' );
	}

	return $items;
}

function jn_seo_json_ld() {
	$home      = home_url( '/' );
	$canonical = jn_seo_canonical_url();
	$title     = wp_get_document_title();
	$desc      = jn_seo_page_description();
	$image     = jn_seo_image();
	$site_name = jn_seo_site_name();

	$org = array(
		'@type' => 'Organization',
		'@id'   => $home . '#organization',
		'name'  => 'Ventspils Jaunrades nams',
		'url'   => $home,
		'logo'  => array(
			'@type'  => 'ImageObject',
			'url'    => jn_asset( 'logo.png' ),
			'width'  => 207,
			'height' => 121,
		),
		'address' => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => 'Maiznieku iela 11',
			'addressLocality' => 'Ventspils',
			'postalCode'      => 'LV-3601',
			'addressCountry'  => 'LV',
		),
		'contactPoint' => array(
			array(
				'@type'             => 'ContactPoint',
				'telephone'         => '+371 636 22805',
				'contactType'       => 'customer service',
				'email'             => 'jaunrades.nams@ventspils.lv',
				'availableLanguage' => array( 'lv', 'en' ),
			),
			array(
				'@type'             => 'ContactPoint',
				'telephone'         => '+371 636 22805',
				'contactType'       => 'planetarium',
				'email'             => 'planetarijs.observatorija@ventspils.lv',
				'availableLanguage' => array( 'lv', 'en' ),
			),
		),
	);

	$website = array(
		'@type'      => 'WebSite',
		'@id'        => $home . '#website',
		'url'        => $home,
		'name'       => $site_name,
		'inLanguage' => 'lv-LV',
		'publisher'  => array( '@id' => $home . '#organization' ),
	);

	$graph = array( $org, $website );

	if ( $canonical ) {
		$webpage = array(
			'@type'           => 'WebPage',
			'@id'             => $canonical . '#webpage',
			'url'             => $canonical,
			'name'            => $title,
			'description'     => $desc,
			'isPartOf'        => array( '@id' => $home . '#website' ),
			'inLanguage'      => 'lv-LV',
			'primaryImageOfPage' => array(
				'@type'  => 'ImageObject',
				'url'    => $image['url'],
				'width'  => (int) $image['width'],
				'height' => (int) $image['height'],
			),
		);

		$breadcrumbs = jn_seo_breadcrumb_items();
		if ( count( $breadcrumbs ) > 1 ) {
			$list = array();
			foreach ( $breadcrumbs as $i => $item ) {
				$entry = array(
					'@type'    => 'ListItem',
					'position' => $i + 1,
					'name'     => $item['name'],
				);
				if ( ! empty( $item['url'] ) ) {
					$entry['item'] = $item['url'];
				}
				$list[] = $entry;
			}
			$graph[] = array(
				'@type'           => 'BreadcrumbList',
				'@id'             => $canonical . '#breadcrumb',
				'itemListElement' => $list,
			);
		}

		$graph[] = $webpage;

		$slug = get_query_var( 'jn_single_post' );
		if ( $slug ) {
			$post = jn_get_single_post( $slug );
			if ( $post ) {
				$graph[] = array(
					'@type'            => 'NewsArticle',
					'@id'              => $canonical . '#article',
					'headline'         => wp_strip_all_tags( $post['title']['rendered'] ),
					'description'      => $desc,
					'datePublished'    => $post['date'] ?: null,
					'dateModified'     => $post['modified'] ?: $post['date'],
					'image'            => array( $image['url'] ),
					'author'           => array( '@id' => $home . '#organization' ),
					'publisher'        => array( '@id' => $home . '#organization' ),
					'mainEntityOfPage' => array( '@id' => $canonical . '#webpage' ),
					'inLanguage'       => 'lv-LV',
				);
			}
		}

		$program = get_query_var( 'jn_single_program' );
		if ( $program ) {
			$page = jn_get_single_post( $program );
			if ( $page ) {
				$graph[] = array(
					'@type'       => 'Course',
					'@id'         => $canonical . '#course',
					'name'        => wp_strip_all_tags( $page['title']['rendered'] ),
					'description' => $desc,
					'provider'    => array( '@id' => $home . '#organization' ),
					'url'         => $canonical,
					'inLanguage'  => 'lv-LV',
				);
			}
		}
	}

	$schema = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);

	return wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP );
}

add_action(
	'wp_head',
	function () {
		$desc      = jn_seo_page_description();
		$canonical = jn_seo_canonical_url();
		$image     = jn_seo_image();
		$title     = wp_get_document_title();

		if ( $desc ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
		}

		if ( $canonical ) {
			printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $canonical ) );
			printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $canonical ) );
		}

		printf( '<meta property="og:type" content="%s">' . "\n", ( get_query_var( 'jn_single_post' ) || get_query_var( 'jn_single_program' ) ) ? 'article' : 'website' );
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( jn_seo_site_name() ) );
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
		printf( '<meta property="og:locale" content="lv_LV">' . "\n" );

		if ( ! empty( $image['url'] ) ) {
			printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image['url'] ) );
			if ( ! empty( $image['width'] ) ) {
				printf( '<meta property="og:image:width" content="%d">' . "\n", (int) $image['width'] );
			}
			if ( ! empty( $image['height'] ) ) {
				printf( '<meta property="og:image:height" content="%d">' . "\n", (int) $image['height'] );
			}
			if ( ! empty( $image['alt'] ) ) {
				printf( '<meta property="og:image:alt" content="%s">' . "\n", esc_attr( $image['alt'] ) );
			}
		}

		printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
		printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
		if ( ! empty( $image['url'] ) ) {
			printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image['url'] ) );
		}

		$slug = get_query_var( 'jn_single_post' );
		if ( $slug ) {
			$post = jn_get_single_post( $slug );
			if ( $post && ! empty( $post['date'] ) ) {
				printf( '<meta property="article:published_time" content="%s">' . "\n", esc_attr( $post['date'] ) );
				printf( '<meta property="article:modified_time" content="%s">' . "\n", esc_attr( $post['modified'] ?: $post['date'] ) );
			}
		}

		echo '<script type="application/ld+json">' . jn_seo_json_ld() . '</script>' . "\n";
	},
	5
);

remove_action( 'wp_head', 'rel_canonical' );

add_filter(
	'wp_robots',
	function ( $robots ) {
		$robots['max-image-preview'] = 'large';
		$robots['max-snippet']       = '-1';
		$robots['max-video-preview'] = '-1';

		if ( is_404() || is_search() || is_author() ) {
			unset( $robots['index'] );
			$robots['noindex'] = true;
			$robots['follow']  = true;
		}

		return $robots;
	},
	20
);

add_filter(
	'robots_txt',
	function ( $output, $public ) {
		if ( $public ) {
			$output = rtrim( $output ) . "\nSitemap: " . home_url( '/sitemap.xml' ) . "\n";
		}
		return $output;
	},
	10,
	2
);

function jn_sitemap_urls() {
	$urls = array();

	$urls[ home_url( '/' ) ] = array( 'priority' => '1.0', 'lastmod' => '' );

	foreach ( get_pages( array( 'post_status' => 'publish' ) ) as $page ) {
		$urls[ get_permalink( $page ) ] = array(
			'priority' => '0.7',
			'lastmod'  => get_post_modified_time( 'c', true, $page ),
		);
	}

	foreach ( get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => -1 ) ) as $post ) {
		$urls[ get_permalink( $post ) ] = array(
			'priority' => '0.6',
			'lastmod'  => get_post_modified_time( 'c', true, $post ),
		);
	}

	foreach ( array( 8, 7 ) as $category ) {
		for ( $page = 1; $page <= 5; $page++ ) {
			$result = jn_api_request(
				'posts',
				array(
					'categories' => $category,
					'per_page'   => 100,
					'page'       => $page,
					'_fields'    => 'slug,modified',
				),
				array( 'timeout' => 20 )
			);

			if ( empty( $result['data'] ) || ! is_array( $result['data'] ) ) {
				break;
			}

			foreach ( $result['data'] as $post ) {
				if ( empty( $post['slug'] ) ) {
					continue;
				}
				$urls[ jn_single_url_post( $post['slug'] ) ] = array(
					'priority' => '0.6',
					'lastmod'  => (string) ( $post['modified'] ?? '' ),
				);
			}

			if ( $page >= (int) $result['total_pages'] ) {
				break;
			}
		}
	}

	if ( function_exists( 'jn_get_programs_index' ) ) {
		foreach ( jn_get_programs_index() as $program ) {
			$urls[ $program['url'] ] = array( 'priority' => '0.6', 'lastmod' => '' );
		}
	}

	return $urls;
}

function jn_sitemap_xml() {
	$cached = get_transient( 'jn_sitemap_xml' );
	if ( is_string( $cached ) && $cached ) {
		return $cached;
	}

	$urls = jn_sitemap_urls();

	$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

	foreach ( $urls as $loc => $meta ) {
		$xml .= '<url>';
		$xml .= '<loc>' . esc_url( $loc ) . '</loc>';
		if ( ! empty( $meta['lastmod'] ) ) {
			$ts = strtotime( (string) $meta['lastmod'] );
			if ( $ts ) {
				$xml .= '<lastmod>' . esc_html( gmdate( 'c', $ts ) ) . '</lastmod>';
			}
		}
		if ( ! empty( $meta['priority'] ) ) {
			$xml .= '<priority>' . esc_html( $meta['priority'] ) . '</priority>';
		}
		$xml .= '</url>' . "\n";
	}

	$xml .= '</urlset>';

	set_transient( 'jn_sitemap_xml', $xml, HOUR_IN_SECONDS );

	return $xml;
}
