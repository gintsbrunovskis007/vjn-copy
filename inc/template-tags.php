<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jn_breadcrumb_render( $items, $class = 'breadcrumb' ) {
	if ( empty( $items ) ) {
		return;
	}

	echo '<nav class="' . esc_attr( $class ) . '" aria-label="Atrašanās vieta">';
	$last = count( $items ) - 1;
	foreach ( $items as $i => $item ) {
		if ( $i > 0 ) {
			echo '<span aria-hidden="true">/</span>';
		}
		if ( $i < $last && ! empty( $item['url'] ) ) {
			echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['name'] ) . '</a>';
		} else {
			echo '<span aria-current="page">' . esc_html( $item['name'] ) . '</span>';
		}
	}
	echo '</nav>';
}

function jn_pagination_render( $current, $total, $url_cb, $show_meta = true ) {
	$current = max( 1, (int) $current );
	$total   = max( 1, (int) $total );
	if ( $total <= 1 ) {
		return;
	}

	$range = jn_pagination_range( $current, $total );
	?>
	<nav class="pagination" aria-label="Lapu navigācija">
		<?php if ( $current > 1 ) : ?>
			<a class="pagination__arrow" href="<?php echo esc_url( call_user_func( $url_cb, $current - 1 ) ); ?>" rel="prev" aria-label="Iepriekšējā lapa">
				<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M15 5 8 12l7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</a>
		<?php else : ?>
			<span class="pagination__arrow is-disabled" aria-hidden="true">
				<svg viewBox="0 0 24 24" width="20" height="20" focusable="false"><path d="M15 5 8 12l7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</span>
		<?php endif; ?>

		<?php foreach ( $range as $p ) : ?>
			<?php if ( -1 === $p ) : ?>
				<span class="pagination__gap" aria-hidden="true">…</span>
			<?php elseif ( $p === $current ) : ?>
				<span class="pagination__num is-current" aria-current="page">
					<?php echo esc_html( $p ); ?>
					<span class="screen-reader-text">(pašreizējā lapa)</span>
				</span>
			<?php else : ?>
				<a class="pagination__num" href="<?php echo esc_url( call_user_func( $url_cb, $p ) ); ?>" aria-label="Doties uz lapu <?php echo esc_attr( $p ); ?>">
					<?php echo esc_html( $p ); ?>
				</a>
			<?php endif; ?>
		<?php endforeach; ?>

		<?php if ( $current < $total ) : ?>
			<a class="pagination__arrow" href="<?php echo esc_url( call_user_func( $url_cb, $current + 1 ) ); ?>" rel="next" aria-label="Nākamā lapa">
				<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</a>
		<?php else : ?>
			<span class="pagination__arrow is-disabled" aria-hidden="true">
				<svg viewBox="0 0 24 24" width="20" height="20" focusable="false"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</span>
		<?php endif; ?>
	</nav>

	<?php if ( $show_meta ) : ?>
		<p class="pagination__meta">Lapa <?php echo esc_html( $current ); ?> no <?php echo esc_html( $total ); ?></p>
	<?php endif; ?>
	<?php
}

function jn_image_render( $media, $args = array() ) {
	$media = is_array( $media ) ? $media : array();
	$src   = $media['src'] ?? ( $media['url'] ?? '' );
	if ( ! $src ) {
		return;
	}

	$args = wp_parse_args(
		$args,
		array(
			'class'           => '',
			'alt'             => $media['alt'] ?? '',
			'loading'         => 'lazy',
			'decoding'        => 'async',
			'fetchpriority'   => '',
			'sizes'           => '',
			'referrerpolicy'  => 'no-referrer',
		)
	);

	$attrs = array(
		'src'   => esc_url( $src ),
		'alt'   => esc_attr( $args['alt'] ),
		'class' => $args['class'] ? esc_attr( $args['class'] ) : '',
	);

	if ( ! empty( $media['srcset'] ) ) {
		$attrs['srcset'] = esc_attr( $media['srcset'] );
		if ( $args['sizes'] ) {
			$attrs['sizes'] = esc_attr( $args['sizes'] );
		}
	}

	if ( ! empty( $media['width'] ) ) {
		$attrs['width'] = (int) $media['width'];
	}
	if ( ! empty( $media['height'] ) ) {
		$attrs['height'] = (int) $media['height'];
	}

	$attrs['loading'] = esc_attr( $args['loading'] );
	$attrs['decoding'] = esc_attr( $args['decoding'] );

	if ( $args['fetchpriority'] ) {
		$attrs['fetchpriority'] = esc_attr( $args['fetchpriority'] );
	}
	if ( $args['referrerpolicy'] ) {
		$attrs['referrerpolicy'] = esc_attr( $args['referrerpolicy'] );
	}

	$out = '<img';
	foreach ( $attrs as $name => $value ) {
		if ( '' === $value || null === $value ) {
			continue;
		}
		$out .= ' ' . $name . '="' . $value . '"';
	}
	$out .= '>';

	echo $out;
}

