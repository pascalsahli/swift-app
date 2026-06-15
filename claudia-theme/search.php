<?php
/**
 * Search results template.
 *
 * @package Claudia_Editorial
 */

get_header();
?>

<section class="page-banner">
	<div class="wrap">
		<h1><?php printf( esc_html__( 'Suchergebnisse für: %s', 'claudia-editorial' ), '“' . esc_html( get_search_query() ) . '”' ); ?></h1>
		<?php get_search_form(); ?>
	</div>
</section>

<section class="section wrap">
	<?php if ( have_posts() ) : ?>
		<div class="post-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content' );
			endwhile;
			?>
		</div>

		<?php
		the_posts_pagination(
			array(
				'mid_size'  => 1,
				'prev_text' => __( '← Zurück', 'claudia-editorial' ),
				'next_text' => __( 'Weiter →', 'claudia-editorial' ),
			)
		);
		?>
	<?php else : ?>
		<p><?php esc_html_e( 'Keine Beiträge gefunden. Versuch es mit einem anderen Suchbegriff.', 'claudia-editorial' ); ?></p>
	<?php endif; ?>
</section>

<?php
get_footer();
