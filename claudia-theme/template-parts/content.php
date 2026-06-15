<?php
/**
 * Post card used in grids (index, archive, search).
 *
 * @package Claudia_Editorial
 */

?>
<article <?php post_class( 'post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="post-card__media" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php the_post_thumbnail( 'medium_large', array( 'alt' => the_title_attribute( array( 'echo' => false ) ) ) ); ?>
		</a>
	<?php endif; ?>

	<?php claudia_editorial_primary_category(); ?>

	<h3 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

	<p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22, '…' ) ); ?></p>

	<?php claudia_editorial_meta(); ?>
</article>
