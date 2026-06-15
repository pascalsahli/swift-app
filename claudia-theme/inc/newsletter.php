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
 * Database schema version. Bump to trigger an upgrade via dbDelta.
 */
define( 'CLAUDIA_NEWSLETTER_DB', '1.1' );

/**
 * Create / upgrade the subscribers table.
 *
 * Adds double opt-in columns: a confirmation token and the confirmation time.
 * dbDelta is idempotent, so this also safely upgrades an existing table.
 */
function claudia_newsletter_install() {
	global $wpdb;
	$table           = claudia_subscribers_table();
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		email varchar(190) NOT NULL,
		status varchar(20) NOT NULL DEFAULT 'pending',
		token varchar(64) DEFAULT '',
		created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		confirmed_at datetime DEFAULT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY email (email)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
	update_option( 'claudia_newsletter_db', CLAUDIA_NEWSLETTER_DB );
}
add_action( 'after_switch_theme', 'claudia_newsletter_install' );

/**
 * Run the upgrade when the stored schema version is out of date.
 */
function claudia_newsletter_maybe_upgrade() {
	if ( get_option( 'claudia_newsletter_db' ) !== CLAUDIA_NEWSLETTER_DB ) {
		claudia_newsletter_install();
	}
}
add_action( 'admin_init', 'claudia_newsletter_maybe_upgrade' );

/**
 * Send the double opt-in confirmation e-mail to a subscriber.
 *
 * @param string $email Recipient.
 * @param string $token Confirmation token.
 */
