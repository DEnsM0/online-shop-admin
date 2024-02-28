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

}

