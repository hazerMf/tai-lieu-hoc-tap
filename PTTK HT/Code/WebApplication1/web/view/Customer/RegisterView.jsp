<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #d9d9d9;
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
    .container {
      background-color: white;
      width: 700px;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    h1 {
      text-align: center;
      margin-bottom: 30px;
    }
    form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px 20px;
    }
    label {
      display: block;
      font-size: 13px;
      color: #333;
      margin-bottom: 6px;
    }
    .field {
      display: flex;
      flex-direction: column;
    }
    input {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
      width: 100%;
      box-sizing: border-box;
    }
    .actions {
      grid-column: span 2;
      text-align: right;
      margin-top: 10px;
    }
    .btn {
      background-color: #2563eb;
      color: white;
      border: none;
      padding: 10px 25px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s;
    }
    .btn:hover {
      background-color: #1e50c9;
    }
    .back {
      background-color: transparent;
      color: #000;
      border: none;
      cursor: pointer;
      font-size: 16px;
      margin-left: 15px;
    }
    .back:hover {
      text-decoration: underline;
    }
    .required-note {
      font-size: 12px;
      color: #6b7280;
      font-style: italic;
      margin: 8px 0 18px;
      grid-column: span 2;
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
      color: #333;
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
      margin: 5px;
    }
    .modal-btn:hover {
      background-color: #1e40af;
    }
    .modal-btn.success {
      background-color: #10b981;
    }
    .modal-btn.success:hover {
      background-color: #059669;
    }
  </style>
</head>
<body>
  <div class="banner">
    Supermarket Management
  </div>
  
  <div class="content">
    <div class="container">
      <h1>Register</h1>
      
      <form action="${pageContext.request.contextPath}/CustomerController" method="POST">
        <div class="field">
          <label for="name">Full Name*</label>
          <input id="name" type="text" name="name" placeholder="Full Name" required>
        </div>

        <div class="field">
          <label for="phone">Phone Number*</label>
          <input id="phone" type="text" name="phone" placeholder="Phone Number" required>
        </div>

        <div class="field">
          <label for="address">Address</label>
          <input id="address" type="text" name="address" placeholder="Address">
        </div>

        <div class="field">
          <label for="email">Email</label>
          <input id="email" type="email" name="email" placeholder="Email">
        </div>

        <div class="field">
          <label for="password">Password*</label>
          <input id="password" type="password" name="password" placeholder="Password" required>
        </div>

        <div class="field">
          <label for="confirm">Confirm Password*</label>
          <input id="confirm" type="password" name="confirm" placeholder="Confirm Password" required>
        </div>

        <p class="required-note">*Required information</p>

        <div class="actions">
          <button type="submit" class="btn">Register</button>
          <button type="button" class="back" onclick="window.location.href='<%= request.getContextPath() %>/MenuView.jsp'">Back</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Success Modal -->
  <div id="successModal" class="modal">
    <div class="modal-content">
      <h2>Registration Successful!</h2>
      <p><%= request.getAttribute("message") != null ? request.getAttribute("message") : "" %></p>
      <button class="modal-btn success" onclick="window.location.href='<%= request.getContextPath() %>/MenuView.jsp'">Back to Main Menu</button>
    </div>
  </div>

  <!-- Error Modal -->
  <div id="errorModal" class="modal">
    <div class="modal-content">
      <h2>Registration Failed</h2>
      <p><%= request.getAttribute("message") != null ? request.getAttribute("message") : "" %></p>
      <button class="modal-btn" onclick="closeErrorModal()">OK</button>
    </div>
  </div>

  <script>
    <%
      String status = (String) request.getAttribute("status");
      if ("success".equals(status)) {
    %>
      document.getElementById('successModal').style.display = 'block';
    <%
      } else if ("error".equals(status)) {
    %>
      document.getElementById('errorModal').style.display = 'block';
    <%
      }
    %>

    function closeErrorModal() {
      document.getElementById('errorModal').style.display = 'none';
    }
  </script>
</body>
</html>
