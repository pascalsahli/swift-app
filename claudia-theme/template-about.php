<?php
/**
 * Template Name: Über mich
 *
 * Eine gestaltete Profilseite. So verwendest du sie:
 *  1. Seiten → Erstellen → Titel z. B. "Über mich".
 *  2. Rechts unter "Seitenattribute → Vorlage" → "Über mich" wählen.
 *  3. Ein Beitragsbild setzen = Porträtfoto.
 *  4. Den Auszug (Textauszug) als Lead-Satz nutzen, den Haupttext im Editor schreiben.
 *
 * @package Claudia_Editorial
 */

get_header();

while ( have_posts() ) :
	the_post();

	/*
	 * "Auf einen Blick"-Zahlen. Einfach anpassen – Wert => Beschriftung.
	 * Leeres Array () lässt diesen Abschnitt verschwinden.
	 */
	$facts = array(
		'30+' => __( 'Länder bereist', 'claudia-editorial' ),
		'4'   => __( 'Sprachen', 'claudia-editorial' ),
		'200' => __( 'Geschichten', 'claudia-editorial' ),
		'∞'   => __( 'Fernweh', 'claudia-editorial' ),
	);
	?>

	<article <?php post_class(); ?>>
		<section class="about-intro wrap">
			<div class="about-intro__grid">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="about-portrait"><?php the_post_thumbnail( 'large' ); ?></div>
				<?php endif; ?>
				<div class="about-intro__body">
					<span class="eyebrow"><?php esc_html_e( 'Über mich', 'claudia-editorial' ); ?></span>
					<h1 class="post-hero__title" style="text-align:left;margin-top:8px;"><?php the_title(); ?></h1>
					<?php if ( has_excerpt() ) : ?>
						<p class="about-lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<div class="article">
			<?php the_content(); ?>
			<?php if ( get_edit_post_link() ) : ?>
				<p class="about-signature"><?php echo esc_html( get_the_author() ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $facts ) ) : ?>
			<section class="section about-facts">
				<div class="wrap">
					<div class="about-facts__grid">
						<?php foreach ( $facts as $num => $label ) : ?>
							<div class="about-fact">
								<div class="about-fact__num"><?php echo esc_html( $num ); ?></div>
								<div class="about-fact__label"><?php echo esc_html( $label ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	</article>

	<section class="section band">
		<div class="wrap band__inner">
			<span class="eyebrow"><?php esc_html_e( 'Bleib in Kontakt', 'claudia-editorial' ); ?></span>
			<h2><?php esc_html_e( 'Lust auf meine Geschichten?', 'claudia-editorial' ); ?></h2>
			<p><?php esc_html_e( 'Folge mir auf der Reise – neue Beiträge findest du jederzeit im Blog.', 'claudia-editorial' ); ?></p>
			<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Zum Blog', 'claudia-editorial' ); ?></a>
		</div>
	</section>

	<?php
endwhile;

get_footer();
