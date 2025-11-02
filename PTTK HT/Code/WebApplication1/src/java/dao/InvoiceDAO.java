package dao;

import java.sql.*;
import java.util.*;
import model.Invoice;
import java.util.Date;

public class InvoiceDAO extends DAO {
    
    public List<Invoice> getCustomerInvoices(int customerId, Date startDate, Date endDate) {
        List<Invoice> invoices = new ArrayList<>();
        
        if (connection == null) {
            System.out.println("[InvoiceDAO] connection null, initializing DAO");
            new DAO();
        }
        
        String sql = """
            SELECT i.id, i.payDate, SUM(o.quantity * o.price) as total,
                   i.billingAddress, i.paymentMethod
            FROM tblInvoice i
            JOIN tblOrder r ON r.id = i.tblOrderId
            JOIN tblItemOrder o ON r.id = o.tblOrderId
            JOIN tblCustomer c ON r.tblCustomerCustomerId = c.customerId
            WHERE c.customerId = ? AND i.payDate BETWEEN ? AND ?
            GROUP BY i.id, i.payDate, i.billingAddress, i.paymentMethod
            ORDER BY total DESC
        """;
        
        try (PreparedStatement ps = connection.prepareStatement(sql)) {
            ps.setInt(1, customerId);
            ps.setDate(2, new java.sql.Date(startDate.getTime()));
            ps.setDate(3, new java.sql.Date(endDate.getTime()));
            
            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) {
                    Invoice invoice = new Invoice();
                    invoice.setId(rs.getInt("id"));
                    invoice.setPayDate(rs.getDate("payDate"));
                    invoice.setGrandTotal(rs.getFloat("total"));
                    invoice.setBillingAddress(rs.getString("billingAddress"));
                    invoice.setPaymentMethod(rs.getString("paymentMethod"));
                    invoices.add(invoice);
                }
            }
        } catch (SQLException e) {
            System.out.println("[InvoiceDAO] Error: " + e.getMessage());
            e.printStackTrace();
        }
        
        return invoices;
    }
}