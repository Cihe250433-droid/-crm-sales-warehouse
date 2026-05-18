import java.io.*;
import java.util.*;

public class CSVParser {

    public static List<String[]> parse(String filePath) {
        List<String[]> data = new ArrayList<>();

        try (BufferedReader br = new BufferedReader(new FileReader(filePath))) {
            String line;
            boolean isHeader = true;

            while ((line = br.readLine()) != null) {

                if (isHeader) {
                    isHeader = false;
                    continue; // skip header
                }

                String[] row = line.split(",", -1); // keep empty values
                data.add(row);
            }

        } catch (Exception e) {
            System.out.println("Error reading CSV: " + e.getMessage());
        }

        return data;
    }
}