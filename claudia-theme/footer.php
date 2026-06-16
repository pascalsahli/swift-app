<?php
/**
 * Footer template.
 *
 * @package Claudia_Editorial
 */

?>
</main><!-- #content -->

<footer class="site-footer">
	<div class="wrap">
		<div class="footer__grid">
			<div class="footer__brand">
				<p class="site-brand__title"><?php bloginfo( 'name' ); ?></p>
				<p><?php echo esc_html( get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : __( 'Reisegeschichten, Gedanken und so vieles mehr.', 'claudia-editorial' ) ); ?></p>
			</div>

			<div class="footer">
				<h4><?php esc_html_e( 'Entdecken', 'claudia-editorial' ); ?></h4>
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => '',
							'fallback_cb'    => false,
							'depth'          => 1,
						)
					);
				} else {
					echo '<ul>';
					wp_list_categories(
						array(
							'title_li' => '',
							'number'   => 5,
						)
					);
					echo '</ul>';
				}
				?>
			</div>

			<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
				<div class="footer">
					<?php dynamic_sidebar( 'footer-1' ); ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="footer__bottom">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Alle Rechte vorbehalten.', 'claudia-editorial' ); ?></span>
			<span><?php esc_html_e( 'Mit Freude erstellt.', 'claudia-editorial' ); ?></span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
