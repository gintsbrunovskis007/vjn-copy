<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jn_load_lesson_changes() {
	$cache = get_transient( 'jn_changes_pulcini_v3' );
	if ( is_array( $cache ) ) {
		return $cache;
	}

	$data = jn_get_remote_page_data( 'pulcini' );
	if ( ! is_array( $data ) || empty( $data['content']['rendered'] ) ) {
		return array( 'modified' => '', 'items' => array() );
	}

	$html     = $data['content']['rendered'];
	$modified = $data['modified'] ?? '';
	$items    = array();

	if ( class_exists( 'DOMDocument' ) ) {
		$doc = new DOMDocument();
		libxml_use_internal_errors( true );
		$doc->loadHTML(
			'<?xml encoding="UTF-8"><div id="jn-changes-wrap">' . $html . '</div>',
			LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
		);
		libxml_clear_errors();

		$xpath   = new DOMXPath( $doc );
		$top_uls = $xpath->query( '//div[@id="jn-changes-wrap"]/ul' );

		if ( $top_uls->length > 0 ) {
			foreach ( $top_uls->item( 0 )->childNodes as $li ) {
				if ( 'li' !== $li->nodeName ) {
					continue;
				}

				$prefix_html = '';
				$teacher     = '';
				$course      = '';
				$nested_ul   = null;

				foreach ( $li->childNodes as $child ) {
					if ( 'ul' === $child->nodeName ) {
						$nested_ul = $child;
						break;
					}
					$prefix_html .= $doc->saveHTML( $child );
				}

				$prefix_text = trim( html_entity_decode( wp_strip_all_tags( $prefix_html ), ENT_QUOTES, 'UTF-8' ) );
				$prefix_text = preg_replace( '/\s+/u', ' ', $prefix_text );

				if ( $nested_ul ) {
					foreach ( $nested_ul->childNodes as $t_li ) {
						if ( 'li' !== $t_li->nodeName ) {
							continue;
						}

						$strongs = $t_li->getElementsByTagName( 'strong' );
						if ( $strongs->length > 0 ) {
							$teacher = trim( $strongs->item( 0 )->textContent );
						}

						$course_uls = $t_li->getElementsByTagName( 'ul' );
						if ( $course_uls->length > 0 ) {
							foreach ( $course_uls->item( 0 )->childNodes as $c_li ) {
								if ( 'li' === $c_li->nodeName ) {
									$course = trim( $c_li->textContent );
								}
							}
						}
						break;
					}
				}

				if ( $prefix_text || $teacher || $course ) {
					$items[] = array(
						'prefix'  => $prefix_text,
						'teacher' => $teacher,
						'course'  => $course,
					);
				}
			}
		}
	}

	$result = array( 'modified' => $modified, 'items' => $items );
	if ( ! empty( $items ) ) {
		set_transient( 'jn_changes_pulcini_v3', $result, 5 * MINUTE_IN_SECONDS );
	}

	return $result;
}

function jn_parse_hub_page( $page, $hub_slug = '' ) {
	$html = $page['content']['rendered'] ?? '';
	if ( '' === (string) $html ) {
		return array( 'image' => '', 'categories' => array() );
	}

	$image = '';
	if ( preg_match_all( '/<img[^>]+(?:data-src|src)=["\']([^"\']+)["\'][^>]*>/i', $html, $imgs ) ) {
		foreach ( $imgs[1] as $src ) {
			if ( false !== strpos( $src, 'teatris_mes_logo' ) ) {
				continue;
			}
			if ( false !== strpos( $src, 'Lego-Mindstorms' ) ) {
				$image = $src;
				break;
			}
			if ( false !== strpos( $src, 'hockey' ) ) {
				$image = $src;
				break;
			}
			if ( false !== strpos( $src, 'kulturizglitiba' ) ) {
				$image = $src;
				break;
			}
			if ( false !== strpos( $src, 'greenhands' ) ) {
				$image = $src;
				break;
			}
			if ( ! $image ) {
				$image = $src;
			}
		}
	}

	$categories = array();
	if ( preg_match_all(
		'#(?:https://jaunradesnams\.lv)?/jaunrades-nams/pulcini/([^/"\']+)/([^/"\']+)/#',
		$html,
		$m,
		PREG_SET_ORDER
	) ) {
		foreach ( $m as $row ) {
			$cat_slug  = $row[1];
			$prog_slug = $row[2];
			if ( ! isset( $categories[ $cat_slug ] ) ) {
				$categories[ $cat_slug ] = array(
					'name'     => ucfirst( str_replace( '-', ' ', $cat_slug ) ),
					'programs' => array(),
				);
			}
			$categories[ $cat_slug ]['programs'][ $prog_slug ] = $row[0];
		}
	}

	if ( preg_match_all( '/href=["\']([^"\']+)["\']/i', $html, $links ) ) {
		$skip = array(
			'jaunrades-nams', 'stem', 'kulturizglitibas-programmas',
			'sports-un-fiziskas-aktivitates', 'socialas-un-pilsoniskas-jomas',
			'pulcini', 'planetarijs', 'dokumenti', 'par-mums', 'kontakti',
			'galerija', 'ceni', 'schedule', 'ticket-prices', 'about-us',
			'useful-information', 'o-nas', 'poljeznaja-informacia',
			'pieklustamibas-pazinojums', 'izglitibas-iestadem',
			'piedavajums-izglitibas-iestadem', 'feed', 'comments',
			'wp-admin', 'wp-content', 'wp-includes', 'wp-json', 'wp-login.php',
		);

		$key  = $hub_slug ? $hub_slug : 'citas';
		$hubs = function_exists( 'jn_hubs' ) ? jn_hubs() : array();
		$name = isset( $hubs[ $key ]['name'] ) ? $hubs[ $key ]['name'] : 'Citas programmas';

		foreach ( $links[1] as $url ) {
			$url = html_entity_decode( trim( $url ), ENT_QUOTES, 'UTF-8' );
			if ( '' === $url || '#' === $url[0] || 0 === stripos( $url, 'mailto:' ) || 0 === stripos( $url, 'tel:' ) ) {
				continue;
			}

			$path = wp_parse_url( $url, PHP_URL_PATH );
			$path = trim( (string) $path, '/' );
			if ( '' === $path || false !== strpos( $path, 'wp-content/' ) || false !== strpos( $path, '/jaunrades-nams/pulcini/' ) ) {
				continue;
			}

			$segments = array_values( array_filter( explode( '/', $path ) ) );
			if ( 1 !== count( $segments ) ) {
				continue;
			}

			$slug = sanitize_title( $segments[0] );
			if ( '' === $slug || in_array( $slug, $skip, true ) ) {
				continue;
			}

			if ( ! isset( $categories[ $key ] ) ) {
				$categories[ $key ] = array( 'name' => $name, 'programs' => array() );
			}
			$categories[ $key ]['programs'][ $slug ] = $url;
		}
	}

	return array( 'image' => $image, 'categories' => $categories );
}

