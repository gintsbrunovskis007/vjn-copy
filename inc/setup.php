<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'ventspils-jaunrades-nams', get_template_directory() . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support(
			'html5',
			array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
		);
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 121,
				'width'       => 207,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		register_nav_menus(
			array(
				'primary'    => 'Galvenā navigācija',
				'planetarium' => 'Planetārija navigācija',
				'footer'     => 'Kājenes saites',
			)
		);

		add_image_size( 'jn-card', 640, 420, true );
	}
);

add_action(
	'after_setup_theme',
	function () {
		$GLOBALS['content_width'] = 1200;
	},
	0
);
