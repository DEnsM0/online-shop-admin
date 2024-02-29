package utils;

import java.text.DecimalFormat;
import java.time.LocalDate;
import java.time.format.DateTimeFormatter;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.stream.Collectors;

public class Composer {
    private static final String ITEM_COLUMNS = "item_name, price, size, color, brand, description, availability, discount, vendor_id, category_id";
    private static final String CATEGORY_COLUMNS = "category_id, category_name, overcategory_id";
    private static final String VENDOR_COLUMNS = "vendor_name, login_email, password, company_name, phone, street, house, zip, city, country";
    private static final String CLIENT_COLUMNS = "client_name, login_email, password, name, surname, phone";
    private static final String SUPPLIER_COLUMNS = "company_name, email, phone, street, house, zip, city, country";
    private static final String SHOPPING_CART_COLUMNS = "total_price, total_number";
    private static final String CONTAINS_COLUMNS = "shopping_cart_id, item_id";
    private static final String REVIEW_COLUMNS = "item_id, comment, stars, client_id";
    private static final String ORDER_COLUMNS = "client_id, supplier_id, shopping_cart_id, order_date, delivery_date, delivery_street, delivery_house, delivery_zip, delivery_city, delivery_country, billing_street, billing_house, billing_zip, billing_city, billing_country, cardholder, card_number, expiry_date, check_digit";

    public static String composeOrderStatement(String table, Integer clientId, Integer deliveryId, Integer sCartId, String[] deliveryAddress, String[] billingAddress, String name, String surname) {
        List<String> values = new ArrayList<>();
        values.add(String.valueOf(clientId));
        values.add(String.valueOf(deliveryId));
        values.add(String.valueOf(sCartId));
        LocalDate localDate = Generator.generateFutureDate(Generator.getRandomInt(-100, 100));
        values.add(parseLocalDate(localDate));
        values.add(parseLocalDate(localDate.plusDays(Generator.getRandomInt(5, 14))));
        //deliveryAddress
        values.add(deliveryAddress[0]);
        values.add(Generator.generateHouseNumber());
        values.add(deliveryAddress[1]);
        values.add(deliveryAddress[2]);
        values.add(deliveryAddress[3]);
        //billingAddress
        values.add(billingAddress[0]);
        values.add(Generator.generateHouseNumber());
        values.add(billingAddress[1]);
        values.add(billingAddress[2]);
        values.add(billingAddress[3]);
        //payment details
        values.add(String.format("%s %s", name, surname));
        values.add(Generator.generateRandomCardNumber());
        values.add(parseLocalDate(Generator.generateFutureDate(Generator.getRandomInt(12, 24))));
        values.add(String.valueOf(Generator.getRandomInt(100, 999)));
        return compose(table, ORDER_COLUMNS, values);
    }

    public static String composeReviewStatement(String table, Integer clientId, Integer articleId, String comment, int stars) {
        List<String> values = new ArrayList<>();
        values.add(String.valueOf(articleId));
        values.add(comment.replaceAll("\\Q" + '\'' + "\\E", ""));
        values.add(String.valueOf(stars));
        values.add(String.valueOf(clientId));
        return compose(table, REVIEW_COLUMNS, values);
    }

    public static String composeContainsStatement(String table, Integer sCartId, Integer articleId) {
        List<String> values = new ArrayList<>();
        values.add(String.valueOf(sCartId));
        values.add(String.valueOf(articleId));
        return compose(table, CONTAINS_COLUMNS, values);
    }

    public static String composeShoppingCartStatement(String table) {
        List<String> values = new ArrayList<>();
        values.add("0");
        values.add("0");
        return compose(table, SHOPPING_CART_COLUMNS, values);
    }

    public static String composeDeliveryStatement(String table, String[] address) {
        List<String> values = new ArrayList<>();
        String deliveryService = Generator.getRandomDelivery();
        values.add(deliveryService);
        values.add(Generator.generateCompanyEmail(new String[]{deliveryService}));
        values.add(Generator.generatePhoneNumber());
        values.add(address[0]);
        values.add(Generator.generateHouseNumber());
        values.add(address[1]);
        values.add(address[2]);
        values.add(address[3]);
        return compose(table, SUPPLIER_COLUMNS, values);
    }

    public static String composeArticleStatement(String table, String[] compositeName, String brand, String size, int userId) {
        List<String> values = new ArrayList<>();
        values.add(String.join(" ", Arrays.copyOfRange(compositeName, 0, 3)));
        values.add(new DecimalFormat("#.##").format(Generator.getRandomDouble(0.1, 2000.0)));
        values.add(size);
        values.add(Generator.getRandomColour());
        values.add(brand);
        values.add(String.format("%s %s %s %s %s",values.get(0),
                                                    values.get(1),
                                                    values.get(2),
                                                    values.get(3),
                                                    values.get(4)));
        values.add(String.valueOf(Generator.getRandomInt(0, 1500)));
        values.add(new DecimalFormat("#.##").format(Generator.getRandomDouble(5.0, 75.0)));
        values.add(String.valueOf(userId));
        values.add(compositeName[3]);
        return compose(table, ITEM_COLUMNS, values);
    }

    public static String composeCategoryStatement(String table, Integer id, String name, Integer overId){
        List<String> values = new ArrayList<>();
        values.add(id.toString());
        values.add(name);
        values.add(overId == 0 ? "NULL" : overId.toString());
        return compose(table, CATEGORY_COLUMNS, values);
    }

    public static String composeVendorStatement(String table, String[] compositeName, String[] address){
        List<String> values = new ArrayList<>();
        values.add(Generator.generateUsername(compositeName));
        values.add(Generator.generateCompanyEmail(compositeName));
        values.add(Generator.generatePassword());
        values.add(String.join(" ", compositeName));
        values.add(Generator.generatePhoneNumber());
        values.add(address[0]);
        values.add(Generator.generateHouseNumber());
        values.add(address[1]);
        values.add(address[2]);
        values.add(address[3]);
        return compose(table, VENDOR_COLUMNS, values);
    }

    public static String composeClientStatement(String table,String name, String surname){
        List<String> values = new ArrayList<>();
        String[] fullname = new String[]{name, surname};
        values.add(Generator.generateUsername(fullname));
        values.add(Generator.generateClientEmail(fullname));
        values.add(Generator.generatePassword());
        values.add(name);
        values.add(surname);
        values.add(Generator.generatePhoneNumber());
        return compose(table, CLIENT_COLUMNS, values);
    }

    private static String compose(String table, String columns, List<String> values) {
        return String.format("insert into %s (%s) values (%s)", table, columns, valuesToStatement(values));
    }
    private static String valuesToStatement(List<String> values){
        return values.stream().
                map(s ->s.equals("NULL") || s.startsWith("to_date(") ? s : String.format("'%s'",s))
                .collect(Collectors.joining(", "));
    }

    public static String parseLocalDate(LocalDate date) {
        return date.format(DateTimeFormatter.ofPattern("yyyy-MM-dd"));
    }
}
