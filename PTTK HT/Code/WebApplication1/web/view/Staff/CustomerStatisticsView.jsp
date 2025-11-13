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
        
        .space{
            padding: 30px;
        }
        .loading { color:#666; font-size:13px; margin-left:10px; display:none; }
    </style>
</head>
<body>
    <div class="banner">
        Supermarket Management
    </div>
    
    <div class="container">
        <button class="back-btn" onclick="window.location.href='<%= request.getContextPath() %>/MenuView.jsp'">Back</button>
        <h2>View Customer Statistics</h2>

        <form id="searchForm" class="filter" method="post">
            <input type="date" name="fromDate" id="fromDate" required>
            <input type="date" name="toDate" id="toDate" required>
            <button type="submit">Search</button>
            <span id="loading" class="loading">Loading...</span>
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
            <tbody id="statsBody">
                <tr><td colspan="5" class="empty">Please select a date range and click Search.</td></tr>
            </tbody>
        </table>
    </div>

    <script>
    (function(){
        const form = document.getElementById('searchForm');
        const loading = document.getElementById('loading');
        const tbody = document.getElementById('statsBody');
        const ctx = '<%= request.getContextPath() %>';

        form.addEventListener('submit', function(e){
            e.preventDefault();
            
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;
            
            if (!fromDate || !toDate) return;

            loading.style.display = 'inline';
            
            const fd = new URLSearchParams();
            fd.append('fromDate', fromDate);
            fd.append('toDate', toDate);

            fetch(ctx + '/CustomerStatisticsController', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: fd.toString()
            })
            .then(r => {
                if (!r.ok) throw new Error('Network error');
                return r.json();
            })
            .then(data => {
                tbody.innerHTML = '';
                
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="empty">No data found for selected date range.</td></tr>';
                    return;
                }
                
                data.forEach((cs, idx) => {
                    const tr = document.createElement('tr');

                    const tdNo = document.createElement('td');
                    tdNo.textContent = idx + 1;
                    tr.appendChild(tdNo);

                    const tdName = document.createElement('td');
                    tdName.textContent = cs.name || '';
                    tr.appendChild(tdName);

                    const tdPhone = document.createElement('td');
                    tdPhone.textContent = cs.phoneNumber || '';
                    tr.appendChild(tdPhone);

                    const tdRevenue = document.createElement('td');
                    const rev = Number(cs.revenue) || 0;
                    tdRevenue.textContent = rev.toLocaleString(undefined, {maximumFractionDigits:0});
                    tr.appendChild(tdRevenue);

                    const tdAction = document.createElement('td');
                    const a = document.createElement('a');
                    a.className = 'view-btn';
                    const encFrom = encodeURIComponent(fromDate);
                    const encTo = encodeURIComponent(toDate);
                    const encName = encodeURIComponent(cs.name || '');
                    a.href = ctx + '/view/Staff/CustomerInvoiceView.jsp?customerId=' + cs.customerId + 
                             '&fromDate=' + encFrom + '&toDate=' + encTo + '&customerName=' + encName;
                    a.textContent = 'View Invoices';
                    tdAction.appendChild(a);
                    tr.appendChild(tdAction);

                    tbody.appendChild(tr);
                });
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = '<tr><td colspan="5" class="empty">Error loading data.</td></tr>';
            })
            .finally(() => {
                loading.style.display = 'none';
            });
        });
    })();
    </script>
</body>
</html>
