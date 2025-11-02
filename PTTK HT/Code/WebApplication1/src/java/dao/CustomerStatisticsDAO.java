package dao;

import java.sql.*;
import java.util.*;
import java.util.Date;

import model.CustomerStatistics;

public class CustomerStatisticsDAO extends DAO {

    public List<CustomerStatistics> getCustomerStatistics(Date startDate, Date endDate) {
        List<CustomerStatistics> stats = new ArrayList<>();

        if (connection == null) {
            System.out.println("[CustomerStatisticsDAO] Connection is null! Reinitializing DAO...");
            new DAO();
        }

        String sql = """
            SELECT c.customerId, u.name, u.phoneNumber, SUM(o.quantity * o.price) AS totalRevenue
            FROM tblInvoice AS i
            JOIN tblOrder AS r ON r.id = i.tblOrderId
            JOIN tblItemOrder AS o ON r.id = o.tblOrderId
            JOIN tblCustomer AS c ON r.tblCustomerCustomerId = c.customerId
            JOIN tblUser AS u ON c.customerId = u.id
            WHERE i.payDate BETWEEN ? AND ?
            GROUP BY c.customerId, u.name, u.phoneNumber
            ORDER BY totalRevenue DESC
        """;

        System.out.println("[CustomerStatisticsDAO] Executing query for dates: " + startDate + " to " + endDate);
        try (PreparedStatement ps = connection.prepareStatement(sql)) {
            ps.setDate(1, new java.sql.Date(startDate.getTime()));
            ps.setDate(2, new java.sql.Date(endDate.getTime()));
            
            ResultSet rs = ps.executeQuery();
            while (rs.next()) {
                CustomerStatistics cs = new CustomerStatistics();
                cs.setCustomerId(rs.getInt("customerId"));
                cs.setName(rs.getString("name"));
                cs.setPhoneNumber(rs.getString("phoneNumber"));
                cs.setRevenue(rs.getFloat("totalRevenue"));
                stats.add(cs);
                System.out.println("[CustomerStatisticsDAO] Added customer: " + cs.getName() + " with revenue: " + cs.getRevenue());
            }

        } catch (SQLException e) {
            System.out.println("[CustomerStatisticsDAO] SQL failed: " + e.getMessage());
            e.printStackTrace();
        }

        return stats;
    }
}
