package servlet;

import dao.CustomerStatisticsDAO;
import model.CustomerStatistics;

import jakarta.servlet.*;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;

import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.*;

@WebServlet("/CustomerStatisticsController")
public class CustomerStatisticsController extends HttpServlet {

    private final CustomerStatisticsDAO dao = new CustomerStatisticsDAO();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        try {
            String start = req.getParameter("fromDate"); // Changed from startDate
            String end = req.getParameter("toDate");     // Changed from endDate

            if (start != null && end != null && !start.isEmpty() && !end.isEmpty()) {
                SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
                Date startDate = sdf.parse(start);
                Date endDate = sdf.parse(end);

                System.out.println("[CustomerStatisticsController] Date range: " + start + " to " + end);
                List<CustomerStatistics> list = dao.getCustomerStatistics(startDate, endDate);
                req.setAttribute("stats", list);
                System.out.println("[CustomerStatisticsController] Found " + list.size() + " records");
            }

            req.getRequestDispatcher("/view/Staff/CustomerStatisticsView.jsp").forward(req, resp);

        } catch (Exception e) {
            System.out.println("[CustomerStatisticsController] Error: " + e.getMessage());
            e.printStackTrace();
            req.setAttribute("error", "Invalid input or database error.");
            req.getRequestDispatcher("/view/Staff/CustomerStatisticsView.jsp").forward(req, resp);
        }
    }

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        doGet(req, resp); // reuse logic
    }
}
