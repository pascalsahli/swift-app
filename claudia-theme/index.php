<?php
/**
 * Main template — blog index.
 *
 * @package Claudia_Editorial
 */

get_header();
?>

<?php if ( have_posts() ) : ?>

	<?php
	// On the first page of the blog home, show the latest post as a featured hero.
	$show_featured = ( is_home() && ! is_paged() );
	if ( $show_featured ) :
		the_post();
		?>
		<section class="featured wrap">
			<div class="featured__grid<?php echo has_post_thumbnail() ? '' : ' featured__grid--noimage'; ?>">
				<?php if ( has_post_thumbnail() ) : ?>
					<a class="featured__media" href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( 'large', array( 'alt' => the_title_attribute( array( 'echo' => false ) ) ) ); ?>
					</a>
				<?php endif; ?>
				<div class="featured__body">
					<?php claudia_editorial_primary_category(); ?>
					<h1 class="featured__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
					<p class="featured__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php claudia_editorial_meta(); ?>
					<a class="btn-text" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Weiterlesen', 'claudia-editorial' ); ?> <span class="arrow">→</span></a>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="section wrap">
		<div class="section-head">
			<h2><?php echo $show_featured ? esc_html__( 'Neueste Geschichten', 'claudia-editorial' ) : esc_html__( 'Beiträge', 'claudia-editorial' ); ?></h2>
		</div>

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
	</section>

	<?php get_template_part( 'template-parts/newsletter' ); ?>

<?php else : ?>

	<section class="section wrap">
		<h2><?php esc_html_e( 'Noch keine Beiträge', 'claudia-editorial' ); ?></h2>
		<p><?php esc_html_e( 'Schau bald wieder vorbei – hier entstehen gerade neue Geschichten.', 'claudia-editorial' ); ?></p>
	</section>

<?php endif; ?>

<?php
get_footer();
