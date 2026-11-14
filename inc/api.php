<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jn_api_request( $path, $query = array(), $args = array() ) {
	$url = add_query_arg( $query, jn_api_base() . ltrim( $path, '/' ) );

	$args = wp_parse_args(
		$args,
		array(
			'timeout'    => 15,
			'user-agent' => 'VJN/1.0; ' . home_url( '/' ),
			'headers'    => array( 'Accept' => 'application/json' ),
		)
	);

	$response = wp_remote_get( $url, $args );

	if ( is_wp_error( $response ) ) {
		return array(
			'data'        => null,
			'code'        => 0,
			'total_pages' => 1,
			'error'       => $response->get_error_message(),
			'url'         => $url,
		);
	}

	$code  = (int) wp_remote_retrieve_response_code( $response );
	$body  = json_decode( wp_remote_retrieve_body( $response ), true );
	$total = (int) wp_remote_retrieve_header( $response, 'x-wp-totalpages' );

	return array(
		'data'        => is_array( $body ) ? $body : null,
		'code'        => $code,
		'total_pages' => max( 1, $total ),
		'error'       => ( $code >= 200 && $code < 300 && is_array( $body ) ) ? '' : 'http_' . $code,
		'url'         => $url,
	);
}

function jn_api_get( $path, $query = array() ) {
	$result = jn_api_request( $path, $query );
	return $result['data'];
}