function claudia_send_confirmation( $email, $token ) {
	$site    = get_bloginfo( 'name' );
	$confirm = add_query_arg( 'claudia_confirm', rawurlencode( $token ), home_url( '/' ) );
	$subject = sprintf( __( 'Bitte bestätige deine Anmeldung bei %s', 'claudia-editorial' ), $site );

	$body  = '<div style="font-family:Georgia,serif;font-size:16px;line-height:1.6;color:#1c1a17;">';
	$body .= '<p>' . esc_html__( 'Schön, dass du dabei sein möchtest!', 'claudia-editorial' ) . '</p>';
	$body .= '<p>' . esc_html__( 'Bitte bestätige deine E-Mail-Adresse mit einem Klick auf den folgenden Link:', 'claudia-editorial' ) . '</p>';
	$body .= '<p><a href="' . esc_url( $confirm ) . '" style="display:inline-block;background:#1c1a17;color:#fff;padding:12px 22px;text-decoration:none;border-radius:2px;">' . esc_html__( 'Anmeldung bestätigen', 'claudia-editorial' ) . '</a></p>';
	$body .= '<p style="font-size:13px;color:#726c63;">' . esc_html__( 'Falls du dich nicht angemeldet hast, ignoriere diese E-Mail einfach – es passiert dann nichts.', 'claudia-editorial' ) . '</p>';
	$body .= '<p style="font-size:13px;color:#726c63;">' . esc_html( $site ) . '</p>';
	$body .= '</div>';

	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	wp_mail( $email, $subject, $body, $headers );
}

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
	if ( 'pending' === $state ) {
		$message = __( 'Fast geschafft! Wir haben dir eine Bestätigungs-E-Mail geschickt. Bitte klicke auf den Link darin, um deine Anmeldung abzuschliessen.', 'claudia-editorial' );
		$is_ok   = true;
	} elseif ( 'confirmed' === $state ) {
		$message = __( 'Vielen Dank! Deine Anmeldung ist jetzt bestätigt.', 'claudia-editorial' );
		$is_ok   = true;
	} elseif ( 'exists' === $state ) {
		$message = __( 'Diese Adresse ist bereits angemeldet.', 'claudia-editorial' );
	} elseif ( 'confirm_invalid' === $state ) {
		$message = __( 'Dieser Bestätigungslink ist ungültig oder abgelaufen.', 'claudia-editorial' );
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
	$table    = claudia_subscribers_table();
	$existing = $wpdb->get_row( $wpdb->prepare( "SELECT id, status, token FROM $table WHERE email = %s", $email ) ); // phpcs:ignore WordPress.DB

	if ( $existing && 'confirmed' === $existing->status ) {
		// Already a confirmed subscriber.
		wp_safe_redirect( add_query_arg( 'claudia_sub', 'exists', $redirect ) . '#newsletter' );
		exit;
	}

	$token = wp_generate_password( 32, false );

	if ( $existing ) {
		// Pending sign-up: refresh the token and resend the confirmation.
		$wpdb->update( // phpcs:ignore WordPress.DB
			$table,
			array(
				'token'      => $token,
				'created_at' => current_time( 'mysql' ),
			),
			array( 'id' => $existing->id ),
			array( '%s', '%s' ),
			array( '%d' )
		);
	} else {
		$wpdb->insert( // phpcs:ignore WordPress.DB
			$table,
			array(
				'email'      => $email,
				'status'     => 'pending',
				'token'      => $token,
				'created_at' => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%s' )
		);
	}

	claudia_send_confirmation( $email, $token );

	wp_safe_redirect( add_query_arg( 'claudia_sub', 'pending', $redirect ) . '#newsletter' );
	exit;
}
add_action( 'admin_post_nopriv_claudia_subscribe', 'claudia_handle_subscribe' );
add_action( 'admin_post_claudia_subscribe', 'claudia_handle_subscribe' );

/**
 * Handle the confirmation link from the double opt-in e-mail.
 */
function claudia_handle_confirm() {
	if ( empty( $_GET['claudia_confirm'] ) ) {
		return;
	}

	$token = sanitize_text_field( rawurldecode( wp_unslash( $_GET['claudia_confirm'] ) ) );
	$home  = home_url( '/' );

	if ( ! $token ) {
		wp_safe_redirect( add_query_arg( 'claudia_sub', 'confirm_invalid', $home ) . '#newsletter' );
		exit;
	}

	global $wpdb;
	$table = claudia_subscribers_table();
	$row   = $wpdb->get_row( $wpdb->prepare( "SELECT id, email FROM $table WHERE token = %s AND status = 'pending'", $token ) ); // phpcs:ignore WordPress.DB

	if ( ! $row ) {
		wp_safe_redirect( add_query_arg( 'claudia_sub', 'confirm_invalid', $home ) . '#newsletter' );
		exit;
	}

	$wpdb->update( // phpcs:ignore WordPress.DB
		$table,
		array(
			'status'       => 'confirmed',
			'token'        => '',
			'confirmed_at' => current_time( 'mysql' ),
		),
		array( 'id' => $row->id ),
		array( '%s', '%s', '%s' ),
		array( '%d' )
	);

	// Notify the site admin now that the subscription is confirmed.
	wp_mail(
		get_option( 'admin_email' ),
		__( 'Neue bestätigte Newsletter-Anmeldung', 'claudia-editorial' ),
		sprintf( __( 'Bestätigte Anmeldung: %s', 'claudia-editorial' ), $row->email )
	);

	wp_safe_redirect( add_query_arg( 'claudia_sub', 'confirmed', $home ) . '#newsletter' );
	exit;
}
add_action( 'template_redirect', 'claudia_handle_confirm' );

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
	$table     = claudia_subscribers_table();
	$rows      = $wpdb->get_results( "SELECT email, status, created_at, confirmed_at FROM $table ORDER BY created_at DESC" ); // phpcs:ignore WordPress.DB
	$confirmed = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE status = 'confirmed'" ); // phpcs:ignore WordPress.DB
	$pending   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE status = 'pending'" ); // phpcs:ignore WordPress.DB

	$export_url = wp_nonce_url( admin_url( 'admin-post.php?action=claudia_export_subscribers' ), 'claudia_export' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Newsletter-Abonnenten', 'claudia-editorial' ); ?></h1>
		<p>
			<?php printf( esc_html__( '%1$s bestätigt, %2$s ausstehend (Bestätigung offen).', 'claudia-editorial' ), '<strong>' . esc_html( number_format_i18n( $confirmed ) ) . '</strong>', '<strong>' . esc_html( number_format_i18n( $pending ) ) . '</strong>' ); ?>
			&nbsp; <a class="button button-primary" href="<?php echo esc_url( $export_url ); ?>"><?php esc_html_e( 'Bestätigte als CSV exportieren', 'claudia-editorial' ); ?></a>
		</p>
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'E-Mail', 'claudia-editorial' ); ?></th>
					<th><?php esc_html_e( 'Status', 'claudia-editorial' ); ?></th>
					<th><?php esc_html_e( 'Angemeldet am', 'claudia-editorial' ); ?></th>
					<th><?php esc_html_e( 'Bestätigt am', 'claudia-editorial' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( $rows ) : ?>
					<?php foreach ( $rows as $row ) : ?>
						<tr>
							<td><?php echo esc_html( $row->email ); ?></td>
							<td><?php echo 'confirmed' === $row->status ? esc_html__( '✓ bestätigt', 'claudia-editorial' ) : esc_html__( '⏳ ausstehend', 'claudia-editorial' ); ?></td>
							<td><?php echo esc_html( $row->created_at ); ?></td>
							<td><?php echo esc_html( $row->confirmed_at ? $row->confirmed_at : '—' ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr><td colspan="4"><?php esc_html_e( 'Noch keine Anmeldungen.', 'claudia-editorial' ); ?></td></tr>
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
	$rows  = $wpdb->get_results( "SELECT email, created_at, confirmed_at FROM $table WHERE status = 'confirmed' ORDER BY confirmed_at DESC", ARRAY_A ); // phpcs:ignore WordPress.DB

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=newsletter-abonnenten.csv' );

	$out = fopen( 'php://output', 'w' );
	fputcsv( $out, array( 'email', 'created_at', 'confirmed_at' ) );
	if ( $rows ) {
		foreach ( $rows as $row ) {
			fputcsv( $out, $row );
		}
	}
	fclose( $out );
	exit;
}
add_action( 'admin_post_claudia_export_subscribers', 'claudia_export_subscribers' );
