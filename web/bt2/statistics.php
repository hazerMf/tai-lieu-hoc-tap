<?php
require_once 'functions.php';
session_start();

// Get statistics data
$totalProducts = getTotalProducts();
$totalWarehouses = getTotalWarehouses();
$totalInventory = getTotalInventory();
$productsByCategory = getProductsByCategory();
$topProducts = getTopProducts();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản Lý Kho Hàng - Thống Kê</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Sidebar Toggle Button -->
    <button class="btn btn-default sidebar-toggle" id="sidebarToggle">
        <span class="glyphicon glyphicon-menu-hamburger"></span>
    </button>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <span class="glyphicon glyphicon-home"></span> Quản Lý Kho Hàng
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="index.php">
                    <span class="glyphicon glyphicon-dashboard"></span> Bảng Điều Khiển
                </a>
            </li>
            <li>
                <a href="product.php">
                    <span class="glyphicon glyphicon-list"></span> Sản Phẩm
                </a>
            </li>
            <li>
                <a href="inventory_items.php">
                    <span class="glyphicon glyphicon-th-list"></span> Sản Phẩm Theo Kho
                </a>
            </li>
            <li>
                <a href="warehouse.php">
                    <span class="glyphicon glyphicon-inbox"></span> Kho Hàng
                </a>
            </li>
            <li class="active">
                <a href="#">
                    <span class="glyphicon glyphicon-stats"></span> Thống Kê
                </a>
            </li>
            <?php if (isLoggedIn()): ?>
            <li>
                <a href="export_excel.php">
                    <span class="glyphicon glyphicon-download"></span> Xuất Excel
                </a>
            </li>
            <?php endif; ?>
            <li>
                <?php if (isLoggedIn()): ?>
                <a href="logout.php" id="loginLogoutBtn">
                    <span class="glyphicon glyphicon-log-out"></span> <span class="btn-text">Đăng Xuất</span>
                </a>
                <?php else: ?>
                <a href="login.php" id="loginLogoutBtn">
                    <span class="glyphicon glyphicon-log-in"></span> <span class="btn-text">Đăng Nhập</span>
                </a>
                <?php endif; ?>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm...">
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group">
                            <button class="btn btn-default"><span class="glyphicon glyphicon-bell"></span></button>
                            <button class="btn btn-default"><span class="glyphicon glyphicon-user"></span></button>
                            <?php if (isLoggedIn()): ?>
                            <span class="btn btn-default" id="userDisplay"><?php echo $_SESSION['name']; ?></span>
                            <?php else: ?>
                            <a href="login.php" class="btn btn-default">Đăng Nhập</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Tổng Sản Phẩm</h3>
                    </div>
                    <div class="panel-body">
                        <h3><?php echo $totalProducts; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">Tổng Kho Hàng</h3>
                    </div>
                    <div class="panel-body">
                        <h3><?php echo $totalWarehouses; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Tổng Hàng Tồn</h3>
                    </div>
                    <div class="panel-body">
                        <h3><?php echo number_format($totalInventory); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <h3 class="panel-title">Danh Mục</h3>
                    </div>
                    <div class="panel-body">
                        <h3><?php echo count($productsByCategory); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Sản Phẩm Theo Danh Mục</h3>
                    </div>
                    <div class="panel-body">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Sản Phẩm Tồn Kho Nhiều Nhất</h3>
                    </div>
                    <div class="panel-body">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Handle sidebar toggle
            $('#sidebarToggle').click(function() {
                $('.sidebar').toggleClass('active');
            });

            // Handle mobile menu clicks
            if ($(window).width() <= 768) {
                $('.sidebar-menu a').click(function() {
                    $('.sidebar').removeClass('active');
                });
            }

            // Category Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_column($productsByCategory, 'category')); ?>,
                    datasets: [{
                        label: 'Số Lượng Sản Phẩm',
                        data: <?php echo json_encode(array_column($productsByCategory, 'count')); ?>,
                        backgroundColor: [
                            '#3498db',
                            '#2ecc71',
                            '#f1c40f',
                            '#e74c3c',
                            '#9b59b6'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Top Products Chart
            const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
            new Chart(topProductsCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode(array_column($topProducts, 'name')); ?>,
                    datasets: [{
                        data: <?php echo json_encode(array_column($topProducts, 'total')); ?>,
                        backgroundColor: [
                            '#3498db',
                            '#2ecc71',
                            '#f1c40f',
                            '#e74c3c',
                            '#9b59b6'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>
</body>
</html> 