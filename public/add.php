<?php
require_once('DatabaseHelper.php');

$database = new DatabaseHelper();

$all_table_names = $database->selectAllTableNames();
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
    <title>Add New</title>
    <script>
        $(document).ready(function() {
            // Event listener for table
            $('#tableSelect').change(function() {
                var selectedTable = $(this).val();
                $('#submitButton').prop('disabled', true);
                $('#notification').text('Loading fields...').removeClass('text-success text-danger').addClass('text-primary');
                // Ajax request to get keys for selectedTable
                $.ajax({
                    type: 'GET',
                    url: 'get_keys_and_attributes.php',
                    data: { table: selectedTable },
                    success: function(data) {
                        var result = JSON.parse(data);
                        $('.attribute-inputs').remove();
                        $('#notification').text('');
                        // Input fields primary keys
                        if(selectedTable === "BESTELLUNG" || selectedTable === "BEINHALTET" || selectedTable === "KATEGORIE" || selectedTable === "BEWERTUNG"){
                            result.primaryKeys.forEach(function(attribute) {
                            if (selectedTable === "BEWERTUNG" && attribute !== "ARTIKELID") {
                                return;
                            }
                            var inputField = '<div class="form-group row attribute-inputs">' +
                                '<label class="col-md-4 col-form-label font-weight-bold">' + attribute + ':</label>' +
                                '<div class="col-md-8">' +
                                '<input type="text" class="form-control" name="' + attribute + '">' +
                                '</div>' +
                                '</div>';

                            $('#inputs').append(inputField);
                            });
                        }

                        // Input fields non-primary keys
                        result.nonPrimaryKeys.forEach(function(attribute) {
                            var inputField = '<div class="form-group row attribute-inputs">' +
                                '<label class="col-md-4 col-form-label font-weight-bold">' + attribute + ':</label>' +
                                '<div class="col-md-8">' +
                                '<input type="text" class="form-control" name="' + attribute + '">' +
                                '</div>' +
                                '</div>';

                            $('#inputs').append(inputField);
                        });
                        $('#selectedTableName').text(selectedTable);
                        $('#submitButton').prop('disabled', selectedTable === '0');
                    },
                    error: function() {
                        $('#notification').text('');
                        console.log('Error fetching keys and attributes');
                    }
                });
            });

            $('#addForm').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serializeArray();
                var selectedTable = $('#tableSelect').val();

                // Selected table name as first element in formData
                formData.unshift({ name: 'table', value: selectedTable });

                $('#notification').text('Adding data...').removeClass('text-success text-danger').addClass('text-primary');

                $.ajax({
                    type: 'GET',
                    url: 'insert.php', 
                    data: formData,
                    success: function (response) {
                        if (response === 'success') {
                            $('#notification').text('Data has successfully been added!').removeClass('text-primary text-danger').addClass('text-success');
                        } else {
                            $('#notification').text('ERROR! Failed to add data!').removeClass('text-primary text-success').addClass('text-danger');
                        }
                        console.log(response);
                    },
                    error: function () {
                        $('#notification').text('ERROR! Failed to add data!').removeClass('text-primary text-success').addClass('text-danger');
                        console.log('Error during insertion');
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
                    <h2 class="pt-3 pb-4 font-bold font-up deep-purple-text"> ADD: <span id="selectedTableName"></span></h2>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="input-group">
                                <select class="custom-select mb-2" id="tableSelect" name="table">
                                    <option value="0" selected>Choose table</option>
                                    <?php foreach ($all_table_names as $table_name) : ?>
                                        <option value="<?php echo $table_name; ?>"><?php echo $table_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <form id="addForm" action="insert.php" method="get">
                        <div id="inputs">

                        </div>
                        <div class="container">
                            <div class="row justify-content-end">
                                <a href="index.php" class="btn btn-secondary mr-auto col col-2" role="button">Home</a>
                                <p id="notification" class="col col-8 text-primary text-center mb-1 mt-1"></p>
                                <button id="submitButton" type="submit" class="btn btn-primary col col-2" disabled>Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
