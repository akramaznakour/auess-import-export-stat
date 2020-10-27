<?php
/**
 * Created by PhpStorm.
 * User: Akram
 * Date: 15/07/2019
 * Time: 01:21
 */

namespace App\Controller\Statistique\SubElements;


class StatistiqueSubElement {


	public $colorNames = [ "blue", "yellow", "darkblue", "orange", "green", "purple", "grey" ];


	public static function renderCanvas( $canvas ) {

		$pluginPath = plugin_dir_path( __FILE__ ) . "../../../../";

		require( "$pluginPath/app/View/StatiqueCanvas.php" );
	}


	public function twoColumnsTemplate( $firstCanvasFunction, $secondCanvasFunction ) {
 
		echo "<style>.col-sm-12{width: 50%;display: inline-block;}@media (max-width: 1000px) {.col-sm-12{width: 100%;}}} </style>";

		echo "<div class='row'>";

		echo "<div class='col-md-6 col-sm-12 '>";
		call_user_func( array( $this, $firstCanvasFunction ) );
		echo "</div>";

		echo "<div class='col-md-6 col-sm-12  '>";
		call_user_func( array( $this, $secondCanvasFunction ) );
		echo "</div>";
 
		echo "</div><br/>";

	}
}