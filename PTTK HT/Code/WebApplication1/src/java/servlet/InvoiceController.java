package servlet;

import dao.InvoiceDAO;
import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import model.Invoice;

@WebServlet("/CustomerInvoiceController")
public class InvoiceController extends HttpServlet {
    private final InvoiceDAO dao = new InvoiceDAO();
    
    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) 
            throws ServletException, IOException {
        try {
            String custParam = req.getParameter("customerId");
            if (custParam == null || custParam.isEmpty()) {
                req.setAttribute("error", "Missing customerId");
                req.getRequestDispatcher("/view/Staff/CustomerStatisticsView.jsp").forward(req, resp);
                return;
            }

            int customerId = Integer.parseInt(custParam);
            String fromDate = req.getParameter("fromDate");
            String toDate = req.getParameter("toDate");

            List<Invoice> invoices = new ArrayList<>();
            if (fromDate != null && toDate != null && !fromDate.isEmpty() && !toDate.isEmpty()) {
                SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
                Date startDate = sdf.parse(fromDate);
                Date endDate = sdf.parse(toDate);
                invoices = dao.getCustomerInvoices(customerId, startDate, endDate);
                req.setAttribute("fromDate", fromDate);
                req.setAttribute("toDate", toDate);
            } else {
                invoices = new ArrayList<>();
            }

            // Use customerName passed from the link (do not rely on Invoice model)
            String customerName = req.getParameter("customerName");
            req.setAttribute("customerName", customerName != null ? customerName : "");

            req.setAttribute("invoices", invoices);
            req.setAttribute("customerId", customerId);
            req.getRequestDispatcher("/view/Staff/CustomerInvoiceView.jsp").forward(req, resp);

        } catch (Exception e) {
            System.out.println("[InvoiceController] Error: " + e.getMessage());
            e.printStackTrace();
            req.setAttribute("error", "Unable to load invoices.");
            req.getRequestDispatcher("/view/Staff/CustomerStatisticsView.jsp").forward(req, resp);
        }
    }
}
