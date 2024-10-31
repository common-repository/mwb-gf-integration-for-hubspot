<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Mwb_Gf_Integration_For_Hubspot
 * @subpackage Mwb_Gf_Integration_For_Hubspot/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$headings = $this->add_plugin_headings();
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<main class="mwb-gf-integration-main">
	<header class="mwb-gf-integration-header">
		<h1 class="mwb-gf-integration-header__title"><?php echo esc_html( ! empty( $headings['name'] ) ? $headings['name'] : '' ); ?></h1>
		<span class="mwb-gf-integration-version"><?php echo sprintf( 'v%s', esc_html( ! empty( $headings['version'] ) ? $headings['version'] : '1.0.0' ) ); ?></span>
	</header>
	<?php if ( true == get_option( 'mwb-gf-' . $this->crm_slug . '-authorised' ) ) : // phpcs:ignore?>
		<!-- Dashboard Screen -->
		<?php do_action( 'mwb_' . $this->crm_slug . '_gf_nav_tab' ); ?>
	<?php else : ?>
		<!-- Authorisation Screen -->
		<?php do_action( 'mwb_' . $this->crm_slug . '_gf_auth_screen' ); ?>
	<?php endif; ?>
</main>

