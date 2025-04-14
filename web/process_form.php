<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $city = $_POST['city'] ?? '';
    $description = $_POST['description'] ?? '';

    // Basic validation
    $errors = [];

    if (empty($first_name)) {
        $errors[] = "Họ không được để trống";
    }
    if (empty($last_name)) {
        $errors[] = "Tên không được để trống";
    }
    if (empty($password)) {
        $errors[] = "Mật khẩu không được để trống";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    }
    if (empty($dob)) {
        $errors[] = "Ngày sinh không được để trống";
    }
    if (empty($gender)) {
        $errors[] = "Giới tính không được để trống";
    }
    if (empty($city)) {
        $errors[] = "Thành phố không được để trống";
    }

    // If there are no errors, process the data
    if (empty($errors)) {
        // Here you would typically:
        // 1. Hash the password
        // 2. Save to database
        // 3. Send confirmation email
        // For now, we'll just show a success message
        
        $success_message = "Đăng ký thành công!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả đăng ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .result-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="result-container">
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
                <div class="mt-3">
                    <h4>Thông tin đã đăng ký:</h4>
                    <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>Ngày sinh:</strong> <?php echo htmlspecialchars($dob); ?></p>
                    <p><strong>Giới tính:</strong> <?php echo htmlspecialchars($gender); ?></p>
                    <p><strong>Thành phố:</strong> <?php echo htmlspecialchars($city); ?></p>
                    <?php if (!empty($description)): ?>
                        <p><strong>Mô tả:</strong> <?php echo nl2br(htmlspecialchars($description)); ?></p>
                    <?php endif; ?>
                </div>
            <?php elseif (isset($errors)): ?>
                <div class="alert alert-danger">
                    <h4>Có lỗi xảy ra:</h4>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="mt-3">
                <a href="index.html" class="btn btn-primary">Quay lại form đăng ký</a>
            </div>
        </div>
    </div>
</body>
</html> 