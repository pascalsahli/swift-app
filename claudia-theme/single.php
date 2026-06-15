<?php
/**
 * Single post template.
 *
 * @package Claudia_Editorial
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<article <?php post_class(); ?>>
		<header class="post-hero wrap">
			<?php claudia_editorial_primary_category(); ?>
			<h1 class="post-hero__title"><?php the_title(); ?></h1>
			<?php claudia_editorial_meta(); ?>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="post-cover wrap">
				<?php the_post_thumbnail( 'large' ); ?>
			</figure>
		<?php endif; ?>

		<div class="article">
			<?php
			the_content();

			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Seiten:', 'claudia-editorial' ),
					'after'  => '</div>',
				)
			);
			?>
		</div>

		<?php
		$tags = get_the_tags();
		if ( $tags ) :
			?>
			<div class="tags">
				<?php foreach ( $tags as $tag ) : ?>
					<a class="tag" href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>">#<?php echo esc_html( $tag->name ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</article>

	<?php
	if ( comments_open() || get_comments_number() ) :
		?>
		<div class="article">
			<?php comments_template(); ?>
		</div>
		<?php
	endif;

endwhile;

get_footer();
