package utils;

import java.io.*;
import java.util.ArrayList;
import java.util.List;
import java.util.Random;

public class Reader {
    private static final String DATA_DIR = "data/";

    public static List<String> readNegativeReviews(int n) {
        return readRandomLines1Column("negative_reviews.csv", n);
    }

    public static List<String> readPositiveReviews(int n) {
        return readRandomLines1Column("positive_reviews.csv", n);
    }

    public static List<String[]> readClothes() {
        return readToLists("clothes.csv", 4);
    }

    public static List<String[]> readCarParts() {
        return readToLists("carparts.csv", 4);
    }

    public static List<String[]> readElectronics() {
        return readToLists("electronics.csv", 4);
    }

    public static List<String[]> readElectronicsCategories() {
        return readToLists("electronics_categories.csv", 4);
    }

    public static List<String[]> readCarPartsCategories() {
        return readToLists("carparts_categories.csv", 4);
    }

    public static List<String[]> readClothesCategories() {
        return readToLists("clothes_categories.csv", 4);
    }

    public static List<String[]> readVendorNames() {
        return readToLists("vendor.csv", 3);
    }

    public static List<String[]> readRandomAddresses(int n) {
        return readRandomLinesNColumns("addresses.csv", n, 4);
    }

    public static List<String> readNames(int n) {
        return readRandomLines1Column("names.csv", n);
    }

    public static List<String> readSurnames(int n) {
        return readRandomLines1Column("surnames.csv", n);
    }

    private static List<String> readRandomLines1Column(String filename, int n) {
        List<String> res = new ArrayList<>();

        try (InputStream inputStream = Reader.class.getClassLoader().getResourceAsStream(DATA_DIR + filename);
             BufferedReader br = new BufferedReader(new InputStreamReader(inputStream))) {
            List<String> lines = new ArrayList<>();
            String line;

            while ((line = br.readLine()) != null) {
                lines.add(line/*.replace("\"","")*/);
            }

            int totalLines = lines.size();
            Random random = new Random();

            for (int i = 0; i < n; i++) {
                int randomLineIndex = random.nextInt(totalLines);
                String randomLine = lines.get(randomLineIndex);

                String[] values = randomLine.split(",");

                String val = values[0].trim();

                res.add(val);
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
        return res;
    }

    private static List<String[]> readRandomLinesNColumns(String filename, int n, int numOfCols) {
        List<String[]> res = new ArrayList<>();

        try (InputStream inputStream = Reader.class.getClassLoader().getResourceAsStream(DATA_DIR + filename);
             BufferedReader br = new BufferedReader(new InputStreamReader(inputStream))) {
            List<String> lines = new ArrayList<>();
            String line;

            while ((line = br.readLine()) != null) {
                lines.add(line/*.replace("\"","")*/);
            }

            int totalLines = lines.size();
            Random random = new Random();

            for (int i = 0; i < n; i++) {
                int randomLineIndex = random.nextInt(totalLines);
                String randomLine = lines.get(randomLineIndex);

                String[] values = randomLine.split(",");
                if (values.length == numOfCols) {
                    res.add(values);
                } else {
                    System.out.printf("Wrong number of values in a row: %s. Should be %d.%n", randomLine, numOfCols);
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
        return res;
    }

    private static List<String[]> readToLists(String filename, int numOfCols) {
        List<String[]> res = new ArrayList<>();
        try (InputStream inputStream = Reader.class.getClassLoader().getResourceAsStream(DATA_DIR + filename);
             BufferedReader br = new BufferedReader(new InputStreamReader(inputStream))) {
            String line;

            while ((line = br.readLine()) != null) {
                String[] values = line.split(",");
                if (values.length == numOfCols) {
                    String[] row = new String[numOfCols];
                    for (int i = 0; i < numOfCols; i++) {
                        row[i] = values[i].trim();
                    }
                    res.add(row);
                } else {
                    System.out.printf("Wrong number of values in a row: %s. Should be %d.%n", line, numOfCols);
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
        return res;
    }
}