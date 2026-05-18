import java.util.*;

public class ETL_Main {

    public static void main(String[] args) {

        System.out.println("Starting ETL Process...");

        // CSV Parsing
        List<String[]> products = CSVParser.parse("data/products.csv");

        // Load into MySQL
        MySQLLoader.insertProducts(products);

        System.out.println("ETL Process Completed.");
    }
}