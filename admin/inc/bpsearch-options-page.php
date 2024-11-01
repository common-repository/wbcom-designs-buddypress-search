<?php
/**
 *
 * This template file is used for fetching desired options page file at admin settings end.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Search
 * @subpackage Buddypress_Search/admin/inc
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bpsts_tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'welcome';
switch ( $bpsts_tab ) {
	case 'welcome':
		include 'bpsearch-welcome-page.php';
		break;
	case 'bp-search':
		include 'bpsearch-bp-search.php';
		break;
	case 'pages-posts-search':
		include 'bpsearch-pages-posts-search.php';
		break;
	case 'live-search-settings':
		include 'bpsearch-live-search-settings.php';
		break;
	default:
		include 'bpsearch-welcome-page.php';
		break;
}




