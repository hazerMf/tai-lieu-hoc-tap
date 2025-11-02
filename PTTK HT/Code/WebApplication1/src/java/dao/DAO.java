package dao;

import java.sql.Connection;
import java.sql.DriverManager;

public class DAO {
	public static Connection connection;
	
	public DAO() {
                if (connection == null) {
                        String url = "jdbc:sqlserver://DESKTOP-IVQFNOC\\CLCCSDLPTNHOM4;"
                                   + "databaseName=supermarket;"
                                   + "encrypt=true;trustServerCertificate=true;";
                        try {
                                Class.forName("com.microsoft.sqlserver.jdbc.SQLServerDriver");
                                System.out.println("[DAO] SQLServerDriver loaded manually.");
                                connection = DriverManager.getConnection(url, "sa", "123456");
                        } catch (Exception e) {
                                e.printStackTrace();
                        }
                }
        }
}