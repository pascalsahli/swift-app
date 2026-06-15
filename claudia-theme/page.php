<?php
/**
 * Static page template.
 *
 * @package Claudia_Editorial
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class(); ?>>
		<header class="post-hero wrap">
			<h1 class="post-hero__title"><?php the_title(); ?></h1>
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
	</article>

	<?php
	if ( comments_open() || get_comments_number() ) :
		?>
		<div class="article"><?php comments_template(); ?></div>
		<?php
	endif;

endwhile;

get_footer();