function jn_post_card( $post, $args = array() ) {
	$args = wp_parse_args( $args, array( 'heading' => 'h2' ) );
	$tag  = in_array( $args['heading'], array( 'h2', 'h3', 'h4' ), true ) ? $args['heading'] : 'h2';

	$title   = wp_strip_all_tags( $post['title']['rendered'] ?? '' );
	$link    = $post['link'] ?? '#';
	$date    = $post['date'] ?? '';
	$excerpt = wp_trim_words( wp_strip_all_tags( $post['excerpt']['rendered'] ?? '' ), 24 );
	$media   = $post['media'] ?? array();
	?>
	<article class="jn-post">
		<?php if ( ! empty( $media['src'] ) || ! empty( $media['url'] ) ) : ?>
			<div class="jn-post__thumb">
				<?php jn_image_render( $media, array( 'sizes' => '(max-width: 720px) 100vw, 33vw' ) ); ?>
			</div>
		<?php endif; ?>

		<div class="jn-post__body">
			<?php if ( $date ) : ?>
				<p class="jn-post__date">
					<time datetime="<?php echo esc_attr( $date ); ?>"><?php echo esc_html( jn_date_lv( $date ) ); ?></time>
				</p>
			<?php endif; ?>

			<<?php echo esc_attr( $tag ); ?> class="jn-post__title">
				<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a>
			</<?php echo esc_attr( $tag ); ?>>

			<?php if ( $excerpt ) : ?>
				<p class="jn-post__excerpt"><?php echo esc_html( $excerpt ); ?></p>
			<?php endif; ?>

			<span class="jn-post__more" aria-hidden="true"><span class="jn-post__more-label">Lasīt vairāk</span></span>
		</div>
	</article>
	<?php
}

function jn_gallery_parse_albums_html( $html ) {
	$html = (string) $html;
	if ( '' === $html || false === strpos( $html, 'ngg-album-compact' ) ) {
		return array();
	}

	if ( ! preg_match_all(
		'#<div\s+class="[^"]*ngg-album-compact[^"]*"[^>]*>(.*?)<h4[^>]*>(.*?)</h4>.*?<strong>(\d+)</strong>#is',
		$html,
		$matches,
		PREG_SET_ORDER
	) ) {
		return array();
	}

	$albums = array();
	foreach ( $matches as $m ) {
		$block = $m[1];
		$title = trim( wp_strip_all_tags( $m[2] ) );
		$count = (int) $m[3];
		$link  = '';
		$thumb = '';

		if ( preg_match( '#<a[^>]+href=[\'"]([^\'"]+)[\'"]#i', $block, $lm ) ) {
			$link = esc_url_raw( html_entity_decode( $lm[1], ENT_QUOTES, 'UTF-8' ) );
		}
		if ( preg_match( '#<img[^>]+(?:data-src|src)=[\'"]([^\'"]+)[\'"]#i', $block, $im ) ) {
			$thumb = esc_url_raw( html_entity_decode( $im[1], ENT_QUOTES, 'UTF-8' ) );
		}
		if ( ! $link || ! $title || ! $thumb ) {
			continue;
		}

		$albums[] = array(
			'title' => $title,
			'link'  => $link,
			'thumb' => $thumb,
			'count' => $count,
		);
	}

	return $albums;
}

function jn_gallery_load_albums( $source = 'main' ) {
	$source = 'planetarium' === $source ? 'planetarium' : 'main';
	$key    = 'jn_gallery_albums_' . $source . '_v4';

	$cached = get_transient( $key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$albums = array();

	if ( 'planetarium' === $source ) {
		$html = jn_remote_page_html( 'planetarijs-galerija' );
		$albums = jn_gallery_parse_albums_html( $html );
	} else {
		$pages = jn_get_pages_by_slugs( array( 'galerija' ), 'slug,content' );
		$html  = $pages['galerija']['content']['rendered'] ?? '';
		$albums = jn_gallery_parse_albums_html( $html );

		if ( empty( $albums ) ) {
			$response = wp_remote_get(
				jn_remote_base() . '/jaunrades-nams/galerija/',
				array( 'timeout' => 20, 'user-agent' => 'Mozilla/5.0 (compatible; VJN/1.0)' )
			);
			if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
				$albums = jn_gallery_parse_albums_html( wp_remote_retrieve_body( $response ) );
			}
		}
	}

	if ( ! empty( $albums ) ) {
		set_transient( $key, $albums, 15 * MINUTE_IN_SECONDS );
	}

	return $albums;
}

