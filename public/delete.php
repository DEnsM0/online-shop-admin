<?php
require_once('DatabaseHelper.php');

$database = new DatabaseHelper();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $keyAttributesData = $_GET['keyAttributesData'];
    $formData = $_GET['formData'];

    // var_dump($keyAttributesData);
    // var_dump($formData);

    $result = $database->deleteData($keyAttributesData, $formData);

    if ($result === true) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
?>
