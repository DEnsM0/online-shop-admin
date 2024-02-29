package utils;

import java.sql.Connection;
import java.sql.SQLException;
import java.util.*;

public class Inserter {
    private static final String CLIENT_TABLE = "client";
    private static final String VENDOR_TABLE = "vendor";
    private static final String CATEGORIES_TABLE = "category";
    private static final String ITEM_TABLE = "item";
    private static final String SUPPLIER_TABLE = "supplier";
    private static final String SHOPPING_CART_TABLE = "shopping_cart";
    private static final String CONTAINS_TABLE = "contains";
    private static final String REVIEW_TABLE = "review";
    private static final String ORDER_TABLE = "order_";
    private static final Integer CLOTHES_CATEGORY_ID = 1;
    private static final Integer ELECTRONICS_CATEGORY_ID = 2;
    private static final Integer CARPARTS_CATEGORY_ID = 3;
    public static void insertClients(Connection con, int n) throws SQLException {
        List<String> names = Reader.readNames(n);
        List<String> surnames = Reader.readSurnames(n);
        List<String> statements = new ArrayList<>();
        for (int i = 0; i < n; i++){
            statements.add(Composer.composeClientStatement(CLIENT_TABLE,names.get(i), surnames.get(i)));
        }
        Executor.executeAll(con, statements, CLIENT_TABLE);
    }
    public static void insertVendors(Connection con, int n) throws SQLException {
        int numberOfVendors = n;
        List<String[]> addresses = Reader.readRandomAddresses(n);
        List<String[]> vendorNames = Generator.generateVendorNames(Reader.readVendorNames(), numberOfVendors);
        List<String> statements = new ArrayList<>();
        for (int i = 0; i < n; i++){
            statements.add(Composer.composeVendorStatement(VENDOR_TABLE,vendorNames.get(i), addresses.get(i)));
        }
        Executor.executeAll(con, statements, VENDOR_TABLE);
    }
    public static void insertCategories(Connection con) throws SQLException {
        int id = 100;
        Map<Pair<Integer, Integer>, String> ClothesCategories = Generator.generateCategories(Reader.readClothesCategories(), id, CLOTHES_CATEGORY_ID);
        Map<Pair<Integer, Integer>, String> ElectronicsCategories = Generator.generateCategories(Reader.readElectronicsCategories(), id, ELECTRONICS_CATEGORY_ID);
        Map<Pair<Integer, Integer>, String> CarPartsCategories = Generator.generateCategories(Reader.readCarPartsCategories(), id, CARPARTS_CATEGORY_ID);
        Map<Pair<Integer, Integer>, String> combinedCategories = new HashMap<>();
        combinedCategories.putAll(ClothesCategories);
        combinedCategories.putAll(ElectronicsCategories);
        combinedCategories.putAll(CarPartsCategories);
        List<String> statements = new ArrayList<>();
        statements.add(Composer.composeCategoryStatement(CATEGORIES_TABLE, CLOTHES_CATEGORY_ID, "Clothes", 0));
        statements.add(Composer.composeCategoryStatement(CATEGORIES_TABLE, ELECTRONICS_CATEGORY_ID, "Electronics", 0));
        statements.add(Composer.composeCategoryStatement(CATEGORIES_TABLE, CARPARTS_CATEGORY_ID, "Car Parts", 0));
        List<Map.Entry<Pair<Integer, Integer>, String>> sortedCombinedCategories = combinedCategories.entrySet().stream()
                .sorted(Comparator.comparingInt(e -> e.getKey().getL()))
                .toList();
        sortedCombinedCategories.forEach(entry ->
                statements.add(Composer.
                        composeCategoryStatement(CATEGORIES_TABLE,
                                entry.getKey().getL(),
                                entry.getValue(),
                                entry.getKey().getR())));
        Executor.executeAll(con, statements, CATEGORIES_TABLE);
    }
    public static void insertItems (Connection con,int n, List<Integer> vendorsIds) throws SQLException {
        int[] numberOfArticles = Generator.divideRandomly(n);
        List<String[]> clothesNames = Generator.generateArticleNames(Reader.readClothes(), numberOfArticles[0]);
        List<String[]> carPartsNames = Generator.generateArticleNames(Reader.readCarParts(), numberOfArticles[1]);
        List<String[]> electronicsNames = Generator.generateArticleNames(Reader.readElectronics(), numberOfArticles[2]);
        List<String> statements = new ArrayList<>();
        for (int i = 0; i < numberOfArticles[0]; i++){
            statements.add(Composer.composeArticleStatement(ITEM_TABLE,
                    clothesNames.get(i),
                    Generator.getRandomClothingBrand(),
                    Generator.getRandomSize(),
                    vendorsIds.get(Generator.getRandomInt(0, vendorsIds.size()-1))));
        }
        for (int i = 0; i < numberOfArticles[1]; i++){
            statements.add(Composer.composeArticleStatement(ITEM_TABLE,
                    carPartsNames.get(i),
                    Generator.getRandomElectronicsBrand(),
                    "NULL",
                    vendorsIds.get(Generator.getRandomInt(0, vendorsIds.size()-1))));
        }
        for (int i = 0; i < numberOfArticles[2]; i++){
            statements.add(Composer.composeArticleStatement(ITEM_TABLE,
                    electronicsNames.get(i),
                    Generator.getRandomCarPartsBrand(),
                    "NULL",
                    vendorsIds.get(Generator.getRandomInt(0, vendorsIds.size()-1))));
        }
        Executor.executeAll(con, statements, ITEM_TABLE);
    }
    public static void insertSuppliers (Connection con,int n) throws SQLException {
        List<String[]> addresses = Reader.readRandomAddresses(n);
        List<String> statements = new ArrayList<>();
        for (int i = 0; i < n; i++){
            statements.add(Composer.composeDeliveryStatement(SUPPLIER_TABLE, addresses.get(i)));
        }
        Executor.executeAll(con, statements, SUPPLIER_TABLE);
    }

