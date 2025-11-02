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
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background-color: white;
      width: 700px;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    h2 {
      text-align: center;
      font-style: italic;
      margin-bottom: 30px;
    }
    form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px 20px;
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
  </style>
</head>
<body>
  <div class="container">
    <h2>Register</h2>
    <form action="${pageContext.request.contextPath}/CustomerController" method="POST">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="text" name="phone" placeholder="Phone Number" required>
      <input type="text" name="address" placeholder="Address" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="confirm" placeholder="Confirm Password" required>
      
        <button type="submit" class="btn">Register</button>
        <button type="button" class="back" onclick="window.location.href='../../MenuView.jsp'">Back</button>
      
    </form>
  </div>
</body>
</html>
