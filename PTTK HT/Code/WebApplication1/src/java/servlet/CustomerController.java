package servlet;

import dao.CustomerDAO;
import java.io.IOException;
import jakarta.servlet.*;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;
import model.Customer;

@WebServlet("/CustomerController")
public class CustomerController extends HttpServlet {

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        request.setCharacterEncoding("UTF-8");
        response.setContentType("text/html;charset=UTF-8");

        String name = request.getParameter("name");
        String phone = request.getParameter("phone");
        String address = request.getParameter("address");
        String email = request.getParameter("email");
        String password = request.getParameter("password");
        String confirm = request.getParameter("confirm");

        System.out.println("[CustomerController] Data received: " + name + ", " + phone);

        if (!password.equals(confirm)) {
            System.out.println("[CustomerController] Password mismatch");
            request.setAttribute("status", "error");
            request.setAttribute("message", "Passwords do not match");
            request.getRequestDispatcher("/view/Customer/RegisterView.jsp").forward(request, response);
            return;
        }

        Customer customer = new Customer();
        customer.setName(name);
        customer.setPhoneNumber(phone);
        customer.setAddress(address);
        customer.setEmail(email);
        customer.setPassword(password);

        CustomerDAO dao = new CustomerDAO();
        boolean success = dao.register(customer);

        if (success) {
            request.setAttribute("status", "success");
            request.setAttribute("message", "Registration successful!");
            request.getRequestDispatcher("/view/Customer/RegisterView.jsp").forward(request, response);
        } else {
            request.setAttribute("status", "error");
            request.setAttribute("message", "Registration failed. Please try again.");
            request.getRequestDispatcher("/view/Customer/RegisterView.jsp").forward(request, response);
        }
    }
}
