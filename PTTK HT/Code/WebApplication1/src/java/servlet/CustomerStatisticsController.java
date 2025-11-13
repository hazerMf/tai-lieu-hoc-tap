package servlet;

import dao.CustomerStatisticsDAO;
import model.CustomerStatistics;

import jakarta.servlet.*;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;

import java.io.IOException;
import java.io.PrintWriter;
import java.text.SimpleDateFormat;
import java.util.*;

@WebServlet("/CustomerStatisticsController")
public class CustomerStatisticsController extends HttpServlet {

    private final CustomerStatisticsDAO dao = new CustomerStatisticsDAO();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        // Initial page load
        req.getRequestDispatcher("/view/Staff/CustomerStatisticsView.jsp").forward(req, resp);
    }

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        req.setCharacterEncoding("UTF-8");
        resp.setCharacterEncoding("UTF-8");
        
        // Check if this is an AJAX request
        String xrw = req.getHeader("X-Requested-With");
        boolean isAjax = xrw != null && "XMLHttpRequest".equalsIgnoreCase(xrw);

        try {
            String start = req.getParameter("fromDate");
            String end = req.getParameter("toDate");

            if (start != null && end != null && !start.isEmpty() && !end.isEmpty()) {
                SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
                Date startDate = sdf.parse(start);
                Date endDate = sdf.parse(end);

                System.out.println("[CustomerStatisticsController] Date range: " + start + " to " + end);
                List<CustomerStatistics> list = dao.getCustomerStatistics(startDate, endDate);
                System.out.println("[CustomerStatisticsController] Found " + list.size() + " records");

                if (isAjax) {
                    // Return JSON for AJAX request
                    resp.setContentType("application/json;charset=UTF-8");
                    try (PrintWriter out = resp.getWriter()) {
                        out.print("[");
                        for (int i = 0; i < list.size(); i++) {
                            CustomerStatistics cs = list.get(i);
                            out.print("{");
                            out.print("\"customerId\":" + cs.getCustomerId() + ",");
                            out.print("\"name\":" + toJson(cs.getName()) + ",");
                            out.print("\"phoneNumber\":" + toJson(cs.getPhoneNumber()) + ",");
                            out.print("\"revenue\":" + cs.getRevenue());
                            out.print("}");
                            if (i < list.size() - 1) out.print(",");
                        }
                        out.print("]");
                    }
                } else {
                    // Fallback: traditional form submit
                    req.setAttribute("stats", list);
                    req.getRequestDispatcher("/view/Staff/CustomerStatisticsView.jsp").forward(req, resp);
                }
            } else {
                if (isAjax) {
                    resp.setContentType("application/json;charset=UTF-8");
                    resp.getWriter().print("[]");
                } else {
                    req.getRequestDispatcher("/view/Staff/CustomerStatisticsView.jsp").forward(req, resp);
                }
            }

        } catch (Exception e) {
            System.out.println("[CustomerStatisticsController] Error: " + e.getMessage());
            e.printStackTrace();
            
            if (isAjax) {
                resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
                resp.setContentType("application/json;charset=UTF-8");
                resp.getWriter().print("{\"error\": \"Server error\"}");
            } else {
                req.setAttribute("error", "Invalid input or database error.");
                req.getRequestDispatcher("/view/Staff/CustomerStatisticsView.jsp").forward(req, resp);
            }
        }
    }

    // JSON string escaper
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
