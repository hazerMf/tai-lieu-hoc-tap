<!-- filepath: c:\Users\User\Desktop\tai-lieu-hoc-tap\PTTK HT\Code\WebApplication1\web\view\Staff\CustomerInvoiceView.jsp -->
<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<%@ page import="java.util.*, model.Invoice" %>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Customer Invoices</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fc;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 40px auto;
            background: #fff;
            padding: 28px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        h2 {
            margin: 0 0 8px 0;
            color: #333;
        }
        .meta {
            color: #555;
            margin-bottom: 18px;
            font-size: 14px;
        }
        .back-btn {
            float: right;
            background-color: #e5e7eb;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            color: #111827;
        }
        .back-btn:hover { background-color: #d1d5db; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }
        th, td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #e6e9ef;
        }
        thead { background-color: #f4f6f8; }
        th { font-weight: 600; color: #333; }
        .empty { text-align: center; color: #666; padding: 18px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="javascript:history.back()" class="back-btn">Back</a>
        <h2>Customer Invoices</h2>
        <div class="meta">
            Customer: <strong><%= request.getAttribute("customerName") != null ? request.getAttribute("customerName") : "" %></strong>
            <% String f = (String) request.getAttribute("fromDate"); String t = (String) request.getAttribute("toDate"); %>
            <% if (f != null && t != null && !f.isEmpty() && !t.isEmpty()) { %>
                &nbsp; | &nbsp; Date range: <strong><%= f %></strong> to <strong><%= t %></strong>
            <% } %>
        </div>
        
        <%
            List<Invoice> invoices = (List<Invoice>) request.getAttribute("invoices");
            if (invoices != null && !invoices.isEmpty()) {
        %>
        <table>
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Date</th>
                    <th>Billing Address</th>
                    <th>Payment Method</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <% 
                    java.text.SimpleDateFormat sdf = new java.text.SimpleDateFormat("yyyy-MM-dd");
                    for (Invoice invoice : invoices) { 
                %>
                <tr>
                    <td><%= invoice.getId() %></td>
                    <td><%= invoice.getPayDate() != null ? sdf.format(invoice.getPayDate()) : "" %></td>
                    <td><%= invoice.getBillingAddress() != null ? invoice.getBillingAddress() : "" %></td>
                    <td><%= invoice.getPaymentMethod() != null ? invoice.getPaymentMethod() : "" %></td>
                    <td><%= String.format("%,.0f", invoice.getGrandTotal()) %></td>
                </tr>
                <% } %>
            </tbody>
        </table>
        <%
            } else {
        %>
            <div class="empty">No invoices found for this customer in the selected date range.</div>
        <%
            }
        %>
    </div>
</body>
</html>