import java.sql.*;
import java.util.*;

public class MySQLLoader {

    private static final String URL = "jdbc:mysql://localhost:3307/crm_sales_warehouse";
    private static final String USER = "root";
    private static final String PASSWORD = "";

    public static Connection connect() throws Exception {
        return DriverManager.getConnection(URL, USER, PASSWORD);
    }

    public static void insertProducts(List<String[]> data) {

        try (Connection conn = connect()) {

            String sql = "INSERT INTO products (product, series, sales_price) VALUES (?, ?, ?)";
            PreparedStatement stmt = conn.prepareStatement(sql);

            for (String[] row : data) {

                stmt.setString(1, row[0]);
                stmt.setString(2, row[1]);

                if (row[2].isEmpty()) {
                    stmt.setNull(3, Types.DOUBLE);
                } else {
                    stmt.setDouble(3, Double.parseDouble(row[2]));
                }

                stmt.executeUpdate();
            }

            System.out.println("Products loaded successfully.");

        } catch (Exception e) {
            System.out.println("Error inserting products: " + e.getMessage());
        }
    }
}