function jn_cache_remember( $key, $ttl, $callback ) {
	$cached = get_transient( $key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$data = call_user_func( $callback );
	if ( is_array( $data ) ) {
		set_transient( $key, $data, (int) $ttl );
		update_option( 'jn_lg_' . $key, $data, false );
		return $data;
	}

	$last = get_option( 'jn_lg_' . $key );
	return is_array( $last ) ? $last : null;
}

function jn_api_cache( $key, $path, $query = array(), $ttl = 300 ) {
	return jn_cache_remember(
		$key,
		$ttl,
		function () use ( $path, $query ) {
			return jn_api_get( $path, $query );
		}
	);
}

function jn_clean_html( $html ) {
	$html = (string) $html;
	if ( '' === $html ) {
		return '';
	}

	$html = preg_replace( '#<(script|style)\b[^>]*>.*?</\1>#is', '', $html );
	$html = preg_replace( '/\son\w+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html );
	$html = preg_replace( '/(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1="#"', $html );
	$html = preg_replace( '/<p>\s*(?:&nbsp;|\s)*<\/p>/i', '', $html );

	return wp_kses( $html, jn_allowed_html() );
}

function jn_allowed_html() {
	$allowed = wp_kses_allowed_html( 'post' );

	$allowed['iframe'] = array(
		'src'             => true,
		'width'           => true,
		'height'          => true,
		'title'           => true,
		'allow'           => true,
		'allowfullscreen' => true,
		'frameborder'     => true,
		'loading'         => true,
		'referrerpolicy'  => true,
	);

	$allowed['time'] = array( 'datetime' => true );
	$allowed['svg']  = array(
		'viewbox'     => true,
		'width'       => true,
		'height'      => true,
		'fill'        => true,
		'xmlns'       => true,
		'aria-hidden' => true,
		'class'       => true,
	);
	$allowed['path'] = array(
		'd'               => true,
		'fill'            => true,
		'stroke'          => true,
		'stroke-width'    => true,
		'stroke-linecap'  => true,
		'stroke-linejoin' => true,
	);

	return $allowed;
}

function jn_extract_first_image( $html ) {
	if ( ! preg_match( '/<img\b[^>]*>/i', (string) $html, $tag ) ) {
		return array();
	}

	$url = '';
	$alt = '';

	if ( preg_match( '/\b(?:data-src|src)\s*=\s*(["\'])(.*?)\1/i', $tag[0], $m ) ) {
		$url = html_entity_decode( $m[2], ENT_QUOTES, 'UTF-8' );
	}
	if ( preg_match( '/\balt\s*=\s*(["\'])(.*?)\1/i', $tag[0], $m ) ) {
		$alt = html_entity_decode( $m[2], ENT_QUOTES, 'UTF-8' );
	}

	if ( ! $url ) {
		return array();
	}

	return array(
		'url'     => esc_url_raw( $url ),
		'alt'     => $alt,
		'width'   => 0,
		'height'  => 0,
		'src'     => esc_url_raw( $url ),
		'srcset'  => '',
	);
}

function jn_build_srcset( $sizes ) {
	$by_width = array();
	$preferred = array( 'medium', 'medium_large', 'large', 'blog-medium', 'blog-large', 'full' );

	foreach ( $preferred as $name ) {
		if ( empty( $sizes[ $name ]['url'] ) || empty( $sizes[ $name ]['width'] ) ) {
			continue;
		}
		$width = (int) $sizes[ $name ]['width'];
		$by_width[ $width ] = array(
			'url'     => $sizes[ $name ]['url'],
			'width'   => $width,
			'height'  => (int) ( $sizes[ $name ]['height'] ?? 0 ),
			'name'    => $name,
		);
	}

	ksort( $by_width );

	$parts = array();
	foreach ( $by_width as $item ) {
		$parts[] = $item['url'] . ' ' . $item['width'] . 'w';
	}

	return array(
		'srcset' => implode( ', ', $parts ),
		'sizes'  => $by_width,
	);
}

function jn_remote_featured_media( $post ) {
	$media = array(
		'url'    => '',
		'src'    => '',
		'width'  => 0,
		'height' => 0,
		'alt'    => '',
		'srcset' => '',
	);

	$embedded = $post['_embedded']['wp:featuredmedia'][0] ?? null;

	if ( is_array( $embedded ) && empty( $embedded['code'] ) ) {
		$url = $embedded['source_url'] ?? '';
		$alt = $embedded['alt_text'] ?? '';
		$w   = (int) ( $embedded['media_details']['width'] ?? 0 );
		$h   = (int) ( $embedded['media_details']['height'] ?? 0 );
		$srcset = jn_build_srcset( $embedded['media_details']['sizes'] ?? array() );

		if ( $url ) {
			$media['url']    = esc_url_raw( $url );
			$media['width']  = $w;
			$media['height'] = $h;
			$media['alt']    = $alt;
			$media['srcset'] = $srcset['srcset'];

			$best = $url;
			$best_w = $w;
			$best_h = $h;
			foreach ( $srcset['sizes'] as $size ) {
				if ( $size['width'] >= 480 && $size['width'] <= 1200 ) {
					$best   = $size['url'];
					$best_w = $size['width'];
					$best_h = $size['height'];
				}
			}

			$media['src']    = esc_url_raw( $best );
			$media['width']  = $best_w;
			$media['height'] = $best_h;

			if ( ! $media['alt'] ) {
				$media['alt'] = jn_trim_text( $post['title']['rendered'] ?? '', 120 );
			}

			return $media;
		}
	}

	$first = jn_extract_first_image( $post['content']['rendered'] ?? '' );
	if ( ! empty( $first['url'] ) ) {
		$first['alt'] = $first['alt'] ?: jn_trim_text( $post['title']['rendered'] ?? '', 120 );
		return $first;
	}

	return $media;
}

function jn_local_featured_media( $post_id ) {
	$media = array(
		'url'    => '',
		'src'    => '',
		'width'  => 0,
		'height' => 0,
		'alt'    => '',
		'srcset' => '',
	);

	$thumb_id = get_post_thumbnail_id( $post_id );
	if ( ! $thumb_id ) {
		return $media;
	}

	$src = wp_get_attachment_image_src( $thumb_id, 'large' );
	if ( ! $src ) {
		return $media;
	}

	$media['url']    = esc_url_raw( $src[0] );
	$media['src']    = esc_url_raw( $src[0] );
	$media['width']  = (int) $src[1];
	$media['height'] = (int) $src[2];
	$media['alt']    = (string) get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
	$media['srcset'] = (string) wp_get_attachment_image_srcset( $thumb_id, 'large' );

	if ( ! $media['alt'] ) {
		$media['alt'] = jn_trim_text( get_the_title( $post_id ), 120 );
	}

	return $media;
}

function jn_normalize_remote_post( $post ) {
	$slug = sanitize_title( $post['slug'] ?? '' );

	return array(
		'id'          => (int) ( $post['id'] ?? 0 ),
		'slug'        => $slug,
		'title'       => array( 'rendered' => (string) ( $post['title']['rendered'] ?? '' ) ),
		'excerpt'     => array( 'rendered' => (string) ( $post['excerpt']['rendered'] ?? '' ) ),
		'content'     => array( 'rendered' => (string) ( $post['content']['rendered'] ?? '' ) ),
		'date'        => (string) ( $post['date'] ?? '' ),
		'modified'    => (string) ( $post['modified'] ?? '' ),
		'link'        => $slug ? jn_single_url_post( $slug ) : esc_url_raw( $post['link'] ?? '#' ),
		'remote_link' => esc_url_raw( $post['link'] ?? '' ),
		'categories'  => array_map( 'intval', (array) ( $post['categories'] ?? array() ) ),
		'media'       => jn_remote_featured_media( $post ),
		'is_local'    => false,
	);
}

function jn_normalize_local_post( $post ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return null;
	}

	$content = (string) $post->post_content;
	if ( '' !== trim( $content ) ) {
		$content = apply_filters( 'the_content', $content );
	}

	$media = jn_local_featured_media( $post->ID );
	if ( empty( $media['url'] ) ) {
		$media = jn_extract_first_image( $content );
	}

	$excerpt = $post->post_excerpt;
	if ( '' === trim( (string) $excerpt ) ) {
		$excerpt = wp_trim_words( wp_strip_all_tags( $content ), 32 );
	}

	return array(
		'id'          => (int) $post->ID,
		'slug'        => $post->post_name,
		'title'       => array( 'rendered' => get_the_title( $post ) ),
		'excerpt'     => array( 'rendered' => wpautop( $excerpt ) ),
		'content'     => array( 'rendered' => $content ),
		'date'        => get_post_time( 'c', true, $post ),
		'modified'    => get_post_modified_time( 'c', true, $post ),
		'link'        => get_permalink( $post ),
		'remote_link' => '',
		'categories'  => wp_get_post_categories( $post->ID ),
		'media'       => $media,
		'is_local'    => true,
	);
}

function jn_get_posts( $page = 1, $per_page = 9, $category = 8 ) {
	$page     = max( 1, (int) $page );
	$per_page = max( 1, min( 48, (int) $per_page ) );
	$category = (int) $category;

	$term = get_category( $category );
	if ( $term && ! is_wp_error( $term ) && $term->count > 0 ) {
		$q = new WP_Query(
			array(
				'cat'                 => $category,
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => $per_page,
				'paged'               => $page,
				'ignore_sticky_posts' => true,
			)
		);

		$total = max( 1, (int) $q->max_num_pages );

		if ( $q->have_posts() ) {
			$posts = array();
			foreach ( $q->posts as $local_post ) {
				$normalized = jn_normalize_local_post( $local_post );
				if ( $normalized ) {
					$posts[] = $normalized;
				}
			}
			wp_reset_postdata();

			return array(
				'posts'       => $posts,
				'total_pages' => $total,
				'error'       => '',
				'source'      => 'local',
			);
		}
		wp_reset_postdata();

		if ( $page > $total ) {
			return array( 'posts' => array(), 'total_pages' => $total, 'error' => 'out_of_range', 'source' => 'local' );
		}
	}

	$key = 'jn_posts_' . $category . '_' . $page . '_' . $per_page . '_v3';

	$cached = get_transient( $key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$result = jn_api_request(
		'posts',
		array(
			'categories' => $category,
			'per_page'   => $per_page,
			'page'       => $page,
			'_embed'     => 1,
		)
	);

	if ( 400 === $result['code'] && $page > 1 ) {
		return array(
			'posts'       => array(),
			'total_pages' => $result['total_pages'],
			'error'       => 'out_of_range',
			'source'      => 'remote',
		);
	}

	if ( empty( $result['data'] ) || ! is_array( $result['data'] ) ) {
		$last = get_option( 'jn_lg_' . $key );
		if ( is_array( $last ) ) {
			$last['error'] = 'stale';
			return $last;
		}

		return array(
			'posts'       => array(),
			'total_pages' => 1,
			'error'       => 'api_error',
			'source'      => 'remote',
		);
	}

	$posts = array();
	foreach ( $result['data'] as $remote_post ) {
		if ( is_array( $remote_post ) ) {
			$posts[] = jn_normalize_remote_post( $remote_post );
		}
	}

	$data = array(
		'posts'       => $posts,
		'total_pages' => max( 1, (int) $result['total_pages'] ),
		'error'       => '',
		'source'      => 'remote',
	);

	set_transient( $key, $data, 5 * MINUTE_IN_SECONDS );
	update_option( 'jn_lg_' . $key, $data, false );

	return $data;
}

function jn_local_page_payload( $post ) {
	$content = (string) $post->post_content;
	if ( '' !== trim( $content ) ) {
		$content = apply_filters( 'the_content', $content );
	}

	return array(
		'id'       => (int) $post->ID,
		'slug'     => $post->post_name,
		'title'    => array( 'rendered' => get_the_title( $post ) ),
		'link'     => get_permalink( $post ),
		'content'  => array( 'rendered' => $content ),
		'modified' => get_post_modified_time( 'c', true, $post ),
		'is_local' => true,
	);
}

function jn_get_page_by_slug( $slug ) {
	$slug = sanitize_title( $slug );
	if ( '' === $slug ) {
		return null;
	}

	$local = get_page_by_path( $slug );
	if ( $local && '' !== trim( (string) $local->post_content ) ) {
		return jn_local_page_payload( $local );
	}

	$data = jn_api_cache(
		'jn_page_' . md5( $slug ),
		'pages',
		array(
			'slug'     => $slug,
			'per_page' => 1,
			'_fields'  => 'id,slug,title,link,content,modified',
		),
		15 * MINUTE_IN_SECONDS
	);

	return ( is_array( $data ) && ! empty( $data[0] ) && is_array( $data[0] ) ) ? $data[0] : null;
}

function jn_get_page_by_id( $id ) {
	$id = (int) $id;
	if ( $id <= 0 ) {
		return null;
	}

	return jn_api_cache(
		'jn_page_id_' . $id,
		'pages/' . $id,
		array( '_fields' => 'id,slug,title,link,content,modified' ),
		15 * MINUTE_IN_SECONDS
	);
}

function jn_get_remote_page_data( $slug ) {
	$slug = sanitize_title( $slug );
	if ( '' === $slug ) {
		return null;
	}

	$local = get_page_by_path( $slug );
	if ( $local && '' !== trim( (string) $local->post_content ) ) {
		return jn_local_page_payload( $local );
	}

	$id = jn_remote_page_id( $slug );
	if ( $id ) {
		$data = jn_get_page_by_id( $id );
		if ( is_array( $data ) ) {
			return $data;
		}
	}

	return jn_get_page_by_slug( $slug );
}

function jn_remote_page_html( $slug ) {
	$data = jn_get_remote_page_data( $slug );
	return is_array( $data ) ? (string) ( $data['content']['rendered'] ?? '' ) : '';
}

function jn_get_pages_by_slugs( $slugs, $fields = 'slug,title,link,content,modified' ) {
	$slugs = array_values( array_unique( array_filter( array_map( 'sanitize_title', (array) $slugs ) ) ) );
	$out   = array();
	$missing = array();

	foreach ( $slugs as $slug ) {
		$local = get_page_by_path( $slug );
		if ( $local && '' !== trim( (string) $local->post_content ) ) {
			$payload = jn_local_page_payload( $local );
			if ( 'slug,content' === $fields ) {
				$payload = array( 'slug' => $payload['slug'], 'content' => $payload['content'] );
			}
			$out[ $slug ] = $payload;
		} else {
			$missing[] = $slug;
		}
	}

	if ( empty( $missing ) ) {
		return $out;
	}

	foreach ( array_chunk( $missing, 20 ) as $chunk ) {
		$key = 'jn_pages_' . md5( implode( ',', $chunk ) . '|' . $fields );

		$chunk_out = jn_cache_remember(
			$key,
			15 * MINUTE_IN_SECONDS,
			function () use ( $chunk, $fields ) {
				$data  = jn_api_get(
					'pages',
					array(
						'slug'     => implode( ',', $chunk ),
						'per_page' => 100,
						'_fields'  => $fields,
					)
				);
				$out = array();
				if ( is_array( $data ) ) {
					foreach ( $data as $page ) {
						if ( ! empty( $page['slug'] ) ) {
							$out[ $page['slug'] ] = $page;
						}
					}
				}
				return $out;
			}
		);

		if ( is_array( $chunk_out ) ) {
			$out += $chunk_out;
		}
	}

	return $out;
}

function jn_get_single_post( $slug ) {
	$slug = sanitize_title( $slug );
	if ( '' === $slug ) {
		return null;
	}

	$key    = 'jn_single_' . md5( $slug );
	$cached = get_transient( $key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$local = get_posts(
		array(
			'name'        => $slug,
			'post_type'   => array( 'post', 'page' ),
			'post_status' => 'publish',
			'numberposts' => 1,
		)
	);

	if ( ! empty( $local[0] ) && '' !== trim( (string) $local[0]->post_content ) ) {
		$post = jn_normalize_local_post( $local[0] );
		if ( $post ) {
			set_transient( $key, $post, 5 * MINUTE_IN_SECONDS );
			return $post;
		}
	}

	$result = jn_api_request(
		'posts',
		array(
			'slug'     => $slug,
			'per_page' => 1,
			'_embed'   => 1,
		)
	);

	if ( ! empty( $result['data'][0] ) && is_array( $result['data'][0] ) ) {
		$post = jn_normalize_remote_post( $result['data'][0] );
		set_transient( $key, $post, 15 * MINUTE_IN_SECONDS );
		update_option( 'jn_lg_' . $key, $post, false );
		return $post;
	}

	$page = jn_get_page_by_slug( $slug );
	if ( is_array( $page ) ) {
		$content = (string) ( $page['content']['rendered'] ?? '' );
		$post    = array(
			'id'          => (int) ( $page['id'] ?? 0 ),
			'slug'        => $slug,
			'title'       => array( 'rendered' => (string) ( $page['title']['rendered'] ?? '' ) ),
			'excerpt'     => array( 'rendered' => '' ),
			'content'     => array( 'rendered' => $content ),
			'date'        => (string) ( $page['modified'] ?? '' ),
			'modified'    => (string) ( $page['modified'] ?? '' ),
			'link'        => jn_single_url_post( $slug ),
			'remote_link' => esc_url_raw( $page['link'] ?? '' ),
			'categories'  => array(),
			'media'       => jn_extract_first_image( $content ),
			'is_local'    => ! empty( $page['is_local'] ),
		);

		if ( ! empty( $post['media']['url'] ) && empty( $post['media']['alt'] ) ) {
			$post['media']['alt'] = jn_trim_text( $post['title']['rendered'], 120 );
		}

		set_transient( $key, $post, 15 * MINUTE_IN_SECONDS );
		return $post;
	}

	$last = get_option( 'jn_lg_' . $key );
	return is_array( $last ) ? $last : null;
}
