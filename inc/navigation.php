<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jn_nav_items() {
	$items = array(
		array( 'slug' => 'aktualitates', 'path' => '/jaunrades-nams/aktualitates/', 'label' => 'Aktualitātes' ),
		array( 'slug' => 'pulcini', 'path' => '/jaunrades-nams/pulcini/', 'label' => 'Nodarbības' ),
		array( 'slug' => 'galerija', 'path' => '/jaunrades-nams/galerija/', 'label' => 'Galerija' ),
		array( 'slug' => 'dokumenti', 'path' => '/jaunrades-nams/dokumenti/', 'label' => 'Resursi' ),
		array( 'slug' => 'par-mums', 'path' => '/jaunrades-nams/par-mums/', 'label' => 'Par mums' ),
		array( 'slug' => 'kontakti', 'path' => '/jaunrades-nams/kontakti/', 'label' => 'Kontakti', 'class' => 'nav-contact' ),
	);
	return apply_filters( 'jn_nav_items', $items );
}

function jn_planetarium_nav_items() {
	$items = array(
		array( 'slug' => 'planetarijs', 'path' => '/planetarijs/', 'label' => 'Sākums' ),
		array( 'slug' => 'aktualitates', 'path' => '/planetarijs/aktualitates/', 'label' => 'Aktualitātes' ),
		array( 'slug' => 'repertuars', 'path' => '/planetarijs/repertuars/', 'label' => 'Repertuārs' ),
		array( 'slug' => 'seansu-laiki', 'path' => '/planetarijs/seansu-laiki/', 'label' => 'Seansu laiki' ),
		array( 'slug' => 'galerija', 'path' => '/planetarijs/galerija/', 'label' => 'Galerija' ),
		array( 'slug' => 'cenas', 'path' => '/planetarijs/cenas/', 'label' => 'Cenas' ),
		array( 'slug' => 'darbibas-apraksts', 'path' => '/planetarijs/darbibas-apraksts/', 'label' => 'Par mums' ),
		array( 'slug' => 'noteikumi', 'path' => '/planetarijs/noteikumi/', 'label' => 'Noteikumi' ),
	);
	return apply_filters( 'jn_planetarium_nav_items', $items );
}

function jn_nav_is_current( $item ) {
	if ( jn_is_planetarium() ) {
		if ( 'planetarijs' === $item['slug'] ) {
			return is_page( 'planetarijs' );
		}
		$path = wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH );
		if ( $path ) {
			return 0 === strpos( trailingslashit( $path ), $item['path'] );
		}
		return false;
	}
	return is_page( $item['slug'] );
}

function jn_footer_links() {
	$links = jn_is_planetarium()
		? array(
			array( '/planetarijs/', 'Sākums' ),
			array( '/planetarijs/aktualitates/', 'Aktualitātes' ),
			array( '/planetarijs/repertuars/', 'Repertuārs' ),
			array( '/planetarijs/seansu-laiki/', 'Seansu laiki' ),
			array( '/planetarijs/cenas/', 'Cenas' ),
			array( '/planetarijs/noteikumi/', 'Noteikumi' ),
		)
		: array(
			array( '/jaunrades-nams/pulcini/', 'Nodarbības' ),
			array( '/jaunrades-nams/aktualitates/', 'Aktualitātes' ),
			array( '/jaunrades-nams/galerija/', 'Galerija' ),
			array( '/jaunrades-nams/dokumenti/', 'Resursi' ),
			array( '/jaunrades-nams/par-mums/', 'Par mums' ),
			array( '/jaunrades-nams/kontakti/', 'Kontakti' ),
		);

	return apply_filters( 'jn_footer_links', $links );
}

function jn_switch_url() {
	return jn_is_planetarium() ? home_url( '/' ) : home_url( '/planetarijs/' );
}
