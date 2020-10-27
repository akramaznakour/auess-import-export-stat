<?php
/**
 * @package  auessImportExportStatPLugin
 * @copyright   2019 http://auessaouira.com
 */
/*
Plugin Name: auess-import-export-stat
Plugin URI: http://auessaouira.com
Version: 1.0.0
Author: Akram Aznakour
Author URI: github.com/AkramAznakour
Description: Plugin pour importer et exporter les données et presenter des statistiques
*/

// If this file is called firectly, abort!!!
defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );

// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

/**
 * The code that runs during plugin activation
 */
function activate_auessaouira_plugin() {
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'activate_auessaouira_plugin' );

/**
 * The code that runs during plugin deactivation
 */

function deactivate_auessaouira_plugin() {
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'deactivate_auessaouira_plugin' );

/**
 * Initialize all the core classes of the plugin
 */

include( dirname( __FILE__ ) . '/library/apf/admin-page-framework.php' );


use App\Controller\ImportExport\ImportExportDashboard;
use App\Controller\Statistique\StatistiqueDashBoard;
use App\Controller\Api;

// use App\Widget\StatistiqueWideget;

new ImportExportDashboard( null,                           // the option key - when null is passed the class name in this case 'auessaouira_BasicUsage' will be used
	AdminPageFramework_Registry::$sFilePath, // the caller script path.
	'manage_options',               // the default capability
	'auessaouira'   // the text domain
);

new StatistiqueDashBoard( null,                           // the option key - when null is passed the class name in this case 'auessaouira_BasicUsage' will be used
	AdminPageFramework_Registry::$sFilePath, // the caller script path.
	'manage_options',               // the default capability
	'auessaouira'   // the text domain
);

new Api\ApiContoller();

