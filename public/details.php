<?php
require_once('DatabaseHelper.php');

$database = new DatabaseHelper();

if (isset($_GET['table'])) {
    $tableName = $_GET['table'];

    $keyAttributes = array_keys($_GET);
    $keyValues = array_values($_GET);

    // Remove table from the arrays
    unset($keyAttributes[array_search('table', $keyAttributes)]);
    unset($keyValues[array_search($tableName, $keyValues)]);

    $data = $database->selectRowsWithKeys($tableName, array_combine($keyAttributes, $keyValues));
    // var_dump($data['values']);
    // var_dump(count($data['values']));

    $articlesData = array();
    //echo $tableName;
    if ($tableName == 'order_') {
        // articles data
        $userId = $keyValues[array_search('client_id', $keyAttributes)];
        $supplierId = $keyValues[array_search('supplier_id', $keyAttributes)];
        $sCardId = $keyValues[array_search('shopping_cart_id', $keyAttributes)];

        $articlesData = $database->retrieveArticlesData($userId, $supplierId, $sCardId);
        //var_dump($userId);
        //var_dump($supplierId);
        //var_dump($sCardId);
        //var_dump($articlesData);
    }
} else {
 echo'Error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="icon" href="images/database_small_black.png">
    <title>Details <?php echo $tableName; ?> <?php
                                    foreach ($keyAttributes as $index => $keyAttribute) {
                                        echo $keyAttribute . ":" . $keyValues[$index];
                                        if ($index < count($keyAttributes) - 1) {
                                            echo ", ";
                                        }
                                    }
                                ?></title>
    <script>
        $(document).ready(function() {
            $('#deleteConfirm').click(function() {
                $('#deleteConfirm, #deleteCancel, #deleteClose').prop('disabled', true);

                $('#youSure').addClass('d-none');
                $('#notification').text('Deleting data...').removeClass('text-success text-danger').addClass('text-primary');
                var formData = {};
                formData['table'] = '<?php echo $tableName; ?>';
                var keyAttributesData = <?php echo json_encode(array_combine($keyAttributes, $keyValues)); ?>;
                $.ajax({
                    type: 'GET', 
                    url: 'delete.php',
                    data: {keyAttributesData: keyAttributesData, formData: formData},
                    success: function(response) {
                        if (response === 'success') {
                            $('#notification').text('Data has successfully been deleted!').removeClass('text-primary text-danger').addClass('text-success');
                            $('#deleteConfirm, #deleteCancel, #deleteClose').addClass('d-none');
                            $('#deleteHome').removeClass('d-none');
                        } else {
                            $('#notification').text('ERROR! Failed to delete data!').removeClass('text-primary text-success').addClass(' text-danger');
                            $('#deleteConfirm, #deleteCancel, #deleteClose').prop('disabled', false);

                        }
                    },
                    error: function() {
                        $('#notification').text('ERROR! Failed to delete data!').removeClass('text-primary text-success').addClass(' text-danger');
                            $('#deleteConfirm, #deleteCancel, #deleteClose').prop('disabled', false);
                    }
                });
            });
            $('#deleteCancel, #deleteClose').click(function() {
                $('#youSure').removeClass('d-none');
                $('#notification').text('');
            });
        });
    </script>
</head>

<body class="hm-gradient">
    <main>
    <nav class="navbar navbar-expand-lg  navbar-dark bg-dark">
            <a class="navbar-brand" href="index.php">
            <img src="images/database.png" width="32" height="32" alt="">
            </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-item nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a>
                    <a class="nav-item nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'add.php') ? 'active' : ''; ?>" href="add.php">Add</a>
                </div>
            </div>
        </nav>
        <div class="container mt-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="pt-3 pb-4 font-bold font-up deep-purple-text"> DETAILS: <?php echo $tableName; ?></h2>

                    <?php if (empty($data['values'][0])): ?>
                        <p class="text secondary">
                            <?php echo $tableName; ?> 
                            (<?php
                                foreach ($keyAttributes as $index => $keyAttribute) {
                                    echo $keyAttribute . ":" . $keyValues[$index];
                                    if ($index < count($keyAttributes) - 1) {
                                        echo ", ";
                                    }
                                }
                            ?>) not found.
                        </p>
                    <?php endif; ?>
                    
                    <?php
                    // Each row as column name and value
                    foreach ($data['values'] as $values) {
                        for ($i = 0; $i < count($data['columns']); $i++) {
                            $columnName = $data['columns'][$i];
                            $value = $values[$i];

                            echo "<div class='row mb-2'>";
                            echo "<p class='col-md-4 font-weight-bold'>$columnName:</p>";
                            echo "<p class='col-md-8'>$value</p>";
                            echo "</div>";
                        }
                    }
                    ?>

                    <?php if ($tableName == 'order_'): ?>
                        <div id="articlesOfOrder" class="mb-2">
                            <div class="card">
                                <div class="card-header" id="headingOne">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                            ITEMS IN ORDER:
                                        </button>
                                    </h5>
                                </div>

                                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#articlesOfOrder">
                                    <div class="card-body">
                                        <?php foreach ($articlesData as $article): ?>
                                            <div class="border-top border-bottom">
                                                <div class='row mb-2'>
                                                    <p class='col-md-4 font-weight-bold'>ITEM:</p>
                                                    <p class='col-md-8'><?php echo $article['item_name']; ?></p>
                                                </div>
                                                <div class='row mb-2'>
                                                    <p class='col-md-4 font-weight-bold'>PRICE:</p>
                                                    <p class='col-md-8'><?php echo $article['price']; ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($data['values'][0])): ?>
                        <form action="edit.php" method="get">
                            <div class="container">
                                <div class="row justify-content-end">
                                    <input type="hidden" name="table" value="<?php echo htmlspecialchars($tableName, ENT_QUOTES, 'UTF-8'); ?>">
                                    
                                    <?php
                                    // Hidden inputs for key attributes
                                    foreach ($keyAttributes as $index => $keyAttribute) {
                                        echo "<input type='hidden' name='$keyAttribute' value='" . htmlspecialchars($keyValues[$index], ENT_QUOTES, 'UTF-8') . "'>\n";
                                    }
                                    ?>
                                    <a href="index.php" class="btn btn-secondary mr-auto col col-2" role="button">Home</a>
                                    <?php if ($tableName != 'contains'): ?>
                                        <button class="btn btn-primary mr-2 col col-2" type="submit">Edit</button>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-danger col col-2" id="deleteButton" data-toggle="modal" data-target="#modalDelete" data-backdrop="static" data-keyboard="false">Delete</button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>

                    <div class="modal" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="deleteModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">DELETE: <?php echo $tableName; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="deleteClose">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p id="youSure">
                                Are you sure you want to permanently delete <?php echo $tableName; ?> 
                                (<?php
                                    foreach ($keyAttributes as $index => $keyAttribute) {
                                        echo $keyAttribute . ":" . $keyValues[$index];
                                        if ($index < count($keyAttributes) - 1) {
                                            echo ", ";
                                        }
                                    }
                                ?>)?
                                </p>
                                <p id="notification" class="text-primary mb-1 mt-1"></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="deleteCancel">Close</button>
                                <button type="button" class="btn btn-danger" id="deleteConfirm">Delete</button>
                                <form action="index.php" method="get">
                                    <button type="submit" class="btn btn-secondary d-none" id="deleteHome">Home</button>
                                </form>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
