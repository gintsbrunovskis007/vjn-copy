<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'JN_TEACHER_PASSWORD_HASH' ) ) {
	define( 'JN_TEACHER_PASSWORD_HASH', '$2y$10$tvMOafRmqIpIfh/R5VqrjOnAjtzJXWF7I/SSPxlsa7ObOujt1mg0G' );
}

if ( ! defined( 'JN_TEACHER_TTL' ) ) {
	define( 'JN_TEACHER_TTL', 8 * HOUR_IN_SECONDS );
}

function jn_teacher_cookie_name() {
	return 'jn_teacher_auth';
}

function jn_teacher_sign( $expires ) {
	return $expires . '.' . hash_hmac( 'sha256', 'jn_teacher_auth|' . $expires, wp_salt( 'auth' ) );
}

function jn_teacher_verify() {
	if ( empty( $_COOKIE[ jn_teacher_cookie_name() ] ) ) {
		return false;
	}

	$raw = (string) $_COOKIE[ jn_teacher_cookie_name() ];
	if ( ! preg_match( '/^(\d{10})\.([a-f0-9]{64})$/', $raw, $m ) ) {
		return false;
	}
	if ( (int) $m[1] < time() ) {
		return false;
	}

	$expected = hash_hmac( 'sha256', 'jn_teacher_auth|' . $m[1], wp_salt( 'auth' ) );
	return hash_equals( $expected, $m[2] );
}

function jn_teacher_grant() {
	$expires = time() + (int) JN_TEACHER_TTL;
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

function jn_teacher_revoke() {
	setcookie(
		jn_teacher_cookie_name(),
		'',
		array(
			'expires'  => time() - 3600,
			'path'     => COOKIEPATH ?: '/',
			'domain'   => COOKIE_DOMAIN,
			'secure'   => is_ssl(),
			'httponly' => true,
			'samesite' => 'Lax',
		)
	);
	unset( $_COOKIE[ jn_teacher_cookie_name() ] );
}

function jn_teacher_rate_key() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
	return 'jn_teacher_rl_' . md5( $ip );
}

function jn_teacher_password_verify( $password ) {
	if ( defined( 'JN_TEACHER_PASSWORD' ) && JN_TEACHER_PASSWORD ) {
		return hash_equals( (string) JN_TEACHER_PASSWORD, (string) $password );
	}
	return password_verify( (string) $password, (string) JN_TEACHER_PASSWORD_HASH );
}

function jn_teacher_process_login() {
	$is_auth   = jn_teacher_verify();
	$error_msg = '';

	if ( isset( $_POST['jn_login'] ) ) {
		check_admin_referer( 'jn_teacher_login', 'jn_teacher_nonce' );

		$attempts = (int) get_transient( jn_teacher_rate_key() );

		if ( $attempts >= 5 ) {
			$error_msg = 'Pārāk daudz mēģinājumu. Lūdzu, uzgaidiet 15 minūtes.';
		} elseif ( jn_teacher_password_verify( wp_unslash( $_POST['jn_password'] ?? '' ) ) ) {
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
		$nonce = isset( $_GET['_jn_nonce'] ) ? (string) $_GET['_jn_nonce'] : '';
		if ( wp_verify_nonce( $nonce, 'jn_teacher_logout' ) ) {
			jn_teacher_revoke();
			wp_safe_redirect( get_permalink() . '#pedagogiem' );
			exit;
		}
	}

	return array( 'auth' => $is_auth, 'error' => $error_msg );
}

add_action(
	'send_headers',
	function () {
		if ( headers_sent() ) {
			return;
		}

		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
		header( 'Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=()' );

		if ( is_ssl() ) {
			header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
		}
	}
);

add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'pings_open', '__return_false' );

add_filter(
	'wp_headers',
	function ( $headers ) {
		unset( $headers['X-Pingback'] );
		return $headers;
	}
);

add_action(
	'template_redirect',
	function () {
		if ( is_author() ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	}
);
