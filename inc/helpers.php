<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jn_remote_base() {
	return 'https://jaunradesnams.lv';
}

function jn_api_base() {
	return jn_remote_base() . '/wp-json/wp/v2/';
}

function jn_asset( $path ) {
	return get_template_directory_uri() . '/assets/' . ltrim( $path, '/' );
}

function jn_single_url_post( $slug ) {
	return home_url( '/' . sanitize_title( $slug ) . '/' );
}

function jn_single_url_program( $slug, $cat = '' ) {
	$cat = $cat ? trailingslashit( sanitize_title( $cat ) ) : '';
	return home_url( '/nodarbiba/' . $cat . sanitize_title( $slug ) . '/' );
}

function jn_page_url( $base, $page ) {
	$page = (int) $page;
	return $page <= 1 ? $base : add_query_arg( 'strana', $page, $base );
}

function jn_pg_range( $cur, $tot, $w = 1 ) {
	$cur = max( 1, min( (int) $cur, (int) $tot ) );
	$k   = array();
	for ( $i = 1; $i <= $tot; $i++ ) {
		if ( 1 === $i || $tot === $i || ( $i >= $cur - $w && $i <= $cur + $w ) ) {
			$k[] = $i;
		}
	}
	$out  = array();
	$prev = 0;
	foreach ( $k as $p ) {
		if ( $prev && $p - $prev > 1 ) {
			$out[] = -1;
		}
		$out[] = $p;
		$prev  = $p;
	}
	return $out;
}

function jn_pagination_range( $current, $total, $window = 1 ) {
	return jn_pg_range( $current, $total, $window );
}

function jn_is_planetarium() {
	static $is = null;
	if ( null !== $is ) {
		return $is;
	}

	$is = false;

	if ( ! empty( $GLOBALS['is_planetarium_post'] ) ) {
		$is = true;
		return $is;
	}

	$path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '';
	if ( false !== strpos( $path, '/planetarijs/' ) ) {
		$is = true;
		return $is;
	}

	if ( is_page( 'planetarijs' ) ) {
		$is = true;
		return $is;
	}

	if ( is_page() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			$ancestors = get_post_ancestors( $post );
			foreach ( $ancestors as $ancestor_id ) {
				if ( 'planetarijs' === get_post_field( 'post_name', $ancestor_id ) ) {
					$is = true;
					return $is;
				}
			}
		}
	}

	return $is;
}

function jn_school_year() {
	$month = (int) wp_date( 'n' );
	$year  = (int) wp_date( 'Y' );
	$start = $month >= 9 ? $year : $year - 1;
	return $start . './' . ( $start + 1 ) . '.';
}

function jn_date_lv( $date, $with_time = false ) {
	if ( empty( $date ) ) {
		return '';
	}

	$ts = is_numeric( $date ) ? (int) $date : strtotime( (string) $date );
	if ( ! $ts ) {
		return '';
	}

	$months = array(
		1  => 'janvāris',
		2  => 'februāris',
		3  => 'marts',
		4  => 'aprīlis',
		5  => 'maijs',
		6  => 'jūnijs',
		7  => 'jūlijs',
		8  => 'augusts',
		9  => 'septembris',
		10 => 'oktobris',
		11 => 'novembris',
		12 => 'decembris',
	);

	$month_num = (int) wp_date( 'n', $ts );
	$out       = wp_date( 'j', $ts ) . '. ' . $months[ $month_num ] . ' ' . wp_date( 'Y', $ts );

	if ( $with_time ) {
		$out .= ', ' . wp_date( 'H:i', $ts );
	}

	return $out;
}

function jn_trim_text( $text, $length = 160 ) {
	$text = trim( wp_strip_all_tags( (string) $text ) );
	$text = preg_replace( '/\s+/u', ' ', $text );
	$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
	if ( mb_strlen( $text ) <= $length ) {
		return $text;
	}
	return rtrim( mb_substr( $text, 0, $length - 1 ) ) . '…';
}

function jn_remote_page_id( $slug ) {
	$map = array(
		'galerija'                        => 15,
		'planetarijs-galerija'            => 65,
		'seansu-laiki'                    => 24,
		'cenas'                           => 28,
		'darbibas-apraksts'               => 61,
		'noteikumi'                       => 63,
		'repertuars'                      => 26,
		'pulcini'                         => 23901,
		'piedavajums-izglitibas-iestadem' => 24319,
	);
	return isset( $map[ $slug ] ) ? (int) $map[ $slug ] : 0;
}

function jn_current_url() {
	$path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
	return home_url( $path );
}
