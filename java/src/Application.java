import utils.Executor;
import utils.Inserter;

import java.sql.Connection;
import java.sql.DriverManager;
import java.util.List;

public class Application {
    /**
     * table names
     */
    private static final String CLIENT_TABLE = "client";
    private static final String VENDOR_TABLE = "vendor";
    private static final String ITEM_TABLE = "item";
    private static final String SUPPLIER_TABLE = "supplier";
    private static final String SHOPPING_CART_TABLE = "shopping_cart";

    /**
     * table id names
     */
    private static final String CLIENT_ID = "client_id";
    private static final String VENDOR_ID = "vendor_id";
    private static final String ITEM_ID = "item_id";
    private static final String SHOPPING_CART_ID = "shopping_cart_id";
    private static final String SUPPLIER_ID = "supplier_id";


    public static void main(String args[]) {
        try {
            System.setProperty("file.encoding" , "UTF-8");
            // This will load the MySQL driver into the memory
            Class.forName("com.mysql.cj.jdbc.Driver");

            // Connection details
            String database = "jdbc:mysql://localhost:3306/php_docker?characterEncoding=utf8";
            String user = "php_docker";
            String pass = "password";

            // Establish a connection to the database
            Connection con = DriverManager.getConnection(database, user, pass);
            Inserter.insertClients(con, 1000);
            Inserter.insertVendors(con, 100);
            List<Integer> allVendorIds = Executor.getTableIds(con,VENDOR_TABLE,VENDOR_ID);
            List<Integer> allClientIds = Executor.getTableIds(con,CLIENT_TABLE,CLIENT_ID);

            Inserter.insertCategories(con);

            Inserter.insertItems(con,5000, allVendorIds);
            List<Integer> allItemsIds = Executor.getTableIds(con,ITEM_TABLE,ITEM_ID);

            Inserter.insertReviews(con,allClientIds, allItemsIds);

            Inserter.insertSuppliers(con, 10);
            List<Integer> allSupplierIds = Executor.getTableIds(con,SUPPLIER_TABLE,SUPPLIER_ID);

            Inserter.insertShoppingCarts(con,allClientIds.size());
            List<Integer> allShoppingCartsIds = Executor.getTableIds(con,SHOPPING_CART_TABLE,SHOPPING_CART_ID);

            Inserter.insertContains(con, allShoppingCartsIds, allItemsIds);
            Inserter.insertOrders(con, allShoppingCartsIds.size(), allClientIds, allSupplierIds, allShoppingCartsIds);
            con.close();
        } catch (Exception e) {
            System.err.println(e.getMessage());
        }
    }
}
