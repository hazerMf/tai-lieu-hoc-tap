package dao;

import model.Customer;
import java.sql.*;

public class CustomerDAO extends DAO {

    public boolean register(Customer customer) {
        if (connection == null) {
            System.out.println("[CustomerDAO] Connection is null! Reinitializing DAO...");
            new DAO();
        }

        String insertUser = "INSERT INTO tblUser (name, password, email, phoneNumber, address, role) VALUES (?, ?, ?, ?, ?, ?)";
        String insertCustomer = "INSERT INTO tblCustomer (tblUserId) VALUES (?)";

        try {
            connection.setAutoCommit(false);

            try (PreparedStatement psUser = connection.prepareStatement(insertUser, Statement.RETURN_GENERATED_KEYS)) {
                psUser.setString(1, customer.getName());
                psUser.setString(2, customer.getPassword());
                psUser.setString(3, customer.getEmail());
                psUser.setString(4, customer.getPhoneNumber());
                psUser.setString(5, customer.getAddress());
                psUser.setString(6, "customer");

                int userRows = psUser.executeUpdate();
                if (userRows == 0) {
                    connection.rollback();
                    System.out.println("[CustomerDAO] Failed to insert user.");
                    return false;
                }

                ResultSet rs = psUser.getGeneratedKeys();
                int userId = -1;
                if (rs.next()) {
                    userId = rs.getInt(1);
                    System.out.println("[CustomerDAO] userId: " + userId);
                } else {
                    connection.rollback();
                    System.out.println("[CustomerDAO] No userId generated.");
                    return false;
                }

                try (PreparedStatement psCustomer = connection.prepareStatement(insertCustomer)) {
                    psCustomer.setInt(1, userId);
                    psCustomer.executeUpdate();
                }

                connection.commit();
                System.out.println("[CustomerDAO] Register success — userId: " + userId);
                return true;
            } catch (SQLException e) {
                connection.rollback();
                System.out.println("[CustomerDAO] SQL failed: " + e.getMessage());
                return false;
            } finally {
                connection.setAutoCommit(true);
            }

        } catch (SQLException e) {
            System.out.println("[CustomerDAO] Transaction failed: " + e.getMessage());
            return false;
        }
    }
}

