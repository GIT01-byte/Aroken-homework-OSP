<?php defined( 'ABSPATH' ) || exit; ?>

<div class="notice-success is-dismissible wpse-variation-metabox" style="margin-left: 10px">
	<?php
	// translators: %s: sheet editor url
	echo wp_kses_post( sprintf( __( '<b>WP Sheet Editor:</b> <a href="%s" target="_blank">Open in a spreadsheet</a>', 'vg_sheet_editor' ), esc_url( $spreadsheet_url ) ) );
	?>
</div>