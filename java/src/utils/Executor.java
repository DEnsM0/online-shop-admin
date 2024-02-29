package utils;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class Executor {
    public static void executeAll(Connection con, List<String> statements, String table) throws SQLException {
        Statement stmt = con.createStatement();
        statements.stream()
                .forEach( s -> {
                    try {
                        //System.out.println(s);
                        stmt.executeUpdate(s);
                        //executeUpdate Method: Executes the SQL statement, which can be an INSERT, UPDATE, or DELETE statement
                    } catch (SQLException e) {
                        System.err.println("Error while executing INSERT INTO statement: " + e.getMessage());
                    }
                });
        // Check number of datasets in person table
        ResultSet rs = stmt.executeQuery(String.format("SELECT COUNT(*) FROM %s", table));
        if (rs.next()) {
            int count = rs.getInt(1);
            System.out.println("Number of datasets: " + count);
        }
        // Clean up connections
        rs.close();
        stmt.close();
    }

    public static List<Integer> getTableIds(Connection con, String tableName, String id) throws SQLException {
        List<Integer> tableIds = new ArrayList<>();

        String sqlQuery = String.format("SELECT %s FROM %s", id, tableName);

        try (PreparedStatement preparedStatement = con.prepareStatement(sqlQuery)) {
            try (ResultSet resultSet = preparedStatement.executeQuery()) {
                while (resultSet.next()) {
                    tableIds.add(resultSet.getInt(id));
                }
            }
        }

        return tableIds;
    }
}
