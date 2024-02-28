<?php
require_once('DatabaseHelper.php');

$database = new DatabaseHelper();

if (isset($_GET['table'])) {
    $tableName = $_GET['table'];
    $keyAttributes = array_keys($_GET);
    $keyValues = array_values($_GET);

    // Remove 'table' from the arrays
    unset($keyAttributes[array_search('table', $keyAttributes)]);
    unset($keyValues[array_search($tableName, $keyValues)]);

    $data = $database->selectRowsWithKeys($tableName, array_combine($keyAttributes, $keyValues));

    // Non-primary attributes
    foreach ($keyAttributes as $key) {
        $index = array_search($key, $data["columns"]);
        if ($index !== false) {
            unset($data["columns"][$index]);
            unset($data["values"][0][$index]);
        }
    }

    // Remove empty elements
    $data["columns"] = array_values($data["columns"]);
    $data["values"][0] = array_values($data["values"][0]);

} else {
    // Redirect to the error
    $errorMessage = 'Incomplete or missing parameters';
    header('Location: error.php?message=' . urlencode($errorMessage));
    exit();
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
    <title>Edit <?php echo $tableName; ?> <?php
                                    foreach ($keyAttributes as $index => $keyAttribute) {
                                        echo $keyAttribute . ":" . $keyValues[$index];
                                        if ($index < count($keyAttributes) - 1) {
                                            echo ", ";
                                        }
                                    }
                                ?></title>
    <script>
        $(document).ready(function() {
            $('#editForm').submit(function(e) {
                e.preventDefault();
                var nonKeyAttributesData = {};
                var formData = {};

                formData['table'] = '<?php echo $tableName; ?>';

                var serializedData = $(this).serializeArray();
                // Serialized to object with name-value pairs
                serializedData.forEach(function(item) {
                    nonKeyAttributesData[item.name] = item.value;
                });

                var keyAttributesData = <?php echo json_encode(array_combine($keyAttributes, $keyValues)); ?>;

                $('input, button', this).prop('disabled', true);
                $('#notification').text('Updating data...').removeClass('text-danger text-success').addClass(' text-primary');

                $.ajax({
                    type: 'GET',
                    url: 'update.php',
                    data: { keyAttributesData: keyAttributesData, nonKeyAttributesData: nonKeyAttributesData, formData: formData },
                    success: function(response) {
                        $('input, button', '#editForm').prop('disabled', false);

                        if (response === 'success') {
                            $('#notification').text('Data has successfully been updated!').removeClass('text-primary text-danger').addClass('text-success');
                        } else {
                            $('#notification').text('ERROR! Failed to update data!').removeClass('text-primary text-success').addClass(' text-danger');
                        }
                    },
                    error: function() {
                        $('input, button', '#editForm').prop('disabled', false);
                        $('#notification').text('ERROR! Failed to updata data!').removeClass('text-primary text-success').addClass(' text-danger');
                    }
                });
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
                        <h2 class="pt-3 pb-4 font-bold font-up deep-purple-text"> EDIT: <?php echo $tableName; ?></h2>

                        <form id="editForm" action="update.php" name="table" method="get">
                            <?php
                            foreach ($data['values'] as $values) {
                                for ($i = 0; $i < count($data['columns']); $i++) {
                                    $columnName = $data['columns'][$i];
                                    $value = $values[$i];

                                    echo "<div class='form-group row'>";
                                    echo "<label class='col-md-4 col-form-label font-weight-bold'>$columnName:</label>";
                                    echo "<div class='col-md-8'>";
                                    echo "<input type='text' class='form-control' name='$columnName' value='$value'>";
                                    echo "</div>";
                                    echo "</div>";
                                }
                            }
                            ?>
                            <div class="container">
                                <div class="row justify-content-end">
                                    <a href="index.php" class="btn btn-secondary mr-auto col col-2" role="button">Home</a>
                                    <p id="notification" class="col col-8 text-primary text-center mb-1 mt-1"></p>
                                    <button type="submit" class="btn btn-primary col col-2">Submit</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
    </main>
</body>
</html>

