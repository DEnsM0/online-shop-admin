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
    <title>CRUD APPLICATION "ONLINE SHOP"-ADMIN"</title>

    <script>
        $(document).ready(function () {
            $('#toAddButton').click(function() {
                window.location.href = 'add.php';
            });
            var selectedTable;
            var attributes = [];
            // On 1st dropdown change
            $('#tableSelect').change(function () {
                // Turn on/off elements in the search bar
                selectedTable = $(this).val();
                $('#notification').text('');
                $('#resultTable').addClass('d-none');

                // Change 2nd dropdown values
                if (selectedTable !== '0') {
                    $('#searchInput, #attributeSelect, #submitButton').prop('disabled', true);
                    var attributeSelect = $('#attributeSelect');
                    $('#notification').text('Loading attributes...').removeClass('text-secondary text-danger').addClass('text-primary');
                    // Make ajax request to fetch attributes
                    $.ajax({
                        url: 'get_keys_and_attributes.php', 
                        method: 'GET',
                        data: { table: selectedTable },
                        success: function (data) {
                            try {
                                //console.log(data);
                                attributes = JSON.parse(data);
                                //console.log(attributes);
                                var attributesCombined = attributes.primaryKeys.concat(attributes.nonPrimaryKeys);
                                attributeSelect.empty();
                                $.each(attributesCombined, function (index, value) {
                                    attributeSelect.append('<option value="' + value + '">' + value + '</option>');
                                });

                                // Set default
                                attributeSelect.val(attributesCombined[0]);
                                $('#searchInput, #attributeSelect, #submitButton').prop('disabled', selectedTable === '0');
                                $('#notification').text('');
                            } catch (error) {
                                $('#notification').text('ERROR! Failed to load attributes!').removeClass('text-primary text-secondary').addClass('text-danger');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            $('#notification').text('ERROR! Failed to load attributes!').removeClass('text-primary text-secondary').addClass('text-danger');
                        }
                    });
                }
            });

            // On form submit
            $('#searchForm').submit(function (event) {
                event.preventDefault(); // Prevent searchForm from submitting normally
                $('#resultTable').addClass("table-bordered")
                $('#notification').text('Loading data...').removeClass('text-secondary text-danger').addClass('text-primary');

                var selectedTable = $('#tableSelect').val();
                var searchKeyword = $('#searchInput').val();
                var selectedAttribute = $('#attributeSelect').val();

                console.log(searchKeyword);

                // Make ajax request to fetch search results
                $.ajax({
                    url: 'search.php',
                    method: 'GET',
                    data: {
                        table: selectedTable,
                        search: searchKeyword,
                        attribute: selectedAttribute,
                        submit: true //  Flag ajax request
                    },
                    success: function (searchResult) {
                        //console.log(searchResult);
                        // Display searchResult
                        var resultTable = $('#resultTable');
                        resultTable.find('thead').empty();
                        resultTable.find('tbody').empty();

                        var theadRow = $('#resultTable thead');
                        theadRow.empty();
                        theadRow.append('<tr>');
                        $.each(searchResult.columns, function (index, columnName) {

                            theadRow.append('<th scope="col">' + columnName + '</th>');
                        });
                        theadRow.append('</tr>')

                        // Update table rows
                        var tbody = $('#resultTable tbody');
                        tbody.empty();
                        if(typeof searchResult.rows !== 'undefined' && searchResult.rows.length > 0){
                            $.each(searchResult.rows, function (index, row) {
                            var tr = $('<tr>').addClass('table-row');

                            // Row event handler
                            tr.click(function () {
                                // Values corresponding to key attributes
                                var primaryKeyValues = attributes.primaryKeys.map(function (key) {
                                    return row[key];
                                });

                                var url = 'details.php?table=' + selectedTable;
                                for (var i = 0; i < attributes.primaryKeys.length; i++) {
                                    url += '&' + encodeURIComponent(attributes.primaryKeys[i]) + '=' + encodeURIComponent(primaryKeyValues[i]);
                                }
                                //console.log('URL:', url);
                                // Redirect to details
                                window.location.href = url;
                                // console.log('Table: ' + selectedTable);
                                // console.log('Columns: ' + attributes.primaryKeys.join(', '));
                                // console.log('Values: ' + primaryKeyValues.join(', '));
                            });

                            // Append created row to tbody
                            $.each(row, function (index, value) {
                                tr.append('<td>' + value + '</td>');
                            });
                            tbody.append(tr);
                        });
                        $('#notification').text('');
                        $('#resultTable').removeClass('d-none');
                        } else{
                            $('#resultTable').addClass('d-none');
                            $('#notification').text('No data found...').removeClass('text-primary text-danger').addClass('text-secondary');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        $('#notification').text('ERROR! Failed to load data!').removeClass('text-primary text-secondary').addClass('text-danger');
                    }
                });
            });
        }); 
    </script>
    <style>

    .table-row:hover {
        cursor: pointer;
    }
    </style>
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
                    <div class="row">
                        <div class="col-md-12">
                            <h1 class="pt-3 pb-4 text-center font-bold font-up deep-purple-text">CRUD APPLICATION "ONLINE SHOP"-ADMIN</h1>
                            <div class="container mb-1">
                                <button class="btn btn-success" type="submit" id="toAddButton" name="submit">Add New</button>
                            </div>
                            <h3 class="mt-2 mb-1 pl-3 pr-1 font-bold font-up deep-purple-text">Database search:</h3>
                            <form id="searchForm" method="get" class="table table-striped" name="searchForm">
                                <div class="input-group container">
                                    <select class="custom-select col col-3" id="tableSelect" name="table">
                                        <option value="0" selected>Choose table</option>
                                        <?php foreach ($all_table_names as $table_name) : ?>
                                            <option value="<?php echo $table_name; ?>"><?php echo $table_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input class="form-control my-0 py-1 pl-3 purple-border col col-4" type="text" id="searchInput" name="search" placeholder="Search something here..." aria-label="Search" disabled>
                                    <select class="custom-select col col-3" id="attributeSelect" name="attribute" disabled></select>
                                    <button class="btn btn-primary col col-2" type="submit" id="submitButton" name="submit" disabled>Search</button>
                                </div>
                            </form>      
                        </div>
                    </div>
                    <div class= "table-responsive">
                        <table class="table table-sm table-hover" id="resultTable">
                            <thead></thead>
                            <tbody></tbody>
                        </table>   
                    </div>
                    <p id="notification" class="text-primary text-center mb-1 mt-1"></p>    
                </div>
            </div>
        </div>
    </main>
</body>
</html>
