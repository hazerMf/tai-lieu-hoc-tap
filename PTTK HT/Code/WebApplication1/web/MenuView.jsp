<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Main Menu</title>
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
            margin-top: 84px;
        }
        .toggle-container {
            text-align: center;
            margin: 40px 0 20px;
        }
        .toggle-btn {
            background: #e5e7eb;
            border: none;
            padding: 10px 20px;
            margin: 0 5px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 6px;
            transition: background 0.3s;
        }
        .toggle-btn.active {
            background: #2563eb;
            color: white;
        }
        .toggle-btn:hover {
            background: #d1d5db;
        }
        .toggle-btn.active:hover {
            background: #1e40af;
        }
        .menu {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
            display: none;
        }
        .menu.active {
            display: block;
        }
        .menu h2 {
            margin-bottom: 20px;
        }
        .menu a {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            font-size: 16px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s;
        }
        .menu a:hover {
            background-color: #1e40af;
        }
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }
        .login-form input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        .login-form button {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        .login-form button:hover {
            background-color: #1e40af;
        }
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 30px;
            border-radius: 10px;
            width: 400px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .modal-content h2 {
            margin-top: 0;
            color: #dc2626;
        }
        .modal-content p {
            margin: 20px 0;
            font-size: 16px;
            color: #555;
        }
        .modal-btn {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        .modal-btn:hover {
            background-color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="banner">
        Supermarket Management
    </div>
    
    <div class="content">
        <div style="width: 100%; max-width: 600px;">
            <div class="toggle-container">
                <button class="toggle-btn active" onclick="showMenu('customer')">Customer Menu</button>
                <button class="toggle-btn" onclick="showMenu('staff')">Staff Menu</button>
            </div>
            
            <div id="customerMenu" class="menu active">
                <h2>Customer Menu</h2>
                <a href="view/Customer/RegisterView.jsp">Register</a>
            </div>
            
            <div id="staffMenu" class="menu">
                <h2>Staff Login</h2>
                <form class="login-form" action="<%= request.getContextPath() %>/StaffController" method="post">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Login</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Login Error Modal -->
    <div id="loginErrorModal" class="modal">
        <div class="modal-content">
            <h2>Login Failed</h2>
            <p><%= request.getAttribute("loginMessage") != null ? request.getAttribute("loginMessage") : "" %></p>
            <button class="modal-btn" onclick="closeLoginError()">OK</button>
        </div>
    </div>

    <script>
        function showMenu(type) {
            const customerMenu = document.getElementById('customerMenu');
            const staffMenu = document.getElementById('staffMenu');
            const buttons = document.querySelectorAll('.toggle-btn');
            
            buttons.forEach(btn => btn.classList.remove('active'));
            
            if (type === 'customer') {
                customerMenu.classList.add('active');
                staffMenu.classList.remove('active');
                buttons[0].classList.add('active');
            } else {
                staffMenu.classList.add('active');
                customerMenu.classList.remove('active');
                buttons[1].classList.add('active');
            }
        }

        function closeLoginError() {
            document.getElementById('loginErrorModal').style.display = 'none';
        }

        <%
            String loginStatus = (String) request.getAttribute("loginStatus");
            if ("error".equals(loginStatus)) {
        %>
            document.getElementById('loginErrorModal').style.display = 'block';
            showMenu('staff');
        <%
            }
        %>
    </script>
</body>
</html>
