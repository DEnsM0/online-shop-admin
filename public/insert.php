<?php
require_once('DatabaseHelper.php');

$database = new DatabaseHelper();

if (isset($_GET['table'])) {
    $tableName = $_GET['table'];

    // Remove table from arrays
    unset($_GET['table']);

    $formData = $_GET;

    $result = $database->insertIntoTable($tableName, $formData);

    if ($result === true) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
?>
