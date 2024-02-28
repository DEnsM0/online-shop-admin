<?php
require_once('DatabaseHelper.php');

$database = new DatabaseHelper();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $keyAttributesData = $_GET['keyAttributesData'];
    $nonKeyAttributesData = $_GET['nonKeyAttributesData'];
    $formData = $_GET['formData'];

    //var_dump($keyAttributesData);
    //var_dump($nonKeyAttributesData);
   // var_dump($formData);

    $result = $database->updateData($keyAttributesData, $nonKeyAttributesData, $formData);

    if ($result === true) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
?>
