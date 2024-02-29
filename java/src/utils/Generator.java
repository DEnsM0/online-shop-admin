package utils;


import java.security.SecureRandom;
import java.time.LocalDate;
import java.util.*;
import java.util.concurrent.atomic.AtomicInteger;

public class Generator {

    private static final Random random = new Random();
    private static final String UPPER = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    private static final String LOWER = "abcdefghijklmnopqrstuvwxyz";
    private static final String DIGITS = "0123456789";
    private static final String SPECIAL_CHARS = "!@#$%^&<>/?";
    private static final int PASSWORD_LENGTH = 12;
    private static final String[] CLIENT_DOMAINS = {"gmail.com", "yahoo.com", "outlook.com", "hotmail.com", "aol.com", "icloud.com", "protonmail.com", "mail.com", "yandex.com", "zoho.com"
    };
    private static final String[] COMPANY_DOMAINS = {"service.net","info.com", "company.net", "enterprise.org"};
    private static final String[] SIZES = {"XS", "S", "M", "L", "XL", "XXL"};
    private static final String[] COLOURS = {"Red", "Blue", "Green", "Yellow", "Purple", "Pink", "Orange", "Black", "White", "Grey", "Brown", "Beige", "Dark Blue", "Salmon", "Lavender", "Turquoise", "Maroon", "Teal", "Indigo", "Cyan", "Magenta", "Olive", "Gold", "Silver", "Ruby", "Emerald", "Sapphire", "Coral", "Plum", "Charcoal"};
    private static final String[] CLOTHING_BRANDS = {"Nike", "Adidas", "Gucci", "Louis Vuitton", "Zara", "H&M", "Chanel", "Prada", "Calvin Klein", "Ralph Lauren", "Versace", "Tommy Hilfiger", "Dior", "Balenciaga", "Yves Saint Laurent", "Under Armour", "Puma", "Fendi", "Burberry", "Armani", "Gap", "Forever 21", "Michael Kors", "Hugo Boss", "The North Face", "Vans", "Converse"};
    private static final String[] ELECTRONICS_BRANDS = {"Apple", "Samsung", "Sony", "LG", "Panasonic", "Microsoft", "Dell", "HP", "Lenovo", "Asus", "Acer", "Toshiba", "Canon", "Nikon", "GoPro", "Philips", "Bose", "JBL", "Beats by Dre", "Fitbit", "Garmin", "Intel", "AMD", "Nvidia", "Logitech", "Sharp", "Vizio", "Huawei", "OnePlus", "Xiaomi"};
    private static final String[] CARPARTS_BRANDS = {"Bosch", "Denso", "NGK", "Aisin", "Magnaflow", "Brembo", "Moog", "KYB", "ACDelco", "Hella", "Gates", "Wix", "Mann-Filter", "Akebono", "Centric", "Raybestos", "Walker", "Eibach", "Bilstein", "K&N", "Fel-Pro", "TRW", "ATE", "Power Stop", "Valeo", "Cooper Tire", "GMB", "Dayco", "Mahle", "BorgWarner"};
    private static final String[] DELIVERIES = {"DHL", "UPS", "FedEx", "DPD", "TNT", "GLS", "Hermes", "Royal Mail", "La Poste", "Chronopost"};

    public static String getRandomDelivery() {
        return DELIVERIES[random.nextInt(DELIVERIES.length)];
    }

    public static int getRandomInt(int min, int max) {
        return random.nextInt((max - min) + 1) + min;
    }

    public static double getRandomDouble(double min, double max) {
        return min + (max - min) * random.nextDouble();
    }

    public static String getRandomSize() {
        return SIZES[random.nextInt(SIZES.length)];
    }
    public static String getRandomColour() {
        return COLOURS[random.nextInt(COLOURS.length)];
    }
    public static String getRandomClothingBrand() {
        return CLOTHING_BRANDS[random.nextInt(CLOTHING_BRANDS.length)];
    }
    public static String getRandomElectronicsBrand() {
        return ELECTRONICS_BRANDS[random.nextInt(ELECTRONICS_BRANDS.length)];
    }
    public static String getRandomCarPartsBrand() {
        return CARPARTS_BRANDS[random.nextInt(CARPARTS_BRANDS.length)];
    }

    public static String generateRandomCardNumber() {
        StringBuilder cardNumberBuilder = new StringBuilder();
        for (int i = 0; i < 16; i++) {
            cardNumberBuilder.append(random.nextInt(10));
        }

        return cardNumberBuilder.toString();
    }

