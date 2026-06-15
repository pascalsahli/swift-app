<?php
/**
 * Lightweight e-mail subscription ("Newsletter") for visitors.
 *
 * Collects e-mail addresses into a custom database table, with a honeypot
 * spam trap, an admin overview and CSV export. No third-party service needed.
 *
 * Note: this captures/stores addresses. To actually *send* newsletters you can
 * export the list here, or pair it with a sending plugin (e.g. MailPoet).
 *
 * @package Claudia_Editorial
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Subscribers table name.
 *
 * @return string
 */
function claudia_subscribers_table() {
	global $wpdb;
	return $wpdb->prefix . 'claudia_subscribers';
}

/**
 * Create the subscribers table on theme activation.
 */
function claudia_newsletter_install() {
	global $wpdb;
	$table           = claudia_subscribers_table();
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		email varchar(190) NOT NULL,
		created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		status varchar(20) NOT NULL DEFAULT 'subscribed',
		PRIMARY KEY  (id),
		UNIQUE KEY email (email)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}
add_action( 'after_switch_theme', 'claudia_newsletter_install' );

/**
 * Render the subscription band/form.
 *
 * @param array $args Optional. heading, text, eyebrow.
 * @return string HTML.
 */
function claudia_subscribe_form( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'eyebrow' => __( 'Newsletter', 'claudia-editorial' ),
			'heading' => __( 'Keine Geschichte verpassen', 'claudia-editorial' ),
			'text'    => __( 'Trag dich ein und erhalte neue Beiträge direkt in dein Postfach – ganz ohne Schnickschnack.', 'claudia-editorial' ),
		)
	);

	// Feedback after a submission (set via redirect query arg).
	$state   = isset( $_GET['claudia_sub'] ) ? sanitize_key( wp_unslash( $_GET['claudia_sub'] ) ) : '';
	$message = '';
	$is_ok   = false;
	if ( 'success' === $state ) {
		$message = __( 'Vielen Dank! Du bist jetzt angemeldet.', 'claudia-editorial' );
		$is_ok   = true;
	} elseif ( 'exists' === $state ) {
		$message = __( 'Diese Adresse ist bereits angemeldet.', 'claudia-editorial' );
	} elseif ( 'invalid' === $state ) {
		$message = __( 'Bitte gib eine gültige E-Mail-Adresse ein.', 'claudia-editorial' );
	}

	ob_start();
	?>
	<section class="section band" id="newsletter">
		<div class="wrap band__inner">
			<span class="eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></span>
			<h2><?php echo esc_html( $args['heading'] ); ?></h2>
			<p><?php echo esc_html( $args['text'] ); ?></p>

			<?php if ( $message ) : ?>
				<p class="form-message <?php echo $is_ok ? 'is-success' : 'is-error'; ?>"><?php echo esc_html( $message ); ?></p>
			<?php endif; ?>

			<form class="subscribe" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="claudia_subscribe">
				<?php wp_nonce_field( 'claudia_subscribe', 'claudia_nonce' ); ?>
				<label class="screen-reader-text" for="claudia-email"><?php esc_html_e( 'E-Mail-Adresse', 'claudia-editorial' ); ?></label>
				<input type="email" id="claudia-email" name="claudia_email" required placeholder="<?php esc_attr_e( 'deine@email.ch', 'claudia-editorial' ); ?>">
				<?php // Honeypot: hidden from humans, often filled by bots. ?>
				<input type="text" name="claudia_website" class="hp-field" tabindex="-1" autocomplete="off" aria-hidden="true">
				<button class="btn" type="submit"><?php esc_html_e( 'Abonnieren', 'claudia-editorial' ); ?></button>
			</form>
			<p class="subscribe__note"><?php esc_html_e( 'Keine Weitergabe deiner Daten. Abmeldung jederzeit möglich.', 'claudia-editorial' ); ?></p>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Shortcode: [claudia_subscribe].
 */
add_shortcode(
	'claudia_subscribe',
	function ( $atts ) {
		$atts = shortcode_atts(
			array(
				'heading' => __( 'Keine Geschichte verpassen', 'claudia-editorial' ),
				'text'    => __( 'Trag dich ein und erhalte neue Beiträge direkt in dein Postfach – ganz ohne Schnickschnack.', 'claudia-editorial' ),
			),
			$atts
		);
		return claudia_subscribe_form( $atts );
	}
);

/**
 * Handle the form submission (logged-in and logged-out visitors).
 */
