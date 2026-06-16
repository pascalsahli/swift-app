<?php
/**
 * Claudia Editorial — theme functions.
 *
 * @package Claudia_Editorial
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

if ( ! function_exists( 'claudia_editorial_setup' ) ) {
	/**
	 * Register theme support and navigation menus.
	 */
	function claudia_editorial_setup() {
		load_theme_textdomain( 'claudia-editorial', get_template_directory() . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'responsive-embeds' );
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 60,
				'width'       => 240,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		register_nav_menus(
			array(
				'primary' => __( 'Hauptmenü', 'claudia-editorial' ),
				'footer'  => __( 'Footer-Menü', 'claudia-editorial' ),
			)
		);

		// Jetpack compatibility: responsive videos and content options.
		add_theme_support( 'jetpack-responsive-videos' );
		add_theme_support(
			'jetpack-content-options',
			array(
				'blog-display' => 'content',
				'post-details' => array(
					'stylesheet' => 'claudia-editorial-main',
					'date'       => '.meta time',
				),
			)
		);

		// Nicer excerpts.
		add_filter( 'excerpt_length', function () { return 26; } );
		add_filter( 'excerpt_more', function () { return '…'; } );
	}
}
add_action( 'after_setup_theme', 'claudia_editorial_setup' );

// E-mail subscription / newsletter feature.
require get_template_directory() . '/inc/newsletter.php';

/**
 * Enqueue styles, fonts and scripts.
 */
function claudia_editorial_assets() {
	// Google Fonts: Cormorant Garamond (serif) + Inter (sans).
	wp_enqueue_style(
		'claudia-editorial-fonts',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Inter:wght@400;500;600&display=swap',
		array(),
		null
	);

	$dir = get_template_directory();
	$uri = get_template_directory_uri();

	// File modification times are used as version strings, so any edit to a
	// stylesheet/script automatically busts the browser and CDN cache.
	$css_ver  = file_exists( $dir . '/style.css' ) ? filemtime( $dir . '/style.css' ) : false;
	$main_ver = file_exists( $dir . '/assets/css/main.css' ) ? filemtime( $dir . '/assets/css/main.css' ) : false;
	$js_ver   = file_exists( $dir . '/assets/js/main.js' ) ? filemtime( $dir . '/assets/js/main.js' ) : false;

	// Required base stylesheet (theme header).
	wp_enqueue_style( 'claudia-editorial-base', get_stylesheet_uri(), array(), $css_ver );

	// Main shared stylesheet.
	wp_enqueue_style(
		'claudia-editorial-main',
		$uri . '/assets/css/main.css',
		array( 'claudia-editorial-base' ),
		$main_ver
	);

	wp_enqueue_script(
		'claudia-editorial-nav',
		$uri . '/assets/js/main.js',
		array(),
		$js_ver,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'claudia_editorial_assets' );

/**
 * Register a footer widget area.
 */
function claudia_editorial_widgets() {
	register_sidebar(
		array(
			'name'          => __( 'Footer', 'claudia-editorial' ),
			'id'            => 'footer-1',
			'description'   => __( 'Erscheint im Footer.', 'claudia-editorial' ),
			'before_widget' => '<div class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4>',
			'after_title'   => '</h4>',
		)
	);
}
add_action( 'widgets_init', 'claudia_editorial_widgets' );

/**
 * URL of the bundled portrait photo, with a cache-busting version based on the
 * file's modification time (so a replaced photo is reloaded, not served stale).
 *
 * @return string
 */
function claudia_portrait_url() {
	$path = get_template_directory() . '/assets/img/claudia.jpg';
	$uri  = get_template_directory_uri() . '/assets/img/claudia.jpg';
	$ver  = file_exists( $path ) ? filemtime( $path ) : wp_get_theme()->get( 'Version' );
	return $uri . '?v=' . $ver;
}

/**
 * Find the URL of the first image inside a post's content.
 *
 * @param int|WP_Post|null $post Optional post.
 * @return string Image URL or empty string.
 */
function claudia_first_content_image_url( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	if ( preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/i', $post->post_content, $m ) ) {
		return $m[1];
	}
	return '';
}

/**
 * Whether a post has any usable image (featured image OR a content image).
 *
 * @param int|WP_Post|null $post Optional post.
 * @return bool
 */
function claudia_has_image( $post = null ) {
	return has_post_thumbnail( $post ) || '' !== claudia_first_content_image_url( $post );
}

/**
 * Output an <img> for a post: the featured image if set, otherwise the first
 * image found in the post content. Saves setting a featured image on every post.
 *
 * @param string           $size Image size for the featured image.
 * @param int|WP_Post|null $post Optional post.
 * @return string HTML <img> or empty string.
 */
function claudia_image( $size = 'large', $post = null ) {
	$post = get_post( $post );
	if ( has_post_thumbnail( $post ) ) {
		return get_the_post_thumbnail( $post, $size, array( 'alt' => the_title_attribute( array( 'echo' => false, 'post' => $post ) ) ) );
	}
	$url = claudia_first_content_image_url( $post );
	if ( $url ) {
		return '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( get_the_title( $post ) ) . '" loading="lazy">';
	}
	return '';
}

/**
 * Estimated reading time for a post (in minutes).
 *
 * @param int|null $post_id Optional post ID.
 * @return int
 */
function claudia_editorial_reading_time( $post_id = null ) {
	$content = get_post_field( 'post_content', $post_id );
	$words   = str_word_count( wp_strip_all_tags( $content ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Output the primary category label for the current post.
 */
function claudia_editorial_primary_category() {
	$cats = get_the_category();
	if ( ! empty( $cats ) ) {
		echo '<a class="eyebrow" href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
	}
}

/**
 * A compact "date · reading time" meta string.
 */
function claudia_editorial_meta() {
	printf(
		'<div class="meta"><time datetime="%1$s">%2$s</time><span class="meta__dot"></span><span>%3$s Min. Lesezeit</span></div>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() ),
		esc_html( claudia_editorial_reading_time() )
	);
}
