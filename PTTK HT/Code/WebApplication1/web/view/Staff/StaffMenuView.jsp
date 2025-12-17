<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<%
    String staffUser = (String) session.getAttribute("staffUser");
    if (staffUser == null) {
        response.sendRedirect(request.getContextPath() + "/MenuView.jsp");
        return;
    }
    
    String action = request.getParameter("action");
    if ("logout".equals(action)) {
        session.invalidate();
        response.sendRedirect(request.getContextPath() + "/MenuView.jsp");
        return;
    }
%>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Menu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        .banner {
            background-color: #1f2937;
            color: white;
            padding: 20px;
            padding-left: 40px;
            text-align: left;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .menu {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
            width: 400px;
        }
        .menu h2 {
            margin-bottom: 30px;
            color: #333;
        }
        .menu-btn {
            display: block;
            width: 100%;
            padding: 15px;
            margin: 15px 0;
            font-size: 16px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
            box-sizing: border-box;
        }
        .menu-btn:hover {
            background-color: #1e40af;
        }
        .logout-btn {
            background-color: #dc2626;
        }
        .logout-btn:hover {
            background-color: #b91c1c;
        }
        .user-info {
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="banner">
        Supermarket Management
    </div>
    
    <div class="content">
        <div class="menu">
            <h2>Staff Menu</h2>
            <div class="user-info">Logged in as: <strong><%= staffUser %></strong></div>
            
            <a href="<%= request.getContextPath() %>/view/Staff/CustomerStatisticsView.jsp" class="menu-btn">
                View Customer Statistics
            </a>
            
            <a href="<%= request.getContextPath() %>/view/Staff/StaffMenuView.jsp?action=logout" class="menu-btn logout-btn">
                Logout
            </a>
        </div>
    </div>
</body>
</html>