import java.util.*;

public class DataTransformer {

    public static String cleanText(String value) {
        if (value == null) return null;
        return value.trim();
    }

    public static Double cleanNumber(String value) {
        try {
            if (value == null || value.trim().isEmpty()) return null;
            value = value.replace(",", "").replace("$", "");
            return Double.parseDouble(value);
        } catch (Exception e) {
            return null;
        }
    }

    public static String standardiseSector(String sector) {
        if (sector == null) return null;

        sector = sector.trim().toLowerCase();

        switch (sector) {
            case "technolgy":
            case "technology":
                return "Technology";
            case "software":
                return "Software";
            case "retail":
                return "Retail";
            case "finance":
                return "Finance";
            case "medical":
                return "Medical";
            default:
                return sector.substring(0, 1).toUpperCase() + sector.substring(1);
        }
    }
}