<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<%@ page import="java.util.*, model.Invoice" %>
<%@ page import="java.net.URLEncoder" %>
<%
// Minimal logic fix: if data not loaded yet, redirect to controller (doGet) with same params
Object invAttr = request.getAttribute("invoices");
String redirected = request.getParameter("redirected");
if (invAttr == null && (redirected == null || redirected.isEmpty())) {
    String custId = request.getParameter("customerId");
    String from = request.getParameter("fromDate");
    String to = request.getParameter("toDate");
    String custName = request.getParameter("customerName");

    StringBuilder url = new StringBuilder();
    url.append(request.getContextPath()).append("/InvoiceController?customerId=")
       .append(URLEncoder.encode(custId == null ? "" : custId, "UTF-8"));
    if (from != null && !from.isEmpty()) url.append("&fromDate=").append(URLEncoder.encode(from, "UTF-8"));
    if (to != null && !to.isEmpty()) url.append("&toDate=").append(URLEncoder.encode(to, "UTF-8"));
    if (custName != null && !custName.isEmpty()) url.append("&customerName=").append(URLEncoder.encode(custName, "UTF-8"));
    url.append("&redirected=1");

    response.sendRedirect(url.toString());
    return;
}
%>
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
            margin-top: 84px; /* space for fixed banner */
        }
        .menu {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
            margin-top: 40px;
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
        thead { 
            background-color: #f4f6f8; 
        }
        th { 
            font-weight: 600; color: #333; 
        }
        .empty { 
            text-align: center; 
            color: #666; 
            padding: 18px; 
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
        .space{
            padding: 30px;
        }
    </style>
</head>
<body>
    <div class="banner">
        Supermarket Management
    </div>
    <div class="space"></div>
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
                    <th></th> <!-- detail button column -->
                </tr>
            </thead>
            <tbody>
                <% 
                    java.text.SimpleDateFormat sdf = new java.text.SimpleDateFormat("yyyy-MM-dd");
                    String customerName = request.getAttribute("customerName") != null ? (String) request.getAttribute("customerName") : "";
                    for (Invoice invoice : invoices) { 
                        String encCust = java.net.URLEncoder.encode(customerName, "UTF-8");
                        String payDateStr = invoice.getPayDate() != null ? sdf.format(invoice.getPayDate()) : "";
                        String encDate = java.net.URLEncoder.encode(payDateStr, "UTF-8");
                        String encAddr = java.net.URLEncoder.encode(invoice.getBillingAddress() != null ? invoice.getBillingAddress() : "", "UTF-8");
                        String encPayMethod = java.net.URLEncoder.encode(invoice.getPaymentMethod() != null ? invoice.getPaymentMethod() : "", "UTF-8");
                %>
                <tr>
                    <td><%= invoice.getId() %></td>
                    <td><%= payDateStr %></td>
                    <td><%= invoice.getBillingAddress() != null ? invoice.getBillingAddress() : "" %></td>
                    <td><%= invoice.getPaymentMethod() != null ? invoice.getPaymentMethod() : "" %></td>
                    <td><%= String.format("%,.0f", invoice.getGrandTotal()) %></td>
                    <td>
                        <a class="view-btn" 
                           href="<%= request.getContextPath() %>/view/Staff/InvoiceView.jsp?invoiceId=<%= invoice.getId() %>&customerName=<%= encCust %>&payDate=<%= encDate %>&grandTotal=<%= invoice.getGrandTotal() %>&discount=<%= invoice.getDiscount() %>&billingAddress=<%= encAddr %>&paymentMethod=<%= encPayMethod %>">
                            View Detail
                        </a>
                    </td>
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