function claudia_handle_subscribe() {
	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );

	// Verify nonce.
	if ( ! isset( $_POST['claudia_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['claudia_nonce'] ) ), 'claudia_subscribe' ) ) {
		wp_safe_redirect( add_query_arg( 'claudia_sub', 'invalid', $redirect ) . '#newsletter' );
		exit;
	}

	// Honeypot: pretend success so bots don't learn anything.
	if ( ! empty( $_POST['claudia_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'claudia_sub', 'success', $redirect ) . '#newsletter' );
		exit;
	}

	$email = isset( $_POST['claudia_email'] ) ? sanitize_email( wp_unslash( $_POST['claudia_email'] ) ) : '';
	if ( ! $email || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'claudia_sub', 'invalid', $redirect ) . '#newsletter' );
		exit;
	}

	global $wpdb;
	$table  = claudia_subscribers_table();
	$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table WHERE email = %s", $email ) ); // phpcs:ignore WordPress.DB

	if ( $exists ) {
		wp_safe_redirect( add_query_arg( 'claudia_sub', 'exists', $redirect ) . '#newsletter' );
		exit;
	}

	$wpdb->insert( // phpcs:ignore WordPress.DB
		$table,
		array(
			'email'      => $email,
			'created_at' => current_time( 'mysql' ),
			'status'     => 'subscribed',
		),
		array( '%s', '%s', '%s' )
	);

	// Notify the site admin (best effort).
	wp_mail(
		get_option( 'admin_email' ),
		__( 'Neue Newsletter-Anmeldung', 'claudia-editorial' ),
		sprintf( __( 'Neue Anmeldung: %s', 'claudia-editorial' ), $email )
	);

	wp_safe_redirect( add_query_arg( 'claudia_sub', 'success', $redirect ) . '#newsletter' );
	exit;
}
add_action( 'admin_post_nopriv_claudia_subscribe', 'claudia_handle_subscribe' );
add_action( 'admin_post_claudia_subscribe', 'claudia_handle_subscribe' );

/**
 * Admin menu: subscribers overview.
 */
function claudia_subscribers_menu() {
	add_menu_page(
		__( 'Newsletter', 'claudia-editorial' ),
		__( 'Newsletter', 'claudia-editorial' ),
		'manage_options',
		'claudia-subscribers',
		'claudia_subscribers_page',
		'dashicons-email',
		26
	);
}
add_action( 'admin_menu', 'claudia_subscribers_menu' );

/**
 * Render the subscribers admin page.
 */
function claudia_subscribers_page() {
	global $wpdb;
	$table = claudia_subscribers_table();
	$rows  = $wpdb->get_results( "SELECT email, created_at FROM $table ORDER BY created_at DESC" ); // phpcs:ignore WordPress.DB
	$count = is_array( $rows ) ? count( $rows ) : 0;

	$export_url = wp_nonce_url( admin_url( 'admin-post.php?action=claudia_export_subscribers' ), 'claudia_export' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Newsletter-Abonnenten', 'claudia-editorial' ); ?></h1>
		<p>
			<?php printf( esc_html__( 'Insgesamt %s Anmeldungen.', 'claudia-editorial' ), '<strong>' . esc_html( number_format_i18n( $count ) ) . '</strong>' ); ?>
			&nbsp; <a class="button button-primary" href="<?php echo esc_url( $export_url ); ?>"><?php esc_html_e( 'Als CSV exportieren', 'claudia-editorial' ); ?></a>
		</p>
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'E-Mail', 'claudia-editorial' ); ?></th>
					<th><?php esc_html_e( 'Angemeldet am', 'claudia-editorial' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( $rows ) : ?>
					<?php foreach ( $rows as $row ) : ?>
						<tr>
							<td><?php echo esc_html( $row->email ); ?></td>
							<td><?php echo esc_html( $row->created_at ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr><td colspan="2"><?php esc_html_e( 'Noch keine Anmeldungen.', 'claudia-editorial' ); ?></td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}

/**
 * Export subscribers as CSV.
 */
function claudia_export_subscribers() {
	if ( ! current_user_can( 'manage_options' ) || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'claudia_export' ) ) {
		wp_die( esc_html__( 'Keine Berechtigung.', 'claudia-editorial' ) );
	}

	global $wpdb;
	$table = claudia_subscribers_table();
	$rows  = $wpdb->get_results( "SELECT email, created_at, status FROM $table ORDER BY created_at DESC", ARRAY_A ); // phpcs:ignore WordPress.DB

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=newsletter-abonnenten.csv' );

	$out = fopen( 'php://output', 'w' );
	fputcsv( $out, array( 'email', 'created_at', 'status' ) );
	if ( $rows ) {
		foreach ( $rows as $row ) {
			fputcsv( $out, $row );
		}
	}
	fclose( $out );
	exit;
}
add_action( 'admin_post_claudia_export_subscribers', 'claudia_export_subscribers' );
