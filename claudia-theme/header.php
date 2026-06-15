<?php
/**
 * Header template.
 *
 * @package Claudia_Editorial
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#content"><?php esc_html_e( 'Zum Inhalt springen', 'claudia-editorial' ); ?></a>

<header class="site-header">
	<div class="site-header__inner">
		<div class="site-brand">
			<a class="site-brand__avatar" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-hidden="true" tabindex="-1">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/claudia.jpg' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="56" height="56">
			</a>
			<div class="site-brand__text">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<p class="site-brand__title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></p>
					<?php $desc = get_bloginfo( 'description', 'display' ); ?>
					<?php if ( $desc ) : ?>
						<div class="site-brand__tagline"><?php echo esc_html( $desc ); ?></div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>

		<button class="nav-toggle" aria-label="<?php esc_attr_e( 'Menü', 'claudia-editorial' ); ?>" aria-expanded="false" aria-controls="primary-nav">☰</button>

		<nav class="site-nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Hauptmenü', 'claudia-editorial' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => '',
						'fallback_cb'    => false,
					)
				);
			} else {
				echo '<ul>';
				wp_list_pages( array( 'title_li' => '' ) );
				echo '</ul>';
			}
			?>
		</nav>
	</div>
</header>

<main id="content">