function jn_gallery_validate_album_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}

	$parsed = wp_parse_url( $url );
	if ( empty( $parsed['host'] ) || empty( $parsed['path'] ) ) {
		return '';
	}

	if ( 'jaunradesnams.lv' !== strtolower( $parsed['host'] ) ) {
		return '';
	}

	if ( 0 !== strpos( $parsed['scheme'] ?? '', 'http' ) ) {
		return '';
	}

	if ( false === strpos( $parsed['path'], '/galerija/' ) ) {
		return '';
	}

	return esc_url_raw( $url );
}

function jn_gallery_load_album_images( $album_url ) {
	$album_url = jn_gallery_validate_album_url( $album_url );
	if ( ! $album_url ) {
		return array();
	}

	$key    = 'jn_gallery_images_' . md5( $album_url );
	$cached = get_transient( $key );
	if ( is_array( $cached ) && ! empty( $cached ) ) {
		return $cached;
	}

	$response = wp_remote_get(
		$album_url,
		array( 'timeout' => 25, 'user-agent' => 'Mozilla/5.0 (compatible; VJN/1.0)' )
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return array();
	}

	$html = wp_remote_retrieve_body( $response );

	if ( preg_match( '#<div[^>]*class="[^"]*ngg-galleryoverview[^"]*"[^>]*>(.*?)<div[^>]*class="[^"]*ngg-navigation#is', $html, $gm ) ) {
		$html = $gm[1];
	}

	$images = array();

	if ( preg_match_all(
		'#<a\s[^>]*href=["\']([^"\']+\.(?:jpe?g|png|gif|webp))["\'][^>]*>#i',
		$html,
		$anchors,
		PREG_OFFSET_CAPTURE
	) ) {
		$seen  = array();
		$total = count( $anchors[0] );

		for ( $i = 0; $i < $total; $i++ ) {
			$a_html = $anchors[0][ $i ][0];
			$a_pos  = $anchors[0][ $i ][1];
			$full   = esc_url_raw( html_entity_decode( $anchors[1][ $i ][0], ENT_QUOTES, 'UTF-8' ) );

			if ( ! $full || false === strpos( $full, '/gallery/' ) || isset( $seen[ $full ] ) ) {
				continue;
			}
			$seen[ $full ] = true;

			$window_end = $total > $i + 1 ? $anchors[0][ $i + 1 ][1] : strlen( $html );
			$window     = substr( $html, $a_pos + strlen( $a_html ), $window_end - ( $a_pos + strlen( $a_html ) ) );

			if ( ! preg_match( '#<img[^>]+(?:data-src|src)=["\']([^"\']+)["\']#i', $window, $im ) ) {
				continue;
			}

			$thumb = esc_url_raw( html_entity_decode( $im[1], ENT_QUOTES, 'UTF-8' ) );
			if ( ! $thumb ) {
				continue;
			}

			$mid = preg_replace( '#-(\d+)x(\d+)x(\d+)-#', '-640x480x100-', $thumb );

			$images[] = array(
				'thumb' => $mid ?: $thumb,
				'full'  => $full,
			);
		}
	}

	if ( ! empty( $images ) ) {
		set_transient( $key, $images, 15 * MINUTE_IN_SECONDS );
	}

	return $images;
}

function jn_gallery_album_title( $album_url, $albums = array() ) {
	foreach ( (array) $albums as $album ) {
		if ( isset( $album['link'] ) && $album['link'] === $album_url ) {
			return $album['title'];
		}
	}

	$slug = basename( rtrim( (string) wp_parse_url( $album_url, PHP_URL_PATH ), '/' ) );
	return ucwords( str_replace( array( '-', '_' ), ' ', urldecode( $slug ) ) );
}

function jn_lightbox_markup() {
	?>
	<div class="jn-lightbox" id="jn-lightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Galerijas slaidrāde">
		<button class="jn-lightbox__close" type="button" aria-label="Aizvērt">×</button>
		<button class="jn-lightbox__nav jn-lightbox__nav--prev" type="button" aria-label="Iepriekšējais">
			<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M15 5 8 12l7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
		</button>
		<button class="jn-lightbox__nav jn-lightbox__nav--next" type="button" aria-label="Nākamais">
			<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
		</button>
		<figure class="jn-lightbox__figure">
			<img class="jn-lightbox__img" src="" alt="">
		</figure>
		<div class="jn-lightbox__counter" aria-live="polite"></div>
	</div>
	<?php
}