    public static LocalDate generateFutureDate(int daysToAdd) {
        LocalDate currentDate = LocalDate.now();
        LocalDate futureDate = currentDate.plusDays(daysToAdd);

        return futureDate;
    }
    public static String generateHouseNumber() {
        int number = random.nextInt(150) + 1;

        if (random.nextDouble() < 0.05) {
            char letter = (char) ('A' + random.nextInt(4)); // A, B, C, D
            return String.valueOf(number) + letter;
        } else {
            return String.valueOf(number);
        }
    }
    public static List<String[]> generateArticleNames(List<String[]> names, int n){
        return generateFromThree(names, n);
    }

    public static List<String[]> generateVendorNames(List<String[]> names, int n){
        return generateFromThree(names, n);
    }

    private static List<String[]> generateFromThree(List<String[]> names, int n) {
        List<String[]> result = new ArrayList<>();

        for (int i = 0; i < n; i++) {
            int index1 = random.nextInt(names.size());
            int index2 = random.nextInt(names.size());
            int index3 = random.nextInt(names.size());

            List<String> randomizedList = new ArrayList<>();
            randomizedList.add(names.get(index1)[0]);
            randomizedList.add(random.nextBoolean() ? names.get(index2)[1] : "");
            randomizedList.add(names.get(index3)[2]);

            if(names.get(index3).length == 4){
                randomizedList.add(names.get(index3)[3]);
            }
            result.add(randomizedList.toArray(new String[randomizedList.size()]));
        }
        return result;
    }

    public static int[] divideRandomly(int n) {
        if (n <= 0) {
            throw new IllegalArgumentException("n <= 0");
        }

        int part1;
        int part2;
        int part3;
        try {
            part1 = random.nextInt(n);
            part2 = random.nextInt(n - part1);
            part3 = n - part1 - part2;
        } catch (ArithmeticException e) {
            throw new ArithmeticException("ArithmeticException: " + e.getMessage());
        }

        return new int[]{part1, part2, part3};
    }

    public static int[] divideIntoArray(int n, int[] j) {
        int sumOfJ = Arrays.stream(j).sum();

        if (sumOfJ == 0) {
            throw new IllegalArgumentException("Sum of elements in array j == 0.");
        }

        int[] result = new int[j.length];

        for (int i = 0; i < j.length; i++) {
            result[i] = (j[i] * n) / sumOfJ;
        }

        return result;
    }

    public static String generatePhoneNumber() {

        String countryCode = "+" + (1 + random.nextInt(999));

        String regionalCode = String.format("%03d", random.nextInt(1000));

        String localNumber = String.format("%04d", random.nextInt(10000));

        return countryCode + " " + regionalCode + "-" + localNumber;
    }
    public static String generateUsername(String[] inputArray) {
        return generateUserToken(inputArray).toUpperCase();
    }

    private static String generateUserToken(String[] inputArray) {
        StringBuilder combinedString = new StringBuilder();

        for (int i = 0; i < inputArray.length - 1; i++) {
            String processedString = inputArray[i].substring(0, Math.min(3, inputArray[i].length()));
            combinedString.append(processedString);
        }

        combinedString.append(inputArray[inputArray.length - 1]);

        Random random = new Random(Arrays.toString(inputArray).hashCode());
        int randomNumber = random.nextInt(10000);

        return combinedString.toString().toLowerCase() + randomNumber;
    }

    public static String generateClientEmail(String[] inputArray){
        return generateEmail(inputArray, getDomain(CLIENT_DOMAINS));
    }
    public static String generateCompanyEmail(String[] inputArray){
        return generateEmail(inputArray, getDomain(COMPANY_DOMAINS));
    }
    private static String generateEmail(String[] inputArray, String domain) {
        return  generateUserToken(inputArray) + "@" + domain;

    }

    private static String getDomain(String[] domains) {
        int randomIndex = random.nextInt(domains.length);
        return domains[randomIndex];
    }

    public static String generatePassword() {
        StringBuilder characters = new StringBuilder(UPPER + LOWER);
        characters.append(DIGITS);
        characters.append(SPECIAL_CHARS);

        SecureRandom random = new SecureRandom();
        StringBuilder password = new StringBuilder(PASSWORD_LENGTH);

        for (int i = 0; i < PASSWORD_LENGTH; i++) {
            int randomIndex = random.nextInt(characters.length());
            password.append(characters.charAt(randomIndex));
        }

        return password.toString();
    }

    public static Map<Pair<Integer, Integer>, String> generateCategories(List<String[]> categories, int id, Integer overId) {
        AtomicInteger finalId = new AtomicInteger(id*overId + 1);
        Map<Pair<Integer, Integer>, String> res = new HashMap<>();
        categories.forEach(c ->{
            res.put(new Pair<>(finalId.get(), overId), c[0]);
            for (int i = 1; i < c.length;i++){
                res.put(new Pair<>(finalId.get()*100+i, finalId.get()), c[i]);
            }
            finalId.incrementAndGet();
        });
        return  res;
    }
}