    public static void insertShoppingCarts (Connection con,int n) throws SQLException {
        List<String> statements = new ArrayList<>();
        for (int i = 0; i < n; i++){
            statements.add(Composer.composeShoppingCartStatement(SHOPPING_CART_TABLE));
        }
        Executor.executeAll(con, statements, SHOPPING_CART_TABLE);
    }

    public static void insertContains (Connection con, List<Integer> sCartIds, List<Integer> articleIds) throws SQLException {
        List<String> statements = new ArrayList<>();
        for (int i = 0; i < sCartIds.size(); i++){
            statements.add(Composer.composeContainsStatement(CONTAINS_TABLE, sCartIds.get(i), articleIds.get(Generator.getRandomInt(0,articleIds.size()-1))));
        }
        Executor.executeAll(con, statements, CONTAINS_TABLE);
    }

    public static void insertReviews (Connection con, List<Integer> clientsIds, List<Integer> articleIds) throws SQLException {
        List<String> statements = new ArrayList<>();
        int numberOfReviews = (Math.min(clientsIds.size(), articleIds.size()));
        int numberOfNegativeReviews = numberOfReviews - Generator.getRandomInt(0, numberOfReviews);
        List<String> negativeReviews = Reader.readNegativeReviews(numberOfNegativeReviews);
        List<String> positiveReviews = Reader.readPositiveReviews(numberOfReviews - numberOfNegativeReviews);
        for (int i = 0; i < numberOfNegativeReviews; i++){
            statements.add(Composer.composeReviewStatement(REVIEW_TABLE,
                                                            clientsIds.get(Generator.getRandomInt(0, clientsIds.size()-1)),
                                                            articleIds.get(Generator.getRandomInt(0, articleIds.size()-1)),
                                                            negativeReviews.get(i),
                                                            Generator.getRandomInt(1,2)));
        }
        for (int i = 0; i < numberOfReviews - numberOfNegativeReviews; i++){
            statements.add(Composer.composeReviewStatement(REVIEW_TABLE,
                                                            clientsIds.get(i),
                                                            articleIds.get(i),
                                                            positiveReviews.get(i),
                                                            Generator.getRandomInt(3,5)));
        }
        Executor.executeAll(con, statements, REVIEW_TABLE);
    }

    public static void insertOrders (Connection con, int n, List<Integer> clientsIds,List<Integer> deliveryIds, List<Integer> sCardsIds) throws SQLException {
        List<String> statements = new ArrayList<>();
        Random random = new Random();
        List<String[]> addresses = Reader.readRandomAddresses(n);
        List<String> names = Reader.readNames(n);
        List<String> surnames = Reader.readSurnames(n);
        for (int i = 0; i < n; i++){
            statements.add(Composer.composeOrderStatement(ORDER_TABLE,
                                                        clientsIds.get(Generator.getRandomInt(0, clientsIds.size()-1)),
                                                        deliveryIds.get(Generator.getRandomInt(0, deliveryIds.size()-1)),
                                                        sCardsIds.get(i),
                                                        addresses.get(i),
                                                        addresses.get(random.nextBoolean() ? i : Generator.getRandomInt(1,n)),
                                                        names.get(i),
                                                        surnames.get(i)));
        }
        Executor.executeAll(con, statements, ORDER_TABLE);
    }

}
