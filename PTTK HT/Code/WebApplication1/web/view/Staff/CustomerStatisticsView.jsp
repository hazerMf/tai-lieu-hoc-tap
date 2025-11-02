<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<%@ page import="java.util.*, model.CustomerStatistics" %>
<!DOCTYPE html>
<html>
<head>
    <title>View Customer Statistics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fc;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 50px auto;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 30px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .filter {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .filter input[type="date"] {
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .filter button {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 9px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .filter button:hover {
            background-color: #0056b3;
        }

        .back-btn {
            float: right;
            background-color: #e5e7eb;
            border: none;
            padding: 7px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }

        .back-btn:hover {
            background-color: #d1d5db;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #f1f3f6;
        }

        th, td {
            padding: 10px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            font-weight: 600;
        }

        td {
            color: #333;
        }

        .empty {
            text-align: center;
            color: #666;
            padding: 15px;
        }

        .view-btn {
            background-color: #4CAF50;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
        }

        .view-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="container">
    <button class="back-btn" onclick="window.location.href='<%= request.getContextPath() %>/MenuView.jsp'">Back</button>
    <h2>View Customer Statistics</h2>

    <form class="filter" action="${pageContext.request.contextPath}/CustomerStatisticsController" method="get">
        <input type="date" name="fromDate" required>
        <input type="date" name="toDate" required>
        <button type="submit">Search</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Phone Number</th>
                <th>Revenue</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <%
            List<CustomerStatistics> list = (List<CustomerStatistics>) request.getAttribute("stats");
            String fromDate = request.getParameter("fromDate");
            String toDate = request.getParameter("toDate");
            if (list != null && !list.isEmpty()) {
                int index = 1;
                for (CustomerStatistics cs : list) {
        %>
                    <tr>
                        <td><%= index++ %></td>
                        <td><%= cs.getName() %></td>
                        <td><%= cs.getPhoneNumber() %></td>
                        <td><%= String.format("%,.0f", cs.getRevenue()) %></td>
                        <td>
<%
    // DO NOT redeclare fromDate/toDate here — reuse the ones above
    String encFrom = fromDate != null ? java.net.URLEncoder.encode(fromDate, "UTF-8") : "";
    String encTo = toDate != null ? java.net.URLEncoder.encode(toDate, "UTF-8") : "";
    String encName = cs.getName() != null ? java.net.URLEncoder.encode(cs.getName(), "UTF-8") : "";
%>
<a class="view-btn"
   href="<%= request.getContextPath() %>/CustomerInvoiceController?customerId=<%= cs.getCustomerId() %><%= (fromDate!=null && toDate!=null) ? "&fromDate=" + encFrom + "&toDate=" + encTo : "" %>&customerName=<%= encName %>">
    View Invoices
</a>
                        </td>
                    </tr>
        <%
                }
            } else if (request.getParameter("fromDate") != null) {
        %>
                <tr><td colspan="5" class="empty">No data found for selected date range.</td></tr>
        <%
            }
        %>
        </tbody>
    </table>
</div>
</body>
</html>
