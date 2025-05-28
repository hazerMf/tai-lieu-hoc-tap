<?php
require_once 'functions.php';
session_start();

// Redirect to index.php if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $user = authenticateUser($username, $password);
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['isLoggedIn'] = true;
        
        header('Location: index.php');
        exit;
    } else {
        $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng Nhập - Quản Lý Kho Hàng</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-heading text-center">
                        <h3 class="panel-title">Quản Lý Kho Hàng</h3>
                        <p class="text-muted">Vui lòng đăng nhập để thực hiện thay đổi dữ liệu</p>
                    </div>
                    <div class="panel-body">
                        <form id="loginForm" method="POST" action="">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Tên đăng nhập" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Mật khẩu" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Đăng Nhập</button>
                            <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="glyphicon glyphicon-exclamation-sign"></i> <?php echo $error; ?>
                            </div>
                            <?php endif; ?>
                        </form>
                        <div class="text-center" style="margin-top: 15px;">
                            <a href="index.php" class="btn btn-link">Quay lại trang chủ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 