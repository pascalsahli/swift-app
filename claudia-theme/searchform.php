<?php
/**
 * Search form.
 *
 * @package Claudia_Editorial
 */

?>
<form role="search" method="get" class="subscribe" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="s"><?php esc_html_e( 'Suchen nach:', 'claudia-editorial' ); ?></label>
	<input type="search" id="s" name="s" placeholder="<?php esc_attr_e( 'Suchen…', 'claudia-editorial' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
	<button type="submit" class="btn"><?php esc_html_e( 'Suchen', 'claudia-editorial' ); ?></button>
</form>
