/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package dao;

import java.sql.*;
import java.util.*;
import model.ItemOrder;
import model.Item;

/**
 *
 * @author User
 */
public class ItemOrderDAO extends DAO {

    public List<ItemOrder> getItemsByInvoiceId(int invoiceId) {
        List<ItemOrder> items = new ArrayList<>();
        if (connection == null) {
            System.out.println("[ItemOrderDAO] connection null, initializing DAO");
            new DAO();
        }

        String sql = """
            SELECT io.quantity, io.price, p.name AS itemName
            FROM tblInvoice i
            JOIN tblOrder o ON i.tblOrderId = o.id
            JOIN tblItemOrder io ON o.id = io.tblOrderId
            JOIN tblItem p ON io.tblItemId = p.id
            WHERE i.id = ?
        """;

        try (PreparedStatement ps = connection.prepareStatement(sql)) {
            ps.setInt(1, invoiceId);
            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) {
                    ItemOrder it = new ItemOrder();
                    
                    Item item = new Item();
                    item.setName(rs.getString("itemName"));
                    it.setItem(item);
                    
                    it.setQuantity(rs.getInt("quantity"));
                    it.setPrice(rs.getFloat("price"));
                    
                    items.add(it);
                }
            }
        } catch (SQLException e) {
            System.out.println("[ItemOrderDAO] SQL error: " + e.getMessage());
            e.printStackTrace();
        }

        return items;
    }
}
