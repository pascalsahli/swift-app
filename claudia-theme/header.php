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
				<img src="<?php echo esc_url( claudia_portrait_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="56" height="56">
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
				// No custom menu set: build a sensible automatic menu.
				$claudia_posts = get_posts(
					array(
						'numberposts' => -1,
						'post_status' => 'publish',
						'orderby'     => 'date',
						'order'       => 'DESC',
					)
				);
				?>
				<ul>
					<li class="<?php echo ( is_home() || is_front_page() ) ? 'current' : ''; ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Start', 'claudia-editorial' ); ?></a></li>
					<?php wp_list_pages( array( 'title_li' => '', 'depth' => 1 ) ); ?>
					<?php if ( $claudia_posts ) : ?>
						<li class="menu-item-has-children">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Beiträge', 'claudia-editorial' ); ?> <span class="caret" aria-hidden="true">▾</span></a>
							<ul class="sub-menu">
								<?php foreach ( $claudia_posts as $claudia_post ) : ?>
									<li><a href="<?php echo esc_url( get_permalink( $claudia_post ) ); ?>" title="<?php echo esc_attr( get_the_title( $claudia_post ) ); ?>"><?php echo esc_html( get_the_date( 'j. F Y', $claudia_post ) ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</li>
					<?php endif; ?>
				</ul>
				<?php
			}
			?>
		</nav>
	</div>
</header>

<main id="content">
