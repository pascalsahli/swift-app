<?php
/**
 * Comments template.
 *
 * @package Claudia_Editorial
 */

if ( post_password_required() ) {
	return;
}
?>

<section class="comments" id="comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments__title">
			<?php
			$count = get_comments_number();
			printf(
				esc_html( _n( '%s Kommentar', '%s Kommentare', $count, 'claudia-editorial' ) ),
				esc_html( number_format_i18n( $count ) )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'avatar_size' => 44,
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => __( '← Ältere', 'claudia-editorial' ),
				'next_text' => __( 'Neuere →', 'claudia-editorial' ),
			)
		);
		?>
	<?php endif; ?>

	<?php
	if ( ! comments_open() && get_comments_number() ) :
		?>
		<p class="no-comments"><?php esc_html_e( 'Kommentare sind geschlossen.', 'claudia-editorial' ); ?></p>
		<?php
	endif;

	comment_form(
		array(
			'class_submit' => 'btn',
			'title_reply'  => __( 'Hinterlasse einen Kommentar', 'claudia-editorial' ),
		)
	);
	?>
</section>
