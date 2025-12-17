package servlet;

import dao.StaffDAO;
import model.User;
import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;
import java.io.IOException;

@WebServlet("/StaffController")
public class StaffController extends HttpServlet {

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        
        request.setCharacterEncoding("UTF-8");
        response.setCharacterEncoding("UTF-8");

        String username = request.getParameter("username");
        String password = request.getParameter("password");

        System.out.println("[StaffController] Login attempt: " + username);

        User user = new User();
        user.setName(username);
        user.setPassword(password);

        StaffDAO dao = new StaffDAO();
        boolean success = dao.login(user);

        if (success) {
            System.out.println("[StaffController] Login success");
            HttpSession session = request.getSession();
            session.setAttribute("staffUser", username);
            response.sendRedirect(request.getContextPath() + "/view/Staff/StaffMenuView.jsp");
        } else {
            System.out.println("[StaffController] Login failed");
            request.setAttribute("loginStatus", "error");
            request.setAttribute("loginMessage", "Invalid username or password");
            request.getRequestDispatcher("/MenuView.jsp").forward(request, response);
        }
    }
}