<?php

require_once('DatabaseHelper.php');

$database = new DatabaseHelper();

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['submit'])) {
    $selectedTable = $_GET['table'];
    $searchKeyword = $_GET['search'];
    $selectedAttribute = $_GET['attribute'];

    if(empty($searchKeyword)){
        $searchResult = $database->getAll($selectedTable);
    } else {
        $searchResult = $database->searchInTable($selectedTable, $selectedAttribute, $searchKeyword);
    }

    header('Content-Type: application/json');
    echo json_encode($searchResult, JSON_UNESCAPED_UNICODE);
    exit();
}
?>
