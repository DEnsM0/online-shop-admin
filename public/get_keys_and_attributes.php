<?php
require_once('DatabaseHelper.php');

$database = new DatabaseHelper();

if (isset($_GET['table'])) {
    $tableName = $_GET['table'];

    $result = $database->getKeysAndAttributes($tableName);
    // print_r($result['primaryKeys']);
    // print_r($result['nonPrimaryKeys']);

    echo json_encode($result);
} else {
    echo json_encode(['error' => 'Missing table parameter']);
}
?>
