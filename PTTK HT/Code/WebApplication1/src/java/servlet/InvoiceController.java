package servlet;

import dao.InvoiceDAO;
import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;
import model.Invoice;

import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;

@WebServlet("/InvoiceController")
public class InvoiceController extends HttpServlet {

    private final InvoiceDAO dao = new InvoiceDAO();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        req.setCharacterEncoding("UTF-8");
        resp.setCharacterEncoding("UTF-8");

        try {
            String customerIdParam = req.getParameter("customerId");
            String fromDateParam = req.getParameter("fromDate");
            String toDateParam = req.getParameter("toDate");
            String customerName = req.getParameter("customerName");

            if (customerIdParam != null && !customerIdParam.isEmpty()) {
                int customerId = Integer.parseInt(customerIdParam);
                List<Invoice> invoices;

                if (fromDateParam != null && toDateParam != null && !fromDateParam.isEmpty() && !toDateParam.isEmpty()) {
                    SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
                    Date startDate = sdf.parse(fromDateParam);
                    Date endDate = sdf.parse(toDateParam);
                    invoices = dao.getCustomerInvoices(customerId, startDate, endDate);

                    req.setAttribute("fromDate", fromDateParam);
                    req.setAttribute("toDate", toDateParam);
                } else {
                    invoices = java.util.Collections.emptyList();
                }

                req.setAttribute("invoices", invoices);
                // pass through the customer name for display
                if (customerName != null) {
                    req.setAttribute("customerName", java.net.URLDecoder.decode(customerName, "UTF-8"));
                }
            }

            req.getRequestDispatcher("/view/Staff/CustomerInvoiceView.jsp").forward(req, resp);

        } catch (Exception e) {
            e.printStackTrace();
            req.setAttribute("invoices", java.util.Collections.emptyList());
            req.setAttribute("error", "Error loading invoices");
            req.getRequestDispatcher("/view/Staff/CustomerInvoiceView.jsp").forward(req, resp);
        }
    }
}