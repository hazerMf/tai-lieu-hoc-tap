/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package servlet;

import dao.ItemOrderDAO;
import model.ItemOrder;
import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.List;

@WebServlet("/ItemOrderController")
public class ItemOrderController extends HttpServlet {
    private final ItemOrderDAO dao = new ItemOrderDAO();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        req.setCharacterEncoding("UTF-8");
        resp.setCharacterEncoding("UTF-8");

        String invoiceIdParam = req.getParameter("invoiceId");
        if (invoiceIdParam == null || invoiceIdParam.isEmpty()) {
            resp.sendError(HttpServletResponse.SC_BAD_REQUEST, "Missing invoiceId");
            return;
        }

        try {
            int invoiceId = Integer.parseInt(invoiceIdParam);
            List<ItemOrder> items = dao.getItemsByInvoiceId(invoiceId);

            resp.setContentType("application/json;charset=UTF-8");
            try (PrintWriter out = resp.getWriter()) {
                out.print("[");
                for (int i = 0; i < items.size(); i++) {
                    ItemOrder it = items.get(i);
                    out.print("{");
                    String nameJson = (it.getItem() != null) ? toJson(it.getItem().getName()) : "null";
                    out.print("\"name\":" + nameJson + ",");
                    out.print("\"quantity\":" + it.getQuantity() + ",");
                    out.print("\"price\":" + it.getPrice() + ",");
                    out.print("\"total\":" + it.getPrice()*it.getQuantity());
                    out.print("}");
                    if (i < items.size() - 1) out.print(",");
                }
                out.print("]");
            }
        } catch (Exception e) {
            e.printStackTrace();
            resp.sendError(HttpServletResponse.SC_INTERNAL_SERVER_ERROR, "Server error");
        }
    }

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        // Allow POST to behave like GET
        doGet(req, resp);
    }

    // safe JSON string escaper
    private String toJson(String s) {
        if (s == null) return "null";
        StringBuilder sb = new StringBuilder("\"");
        for (int i = 0; i < s.length(); i++) {
            char c = s.charAt(i);
            switch (c) {
                case '\\': sb.append("\\\\"); break;
                case '"': sb.append("\\\""); break;
                case '\b': sb.append("\\b"); break;
                case '\f': sb.append("\\f"); break;
                case '\n': sb.append("\\n"); break;
                case '\r': sb.append("\\r"); break;
                case '\t': sb.append("\\t"); break;
                default:
                    if (c < 0x20 || c > 0x7E) {
                        sb.append(String.format("\\u%04x", (int) c));
                    } else {
                        sb.append(c);
                    }
            }
        }
        sb.append("\"");
        return sb.toString();
    }
}
