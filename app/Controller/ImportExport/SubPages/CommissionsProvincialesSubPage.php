<?php


namespace App\Controller\ImportExport\SubPages;


final class  CommissionsProvincialesSubPage extends ImportExportSubPageController {

	protected $pageSlug = "auessaouira_commissions_provinciales";
	protected $postType = "commission-regionale";

	protected $valideProjectTypes = array(
		array( "name" => "ADHOC", "id" => "5" ),
	);

	protected $columnsNames = array(
		'date de la commission',
		'numero de dossier',
		'intitule',
		'situation du projet',
		'consistence',
		'architecte',
		'foncier',
		'derogation demandee',
		'observations',
	);


	protected $indexCloumnPostTile = 1;

	public function _insertPostMeta( $postId, $sheetData, $row ) {

		if ( $sheetData->getCellByColumnAndRow( 1, $row )->getFormattedValue() != "" ) {

			for ( $col = 1; $col <= count( $this->columnsNames ); $col ++ ) {

				if ( $col == $this->_getColumnIndex( 'type' ) ) {

					update_post_meta( $postId, $this->_getColumnNameFormated( $this->columnsNames[ $col - 1 ] ), \DateTime::createFromFormat( 'm/d/Y', $sheetData->getCellByColumnAndRow( $col, $row )->getFormattedValue() )->format( 'd/m/Y' ) );

				} else {
					update_post_meta( $postId, $this->_getColumnNameFormated( $this->columnsNames[ $col - 1 ] ), $sheetData->getCellByColumnAndRow( $col, $row )->getFormattedValue() );
				}
			}
		}
	}


	public function _isOfCorrectType( $sheetData, $row ) {
		return true;
	}

	public function _isOfCorrectAvis( $sheetData, $row ) {
		return true;
	}

}
