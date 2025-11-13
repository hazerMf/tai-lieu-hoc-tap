<%@page contentType="text/html;charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.net.URLDecoder" %>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Detail</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background:#f8f9fc; 
            margin:0; 
            padding:20px; 
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
        .container { 
            max-width:980px; 
            margin:0 auto; 
            background:#fff; 
            padding:22px; 
            border-radius:8px; 
            box-shadow:0 2px 8px rgba(0,0,0,0.08); 
        }
        .back { 
            float: right;
            display:inline-block; 
            margin-bottom:12px; 
            background:#e5e7eb; 
            padding:8px 12px; 
            border-radius:6px; 
            text-decoration:none; 
            color:#111; 
        }
        h2 { 
            margin:0 0 8px 0; 
            color:#222; 
        }
        .meta { 
            color:#444; 
            margin-bottom:14px; 
            font-size:14px; 
        }
        .meta b { 
            color:#111; 
        }
        table { 
            width:100%; 
            border-collapse:collapse; 
            margin-top:12px; 
        }
        th, td { 
            padding:10px 12px;
            border-bottom:1px solid #e6e9ef; 
            text-align:left; 
        }
        thead { 
            background:#f4f6f8; 
            font-weight:600; 
        }
        .summary {
             margin-top:14px; 
             text-align:right; 
             font-size:15px; 
             color:#111; 
        }
        .muted { 
            color:#6b7280; 
            font-style:italic; 
            font-size:13px; 
        }
        .empty { 
            text-align:center; 
            padding:12px; 
            color:#666; 
        }
        .space{
            padding: 30px;
        }
    </style>
</head>
<body>
    <div class="space"></div>
<div class="banner">
    Supermarket Management
</div>
<div class="container">
    <a class="back" href="javascript:history.back()">Back</a>
    <h2>Invoice Detail</h2>

    <%
        String invoiceId = request.getParameter("invoiceId");
        String orderId = request.getParameter("orderId");
        String customerName = request.getParameter("customerName") != null ? URLDecoder.decode(request.getParameter("customerName"), "UTF-8") : "";
        String payDate = request.getParameter("payDate") != null ? URLDecoder.decode(request.getParameter("payDate"), "UTF-8") : "";
        String billingAddress = request.getParameter("billingAddress") != null ? URLDecoder.decode(request.getParameter("billingAddress"), "UTF-8") : "";
        String paymentMethod = request.getParameter("paymentMethod") != null ? URLDecoder.decode(request.getParameter("paymentMethod"), "UTF-8") : "";
        String discountParam = request.getParameter("discount") != null ? request.getParameter("discount") : "0";
        String grandTotalParam = request.getParameter("grandTotal") != null ? request.getParameter("grandTotal") : "";
    %>

    <div class="meta">
        Customer: <b id="customerName"><%= customerName %></b>
        &nbsp; | &nbsp; Payment date: <b id="payDate"><%= payDate %></b>
        <% if (billingAddress != null && !billingAddress.isEmpty()) { %>
            <br>Billing address: <span class="muted"><%= billingAddress %></span>
        <% } %>
        <% if (paymentMethod != null && !paymentMethod.isEmpty()) { %>
            &nbsp; | &nbsp; Payment method: <span class="muted"><%= paymentMethod %></span>
        <% } %>
    </div>

    <table id="itemsTable" aria-live="polite">
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th style="width:80px;">Qty</th>
                <th style="width:120px;">Price</th>
                <th style="width:120px;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="5" class="empty">Loading items...</td></tr>
        </tbody>
    </table>

    <div class="summary">
        <div>Discount: <span id="discount"><%= discountParam %></span></div>
        <div style="font-size:18px; margin-top:6px;">Grand total: <span id="grandTotal"><%= grandTotalParam %></span></div>
    </div>
</div>

<script>
(function(){
    const params = new URLSearchParams(location.search);
    const invoiceId = params.get('invoiceId');
    const ctx = '<%= request.getContextPath() %>';
    const tbody = document.querySelector('#itemsTable tbody');
    const discountEl = document.getElementById('discount');
    const grandEl = document.getElementById('grandTotal');

    if (!invoiceId) {
        tbody.innerHTML = '<tr><td colspan="5" class="empty">Invoice id missing.</td></tr>';
        return;
    }

    fetch(ctx + '/ItemOrderController?invoiceId=' + encodeURIComponent(invoiceId))
        .then(r => {
            if (!r.ok) throw new Error('Network error');
            return r.json();
        })
        .then(items => {
            tbody.innerHTML = '';
            if (!items || items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="empty">No items found for this order.</td></tr>';
                return;
            }
            let sum = 0;
            items.forEach((it, i) => {
                const tr = document.createElement('tr');

                const tdNo = document.createElement('td'); tdNo.textContent = i+1; tr.appendChild(tdNo);
                const tdName = document.createElement('td'); tdName.textContent = it.name || ''; tr.appendChild(tdName);
                const tdQty = document.createElement('td'); tdQty.textContent = it.quantity || 0; tr.appendChild(tdQty);

                const tdPrice = document.createElement('td');
                tdPrice.textContent = Number(it.price || 0).toLocaleString(undefined, {maximumFractionDigits:0});
                tr.appendChild(tdPrice);

                const tdTotal = document.createElement('td');
                tdTotal.textContent = Number(it.total || 0).toLocaleString(undefined, {maximumFractionDigits:0});
                tr.appendChild(tdTotal);

                tbody.appendChild(tr);
                sum += Number(it.total || 0);
            });

            // update totals if passed values are empty or to ensure formatting
            const passedGrand = params.get('grandTotal');
            const passedDiscount = Number(params.get('discount') || 0);

            // if grandTotal passed, show formatted; otherwise compute from items minus discount
            if (passedGrand) {
                grandEl.textContent = Number(passedGrand).toLocaleString(undefined, {maximumFractionDigits:0});
            } else {
                const computed = sum - passedDiscount;
                grandEl.textContent = Number(computed).toLocaleString(undefined, {maximumFractionDigits:0});
            }
            discountEl.textContent = Number(passedDiscount).toLocaleString(undefined, {maximumFractionDigits:0});
        })
        .catch(err => {
            console.error(err);
            tbody.innerHTML = '<tr><td colspan="5" class="empty">Error loading items.</td></tr>';
        });
})();
</script>
</body>
</html>
