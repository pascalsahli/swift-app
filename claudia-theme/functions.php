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

	// Required base stylesheet (theme header).
	wp_enqueue_style( 'claudia-editorial-base', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );

	// Main shared stylesheet.
	wp_enqueue_style(
		'claudia-editorial-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'claudia-editorial-base' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_script(
		'claudia-editorial-nav',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		wp_get_theme()->get( 'Version' ),
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