function jn_parse_program( $page ) {
	$html  = $page['content']['rendered'] ?? '';
	$link  = $page['link'] ?? '#';
	$slug  = $page['slug'] ?? '';
	$title = wp_strip_all_tags( $page['title']['rendered'] ?? '' );

	$image = '';
	$alt   = '';
	if ( preg_match( '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $m ) ) {
		$image = esc_url_raw( $m[1] );
		if ( preg_match( '/alt=["\']([^"\']*)["\']/i', $m[0], $a ) ) {
			$alt = $a[1];
		}
	}

	$text = preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES, 'UTF-8' ) );
	$age  = '';
	$teacher = '';

	if ( preg_match( '/(?:Dal[īi]bniek[\p{L}]*|Vecums)\s*[:\-]?\s*([0-9][0-9\s\-\–\—\.līdz]{0,20}?gad[^\.,;]{0,12})/iu', $text, $m ) ) {
		$age = trim( $m[1] );
	}
	if ( preg_match( '/Skolot[āa]j[as]?\s*[:\-]\s*([\p{L}][\p{L}\.\-\s]{1,60})/u', $text, $m ) ) {
		$teacher = trim( preg_replace( '/\s+(Dal|Skol|Vec).*$/u', '', $m[1] ) );
	}

	$excerpt = '';
	if ( preg_match_all( '/<p[^>]*>(.*?)<\/p>/is', $html, $pm ) ) {
		foreach ( $pm[1] as $p ) {
			$clean = trim( html_entity_decode( wp_strip_all_tags( $p ), ENT_QUOTES, 'UTF-8' ) );
			if ( mb_strlen( $clean ) > 50
				&& false === stripos( $clean, 'Dalībniek' )
				&& false === stripos( $clean, 'Skolotāj' )
				&& false === stripos( $clean, 'Vecums' ) ) {
				$excerpt = wp_trim_words( $clean, 24 );
				break;
			}
		}
	}

	return array(
		'title'   => $title,
		'link'    => $link,
		'slug'    => $slug,
		'image'   => $image,
		'alt'     => $alt ?: $title,
		'age'     => $age,
		'teacher' => $teacher,
		'excerpt' => $excerpt,
	);
}

function jn_program_details( $slugs ) {
	$slugs = array_values( array_unique( array_filter( (array) $slugs ) ) );
	if ( empty( $slugs ) ) {
		return array();
	}

	$key    = 'jn_prog_details_v4_' . md5( implode( ',', $slugs ) );
	$cached = get_transient( $key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$out = array();
	foreach ( array_chunk( $slugs, 20 ) as $chunk ) {
		$pages = jn_get_pages_by_slugs( $chunk, 'slug,title,link,content' );
		foreach ( $pages as $slug => $page ) {
			$out[ $slug ] = jn_parse_program( $page );
		}
	}

	if ( ! empty( $out ) ) {
		set_transient( $key, $out, 15 * MINUTE_IN_SECONDS );
	}

	return $out;
}

function jn_cat_url( $hub = '', $page = 1 ) {
	$base = get_permalink() ?: home_url( '/jaunrades-nams/pulcini/' );
	$args = array();

	if ( $hub ) {
		$args['kat'] = $hub;
	}
	if ( $page > 1 ) {
		$args['strana'] = (int) $page;
	}

	return empty( $args ) ? $base : add_query_arg( $args, $base );
}

function jn_get_programs_index() {
	$cached = get_transient( 'jn_programs_index_v3' );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$hubs  = jn_hubs();
	$pages = jn_get_pages_by_slugs( array_keys( $hubs ), 'slug,content' );
	$index = array();

	foreach ( array_keys( $hubs ) as $hub_slug ) {
		$parsed = jn_parse_hub_page( $pages[ $hub_slug ] ?? array(), $hub_slug );
		foreach ( $parsed['categories'] as $cat_slug => $cat ) {
			foreach ( $cat['programs'] as $prog_slug => $remote_url ) {
				if ( isset( $index[ $prog_slug ] ) ) {
					continue;
				}
				$index[ $prog_slug ] = array(
					'slug' => $prog_slug,
					'cat'  => $cat_slug,
					'hub'  => $hub_slug,
					'url'  => jn_single_url_program( $prog_slug, $cat_slug ),
				);
			}
		}
	}

	$index = array_values( $index );
	if ( ! empty( $index ) ) {
		set_transient( 'jn_programs_index_v3', $index, HOUR_IN_SECONDS );
	}

	return $index;
}
