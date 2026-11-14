<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		$version = wp_get_theme()->get( 'Version' );

		wp_enqueue_style( 'jn-fonts', jn_asset( 'fonts/fonts.css' ), array(), $version );
		wp_enqueue_style( 'jn-style', get_stylesheet_uri(), array( 'jn-fonts' ), $version );

		if ( jn_is_planetarium() ) {
			wp_enqueue_style( 'jn-planetarium', jn_asset( 'css/planetarium.css' ), array( 'jn-style' ), $version );
		}

		wp_enqueue_script( 'jn-theme', jn_asset( 'theme.js' ), array(), $version, true );
		wp_script_add_data( 'jn-theme', 'strategy', 'defer' );

		if ( is_page_template( array( 'page-galerija.php', 'page-planetarijs-galerija.php' ) ) ) {
			wp_enqueue_script( 'jn-lightbox', jn_asset( 'lightbox.js' ), array(), $version, true );
			wp_script_add_data( 'jn-lightbox', 'strategy', 'defer' );
		}
	},
	20
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'wp-emoji-styles' );
		wp_dequeue_style( 'wp-img-auto-sizes' );
		wp_dequeue_script( 'wp-embed' );
	},
	100
);

add_action(
	'init',
	function () {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'print_emoji_styles' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	}
);

add_filter(
	'wp_resource_hints',
	function ( $urls, $relation ) {
		if ( 'preconnect' === $relation ) {
			$urls[] = jn_remote_base();
		}
		if ( 'dns-prefetch' === $relation ) {
			$urls[] = 'https://jaunradesnams.lv';
		}
		return $urls;
	},
	10,
	2
);

add_action(
	'wp_head',
	function () {
		$fonts = array(
			jn_asset( 'fonts/manrope-latin-ext.woff2' ),
			jn_asset( 'fonts/sora-latin-ext.woff2' ),
		);

		foreach ( $fonts as $font ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( $font )
			);
		}

		if ( is_front_page() ) {
			printf(
				'<link rel="preload" as="image" href="%1$s" imagesrcset="%2$s" imagesizes="100vw" fetchpriority="high">' . "\n",
				esc_url( jn_asset( 'img/hero-800.jpg' ) ),
				esc_attr(
					implode(
						', ',
						array(
							jn_asset( 'img/hero-480.jpg' ) . ' 480w',
							jn_asset( 'img/hero-800.jpg' ) . ' 800w',
							jn_asset( 'img/hero-1200.jpg' ) . ' 1200w',
						)
					)
				)
			);
		}

		printf( '<link rel="icon" href="%s" sizes="any">' . "\n", esc_url( jn_asset( 'favicon.ico' ) ) );
	},
	2
);

add_filter(
	'language_attributes',
	function () {
		return 'lang="lv"';
	}
);
