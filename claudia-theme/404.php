<?php
/**
 * 404 — not found.
 *
 * @package Claudia_Editorial
 */

get_header();
?>

<section class="page-banner">
	<div class="wrap">
		<p class="eyebrow">404</p>
		<h1><?php esc_html_e( 'Diese Seite gibt es nicht (mehr)', 'claudia-editorial' ); ?></h1>
		<p><?php esc_html_e( 'Vielleicht hilft die Suche weiter?', 'claudia-editorial' ); ?></p>
	</div>
</section>

<section class="section wrap" style="text-align:center;">
	<?php get_search_form(); ?>
	<p style="margin-top:28px;"><a class="btn-text" href="<?php echo esc_url( home_url( '/' ) ); ?>">← <?php esc_html_e( 'Zur Startseite', 'claudia-editorial' ); ?></a></p>
</section>

<?php
get_footer();
