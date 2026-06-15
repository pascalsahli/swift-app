<?php
/**
 * Newsletter subscription band.
 *
 * @package Claudia_Editorial
 */

if ( function_exists( 'claudia_subscribe_form' ) ) {
	echo claudia_subscribe_form(); // phpcs:ignore WordPress.Security.EscapeOutput -- output is escaped within the function.
}
