<?php
class DatabaseHelper
{
    protected $conn;

    public function __construct()
    {
        $this->conn = mysqli_connect(
            'db',             // service name
            'php_docker',     // username
            'password',       // password
            'php_docker'      // database name
        );

        if (!$this->conn) {
            die("DB error: " . mysqli_connect_error());
        }
    }

    public function __destruct()
    {
        mysqli_close($this->conn);
    }

    public function selectAllTableNames()
    {
        $sql = "SHOW TABLES";
        $result = mysqli_query($this->conn, $sql);

        $tables = array();
        while ($row = mysqli_fetch_array($result)) {
            $tables[] = $row[0];
        }

        return $tables;
    }

    public function searchInTable($selectedTable, $selectedAttribute, $searchKeyword)
    {
        $sql = "SELECT * FROM $selectedTable WHERE $selectedAttribute = ?";
        $statement = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($statement, 's', $searchKeyword);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);

        $columns = array();
        $rows = array();
    
        while ($row = mysqli_fetch_assoc($result)) {
            if (empty($columns)) {
                $columns = array_keys($row);
            }
            $rows[] = $row;
        }
    
        mysqli_stmt_close($statement);
    
        return array('columns' => $columns, 'rows' => $rows);
    }

    public function getAll($selectedTable)
    {
        $columnInfoSql = "SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?";
        $statement = mysqli_prepare($this->conn, $columnInfoSql);
        mysqli_stmt_bind_param($statement, 's', $selectedTable);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);

        $columns = array();
        while ($columnInfo = mysqli_fetch_assoc($result)) {
            $columnName = $columnInfo['COLUMN_NAME'];
            $dataType = $columnInfo['DATA_TYPE'];
            
            // Handle CLOB
            $columnExpression = ($dataType === 'longtext') ? "CONVERT($columnName USING utf8) AS $columnName" : $columnName;

            $columns[] = $columnExpression;
        }

        mysqli_free_result($result);
        mysqli_stmt_close($statement);

        $sql = "SELECT " . implode(", ", $columns) . " FROM $selectedTable";
        $result = mysqli_query($this->conn, $sql);

        $res = array();
        $res['columns'] = array();

        if ($result) {
            $numColumns = mysqli_num_fields($result);
            for ($i = 0; $i < $numColumns; $i++) {
                $res['columns'][] = mysqli_fetch_field_direct($result, $i)->name;
            }

            $res['rows'] = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $res['rows'][] = $row;
            }
        }

        mysqli_free_result($result);

        return $res;
    }

    public function selectRowsWithKeys($selectedTable, $keyValuePairs)
    {
        // Prepare statement
        $columnInfoSql = "SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?";
        $columnInfoStatement = mysqli_prepare($this->conn, $columnInfoSql);
        mysqli_stmt_bind_param($columnInfoStatement, "s", $selectedTable);
        mysqli_stmt_execute($columnInfoStatement);
        mysqli_stmt_store_result($columnInfoStatement);
    
        // Bind result variables
        mysqli_stmt_bind_result($columnInfoStatement, $columnName, $dataType);
    
        $columns = array();
        while (mysqli_stmt_fetch($columnInfoStatement)) {
            $columns[] = $columnName;
        }
    
        mysqli_stmt_free_result($columnInfoStatement);
        mysqli_stmt_close($columnInfoStatement);
    
        // WHERE clause
        $whereConditions = [];
        foreach ($keyValuePairs as $key => $value) {
            $whereConditions[] = "$key LIKE ?";
        }
    
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
    
        // Prepare query 
        $sql = "SELECT " . implode(", ", $columns) . " FROM $selectedTable $whereClause";
        $statement = mysqli_prepare($this->conn, $sql);
    
        // Bind parameters for WHERE
        $types = str_repeat("s", count($keyValuePairs));
        $values = array_values($keyValuePairs);
        mysqli_stmt_bind_param($statement, $types, ...$values);
    
        mysqli_stmt_execute($statement);
    
        $result = mysqli_stmt_get_result($statement);
    
        $res = array();
    
        // Fetch column names
        $columns = [];
        while ($field = mysqli_fetch_field($result)) {
            $columns[] = $field->name;
        }
        $res['columns'] = $columns;
    
        // Fetch values as arrays of strings
        $values = [];
        while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
            $values[] = $row;
        }
        $res['values'] = $values;
    
        mysqli_free_result($result);
        mysqli_stmt_close($statement);
    
        return $res;
    }
    

    public function retrieveArticlesData($userId, $supplierId, $sCardId)
    {
        $sql = "CALL GetArticlesInOrder(?, ?, ?)";
        $statement = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($statement, 'iii', $userId, $supplierId, $sCardId);
        mysqli_stmt_execute($statement);
    
        // Fetch results
        $result = mysqli_stmt_get_result($statement);
        $articleCursorResult = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
        mysqli_stmt_close($statement);
    
        return $articleCursorResult;
    }
    

    public function getKeysAndAttributes($tableName)
    {
        $primaryKeys = array();
        $nonPrimaryKeys = array();

        // Primary key columns
        $sql = "
            SELECT 
                COLUMN_NAME AS PRIMARYKEYCOLUMN
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = 'php_docker'
                AND TABLE_NAME = ?
                AND COLUMN_KEY = 'PRI'
        ";
        $statement = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($statement, 's', $tableName);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);

        while ($row = mysqli_fetch_assoc($result)) {
            $primaryKeys[] = $row['PRIMARYKEYCOLUMN'];
        }

        // Non-primary key columns
        $sql = "
            SELECT 
                COLUMN_NAME AS NON_PRIMARY_KEY_COLUMN
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = 'php_docker'
                AND TABLE_NAME = ?
                AND COLUMN_KEY != 'PRI'
        ";
        $statement = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($statement, 's', $tableName);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);

        while ($row = mysqli_fetch_assoc($result)) {
            $nonPrimaryKeys[] = $row['NON_PRIMARY_KEY_COLUMN'];
        }

        return array('primaryKeys' => $primaryKeys, 'nonPrimaryKeys' => $nonPrimaryKeys);
    }

    public function insertIntoTable($tableName, $formData)
    {
        // Validate input
        if (empty($tableName) || !is_array($formData) || empty($formData)) {
            return false;
        }

        // Construct query
        $keys = implode(", ", array_keys($formData));
        $placeholders = rtrim(str_repeat('?, ', count($formData)), ', ');
        $sql = "INSERT INTO $tableName ($keys) VALUES ($placeholders)";

        // Prepare and execute statement
        $statement = mysqli_prepare($this->conn, $sql);
        if ($statement === false) {
            return false;
        }

        // Prepare types string and values array
        $types = '';
        $values = [];
        foreach ($formData as $value) {
            $types .= 's';
            $values[] = $value;
        }

        mysqli_stmt_bind_param($statement, $types, ...$values);

        $res = mysqli_stmt_execute($statement);

        if ($res) {
            mysqli_commit($this->conn);
        } else {
            mysqli_rollback($this->conn);
        }

        mysqli_stmt_close($statement);

        return $res;
    }

    public function updateData($keyAttributesData, $nonKeyAttributesData, $formData)
    {
        $updateQuery = "UPDATE " . $formData['table'] . " SET ";

        // Add non-key attributes to SET
        $setValues = [];
        foreach ($nonKeyAttributesData as $nonKeyName => $nonKeyValue) {
            $updateQuery .= "$nonKeyName = ?, ";
            $setValues[] = $nonKeyValue;
        }

        // Remove last comma
        $updateQuery = rtrim($updateQuery, ', ');

        // WHERE clause for key attributes
        $updateQuery .= " WHERE ";

        $whereValues = [];
        foreach ($keyAttributesData as $keyName => $keyValue) {
            $updateQuery .= "$keyName = ? AND ";
            $whereValues[] = $keyValue;
        }

        // Remove last and
        $updateQuery = substr($updateQuery, 0, -5);

        $statement = mysqli_prepare($this->conn, $updateQuery);
        if ($statement === false) {
            return false;
        }

        // Prepare types string and values array
        $types = str_repeat('s', count($setValues) + count($whereValues));
        $values = array_merge($setValues, $whereValues);

        mysqli_stmt_bind_param($statement, $types, ...$values);
        $res = mysqli_stmt_execute($statement);

        if ($res) {
            mysqli_commit($this->conn);
        } else {
            mysqli_rollback($this->conn);
        }

        mysqli_stmt_close($statement);

        return $res;
    }

    public function deleteData($keyAttributesData, $formData)
    {
        $deleteQuery = "DELETE FROM " . $formData['table'] . " WHERE ";

        $conditions = array();
        $values = array();

        foreach ($keyAttributesData as $keyName => $keyValue) {
            $conditions[] = "$keyName = ?";
            $values[] = $keyValue;
        }

        $deleteQuery .= implode(' AND ', $conditions);

        $statement = mysqli_prepare($this->conn, $deleteQuery);
        if ($statement === false) {
            return false;
        }

        $types = str_repeat('s', count($values));
        mysqli_stmt_bind_param($statement, $types, ...$values);
        $res = mysqli_stmt_execute($statement);

        if ($res) {
            mysqli_commit($this->conn);
        } else {
            mysqli_rollback($this->conn);
        }

        mysqli_stmt_close($statement);

        return $res;
    }
}

