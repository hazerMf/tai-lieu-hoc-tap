<?php
require_once 'functions.php';
session_start();

// Get statistics data
$totalProducts = getTotalProducts();
$totalWarehouses = getTotalWarehouses();
$totalInventory = getTotalInventory();
$productsByCategory = getProductsByCategory();
$topProducts = getTopProducts();

// No need to redirect - this is the main page
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản Lý Kho Hàng</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
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
            <li class="active">
                <a href="#">
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
            <li>
                <a href="statistics.php">
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
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Tổng Sản Phẩm</h3>
                    </div>
                    <div class="panel-body">
                        <h3><?php echo $totalProducts; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">Tổng Kho Hàng</h3>
                    </div>
                    <div class="panel-body">
                        <h3><?php echo $totalWarehouses; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Tổng Hàng Tồn</h3>
                    </div>
                    <div class="panel-body">
                        <h3><?php echo number_format($totalInventory); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Sản Phẩm Tồn Kho Nhiều Nhất</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Tên Sản Phẩm</th>
                                        <th>Số Lượng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topProducts as $product): ?>
                                    <tr>
                                        <td><?php echo $product['name']; ?></td>
                                        <td><?php echo number_format($product['total']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Sản Phẩm Có Số Lượng Thấp Nhất</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Tên Sản Phẩm</th>
                                        <th>Số Lượng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Get products with least quantity
                                    $query = "SELECT p.id, p.name, p.category, i.name as warehouse_name, 
                                             COALESCE(SUM(pi.amount), 0) as total_quantity
                                             FROM Product p 
                                             LEFT JOIN ProductInventory pi ON p.id = pi.ProductId 
                                             LEFT JOIN Inventory i ON pi.InventoryId = i.id 
                                             GROUP BY p.id, p.name, p.category, i.name 
                                             ORDER BY total_quantity ASC 
                                             LIMIT 5";
                                    $result = $mysqli->query($query);
                                    $lowStockProducts = $result->fetch_all(MYSQLI_ASSOC);
                                    
                                    foreach ($lowStockProducts as $product): 
                                    ?>
                                    <tr>
                                        <td><?php echo $product['name']; ?></td>
                                        <td><?php echo number_format($product['total_quantity']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Sản Phẩm Mới Nhất</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên Sản Phẩm</th>
                                        <th>Danh Mục</th>
                                        <th>Giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($topProducts, 0, 5) as $product): ?>
                                    <tr>
                                        <td><?php echo $product['id']; ?></td>
                                        <td><?php echo $product['name']; ?></td>
                                        <td><?php echo $product['category']; ?></td>
                                        <td><?php echo number_format($product['price'], 2); ?> VNĐ</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Tổng Quan Kho Hàng</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Tên Kho</th>
                                        <th>Số Lượng Sản Phẩm</th>
                                        <th>Tổng Giá Trị</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $warehouses = getAllWarehouses();
                                    foreach ($warehouses as $warehouse):
                                        // Get number of items and total value for each warehouse
                                        $stmt = $mysqli->prepare("
                                            SELECT COUNT(pi.ProductId) as item_count, COALESCE(SUM(pi.amount * p.price), 0) as total_value
                                            FROM ProductInventory pi
                                            JOIN Product p ON pi.ProductId = p.id
                                            WHERE pi.InventoryId = ?
                                        ");
                                        $stmt->bind_param("s", $warehouse['id']);
                                        $stmt->execute();
                                        $result = $stmt->get_result()->fetch_assoc();
                                    ?>
                                    <tr>
                                        <td><?php echo $warehouse['name']; ?></td>
                                        <td><?php echo number_format($result['item_count']); ?></td>
                                        <td><?php echo number_format($result['total_value'], 2); ?> VNĐ</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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

            // Search functionality
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $("table tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
</body>
</html> 