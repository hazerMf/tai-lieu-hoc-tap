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
                <h2>Staff Menu</h2>
                <a href="view/Staff/CustomerStatisticsView.jsp">View Statistics</a>
            </div>
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
    </script>
</body>
</html>
