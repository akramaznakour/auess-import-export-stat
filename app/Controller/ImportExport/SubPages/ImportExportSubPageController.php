<?php


namespace App\Controller\ImportExport\SubPages;


use App\Model\Post;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class ImportExportSubPageController
{


    protected $valideProjectTypes = array();
    protected $columnsNames = array();

    protected $indexCloumnPostTile = 2;

    protected $correctAvis = array(
        "favorable",
        "défavorable",
        "ajourné",
        "الرفض",
        "تأجيل البث",
        "الموافقة"
    );
    protected $pageSlug = "";
    protected $postType = "";
    protected $errors = "";
    protected $messages = "";
    protected $pluginPath = "";
    protected $pluginName = "auess-import-export-stat";

    protected $listOfErrors = array(
        "unChosedFile" => "Vous n'avez pas choisi un fichier",
        "inccorectAvis" => "Les avis de vos projets content des valeurs incorrectes",
        "inccorectType" => "Votre fichier contient des projets qui ne sont pas de type de projet correct",
        "emptyLine" => "Votre fichier contient des lignes vides"
    );

    protected $listOfMessages = array(
        "successfulImport" => "Le fichier est bien importé"
    );

    public function __construct()
    {

        $this->pluginPath = plugin_dir_path(__FILE__) . '../../../../';

        if (isset($_FILES['fileToUpload'])) {
            if ($_FILES['fileToUpload']['name'] != "") {
                $this->uploadData();
            } else {
                $this->_appendError("unChosedFile");
            }
        }

        $this->renderErrors();
        $this->renderMessages();
        $this->renderForm();
    }

    public function uploadData()
    {

        $spreadsheet = $this->_getSpreadsheetFromFile();
        $this->_storeSpreadSheetData($spreadsheet);
        $this->_updateExportExcel();
        $this->_appendMessage("successfulImport");


    }

    public function _getSpreadsheetFromFile()
    {

        $arr_file = explode('.', $_FILES['fileToUpload']['name']);
        $extension = end($arr_file);

        if ('csv' == $extension) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        } else {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }

        $spreadsheet = $reader->load($_FILES['fileToUpload']['tmp_name']);

        return $spreadsheet;
    }

    public function _storeSpreadSheetData($spreadsheet)
    {

        $sheetData = $spreadsheet->getActiveSheet();
        $highestRow = $sheetData->getHighestRow();


        for ($row = 2; $row <= $highestRow; $row++) {

            if ($this->_isEmptyLine($sheetData, $row)) {
                $this->_appendError('emptyLine', $row);
                continue;
            }
            if (!$this->_isOfCorrectAvis($sheetData, $row)) {
                $this->_appendError('inccorectAvis', $row);
                continue;
            }
            if (!$this->_isOfCorrectType($sheetData, $row)) {
                $this->_appendError('inccorectType', $row);
                continue;
            }

            $date = $sheetData->getCellByColumnAndRow($this->indexCloumnPostTile, $row)->getFormattedValue();

            $postId = wp_insert_post(array(
                'post_title' => $date,
                'post_type' => $this->postType,
            ));

            $this->_insertPostMeta($postId, $sheetData, $row);
        }
    }

    public function _isEmptyLine($sheetData, $row)
    {

        $isEmpty = array();

        for ($col = 1; $col <= count($this->columnsNames); $col++) {
            if ($sheetData->getCellByColumnAndRow($col, $row)->getFormattedValue() == "") {
                $isEmpty[] = $col;
            }
        }

        if (count($isEmpty) > 5) {
            return true;
        }


        return false;

    }

    public function _appendError($errorName, $row = 0)
    {

        $errorMessage = $this->listOfErrors[$errorName];

        if (substr_count($this->errors, $errorMessage) == 0) {

            if ($errorName != "unChosedFile") {
                if ($this->errors == "") {
                    $this->errors = "$errorMessage  à la ligne  $row ";

                } else {
                    $this->errors = "$this->errors  <br/> $errorMessage à la ligne $row ";

                }
            } else {
                $this->errors = $errorMessage;
            }


        }
    }

    public function _isOfCorrectAvis($sheetData, $row)
    {

        $col = $this->_getColumnIndex('avis de la commission');

        $avis = $sheetData->getCellByColumnAndRow($col + 1, $row)->getFormattedValue();

        if (in_array(strtolower($avis), $this->correctAvis)) {
            return true;
        }

        return false;

    }

    public function _getColumnIndex($columnName)
    {
        return array_search($columnName, $this->columnsNames);
    }

    public function _isOfCorrectType($sheetData, $row)
    {


        $valideProjectTypesIndexes = array_map(function ($in) {
            return $in["id"];
        }, $this->valideProjectTypes);

        $type = $sheetData->getCellByColumnAndRow($this->_getColumnIndex("type") + 1, $row);

        if (in_array($type, $valideProjectTypesIndexes)) {
            return true;
        }

        return false;
    }

    public function _insertPostMeta($postId, $sheetData, $row)
    {


        for ($col = 1; $col <= count($this->columnsNames); $col++) {

            $value = $sheetData->getCellByColumnAndRow($col, $row)->getFormattedValue();

            switch ($col) {
                case $this->_getColumnIndex('type') + 1:
                    $value = $this->_getConvertedProjectType($value);
                    break;
                case $this->_getColumnIndex('date de la commission') + 1:
                    $value = \DateTime::createFromFormat('m/d/Y', $value)->format('d/m/Y');
                    break;
            }

            update_post_meta($postId, $this->_getColumnNameFormated($this->columnsNames[$col - 1]), $value);
        }

    }

    public function _getConvertedProjectType($originalType)
    {
        switch ($originalType) {
            case 1:
                return 1;
            case 2:
                return 2;
            case 3:
                return 1;
            case 4:
                return 2;
        }
    }

    public function _getColumnNameFormated($column)
    {
        return "wpcf-" . str_replace(' ', '-', $column);
    }

    public function _updateExportExcel()
    {


        $header = $this->columnsNames;

        $rawPosts = Post::_getAllPosts($this->postType);

        $posts = array();
        foreach ($rawPosts as $raw_post) {

            foreach ($this->columnsNames as $columns_name) {
                if (isset($raw_post[$this->_getColumnNameFormated($columns_name)])) {
                    $post[$columns_name] = $raw_post[$this->_getColumnNameFormated($columns_name)];
                } else {
                    $post[$columns_name] = "";
                }
            }
            $posts[] = $post;
        };

        $data = $posts;

        array_unshift($data, $header);

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray($data, null, 'A1');

        $writer = new Xlsx($spreadsheet);


        $writer->save($this->pluginPath . "uploads/$this->postType.xlsx");
    }

    public function _appendMessage($message)
    {
        $message = $this->listOfMessages[$message];

        if (!substr_count($this->messages, $message) > 0) {
            if ($this->messages == "") {
                $this->messages = $message;
            } else {
                $this->messages = $this->messages . "<br/>" . $message;
            }
        }
    }

    public function renderErrors()
    {
        if ($this->errors != "") {
            $this->_render("Alerts/Errors");
        }
    }

    public function renderMessages()
    {
        if ($this->messages != "") {
            $this->_render("Alerts/Messages");
        }

    }

    public function renderForm()
    {

        $pageTitle = $this->_getFormatedPageTitle();

        $this->_render("ImportExportPage", $pageTitle);
    }

    public function _render($pageName, $pageTitle = null)
    {

        require($this->pluginPath . "app/View/$pageName.php");
    }

    public function _getFormatedPageTitle()
    {

        $name = explode(" ", str_replace("_", " ", $this->pageSlug));

        array_splice($name, 0, 1);

        return join(" ", $name);
    }
}
