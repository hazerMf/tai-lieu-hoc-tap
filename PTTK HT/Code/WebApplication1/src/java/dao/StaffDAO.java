package dao;

import model.User;
import java.sql.*;

public class StaffDAO extends DAO {

    public boolean login(User user) {
        if (connection == null) {
            System.out.println("[StaffDAO] Connection is null! Reinitializing DAO...");
            new DAO();
        }

        String sql = "SELECT * FROM tblUser WHERE name = ? AND password = ? AND role = 'staff'";

        try (PreparedStatement ps = connection.prepareStatement(sql)) {
            ps.setString(1, user.getName());
            ps.setString(2, user.getPassword());
            
            ResultSet rs = ps.executeQuery();
            if (rs.next()) {
                System.out.println("[StaffDAO] Login successful for: " + user.getName());
                return true;
            } else {
                System.out.println("[StaffDAO] Login failed for: " + user.getName());
                return false;
            }
        } catch (SQLException e) {
            System.out.println("[StaffDAO] SQL error: " + e.getMessage());
            e.printStackTrace();
            return false;
        }
    }
}