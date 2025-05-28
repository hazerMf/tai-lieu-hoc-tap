<?php
require_once 'functions.php';
session_start();

// Handle product actions only if logged in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
    
    switch ($_POST['action']) {
        case 'add':
            $id = $_POST['id'];
            $name = $_POST['name'];
            $image = $_POST['image'];
            $category = $_POST['category'];
            $price = $_POST['price'];
            $quantity = $_POST['quantity'] ?? 0;
            $warehouseId = $_POST['warehouseId'] ?? null;
            
            if (addProduct($id, $name, $image, $category, $price, $quantity, $warehouseId)) {
                $success = "Thêm sản phẩm thành công!";
            } else {
                $error = "Lỗi khi thêm sản phẩm!";
            }
            break;
            
        case 'update':
            $id = $_POST['productId'];
            $name = $_POST['productName'];
            $image = $_POST['category'];
            $category = $_POST['category'];
            $price = $_POST['price'];
            $quantity = $_POST['quantity'] ?? 0;
            $warehouseId = $_POST['warehouseId'] ?? null;
            
            if (updateProduct($id, $name, $image, $category, $price, $quantity, $warehouseId)) {
                $success = "Cập nhật sản phẩm thành công!";
            } else {
                $error = "Có lỗi xảy ra khi cập nhật sản phẩm!";
            }
            break;
            
        case 'delete':
            $id = $_POST['productId'];
            if (deleteProduct($id)) {
                $success = "Xóa sản phẩm thành công!";
            } else {
                $error = "Có lỗi xảy ra khi xóa sản phẩm!";
            }
            break;
    }
}

// Get all products and warehouses
$products = getAllProducts();
$warehouses = getAllWarehouses();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản Lý Kho Hàng - Sản Phẩm</title>
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

        <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Tổng Sản Phẩm</h3>
                    </div>
                    <div class="panel-body">
                        <h3><?php echo getTotalProducts(); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">Danh Mục</h3>
                    </div>
                    <div class="panel-body">
                        <h3><?php echo count(getProductsByCategory()); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Tổng Hàng Tồn</h3>
                    </div>
                    <div class="panel-body">
                        <h3><?php echo number_format(getTotalInventory()); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Quản Lý Sản Phẩm</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="productTable">
                        <thead>
                            <tr>
                                <th class="sortable" data-column="id">ID <span class="sort-indicator"></span></th>
                                <th class="sortable" data-column="name">Tên Sản Phẩm <span class="sort-indicator"></span></th>
                                <th class="sortable" data-column="category">Danh Mục <span class="sort-indicator"></span></th>
                                <th class="sortable" data-column="price">Giá <span class="sort-indicator"></span></th>
                                <th class="sortable" data-column="stock">Tồn Kho <span class="sort-indicator"></span></th>
                                <th class="sortable" data-column="status">Trạng Thái <span class="sort-indicator"></span></th>
                                <?php if (isLoggedIn()): ?>
                                <th>Thao Tác</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo $product['name']; ?></td>
                                <td><?php echo $product['category']; ?></td>
                                <td><?php echo number_format($product['price'], 2); ?> VNĐ</td>
                                <td><?php echo number_format($product['stock']); ?></td>
                                <td>
                                    <?php if ($product['stock'] > 0): ?>
                                    <span class="label label-success">Còn Hàng</span>
                                    <?php else: ?>
                                    <span class="label label-danger">Hết Hàng</span>
                                    <?php endif; ?>
                                </td>
                                <?php if (isLoggedIn()): ?>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-warning btn-sm" onclick="editProduct('<?php echo $product['id']; ?>', '<?php echo $product['name']; ?>', '<?php echo $product['category']; ?>', '<?php echo $product['price']; ?>')">
                                            <span class="glyphicon glyphicon-pencil"></span>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteProduct('<?php echo $product['id']; ?>')">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </button>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php if (isLoggedIn()): ?>
    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Chỉnh Sửa Sản Phẩm</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="productId" id="editProductId">
                        <div class="form-group">
                            <label for="editProductName">Tên Sản Phẩm:</label>
                            <input type="text" class="form-control" id="editProductName" name="productName" required>
                        </div>
                        <div class="form-group">
                            <label for="editCategory">Danh Mục:</label>
                            <select class="form-control" id="editCategory" name="category" required>
                                <option value="Điện tử">Điện tử</option>
                                <option value="Nội thất">Nội thất</option>
                                <option value="Chiếu sáng">Chiếu sáng</option>
                                <option value="Thiết bị điện">Thiết bị điện</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editPrice">Giá:</label>
                            <input type="number" step="0.01" class="form-control" id="editPrice" name="price" required>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <span class="glyphicon glyphicon-ok"></span> Cập Nhật
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Product Modal -->
    <div id="deleteProductModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Xác Nhận Xóa</h4>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa sản phẩm này?</p>
                    <form method="POST" action="" id="deleteProductForm">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="productId" id="deleteProductId">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                    <button type="submit" form="deleteProductForm" class="btn btn-danger">Xóa</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

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

            // Table sorting
            let sortColumn = 'id';
            let sortAsc = true;

            function sortTable(column, asc) {
                const tbody = $('#productTable tbody');
                const rows = tbody.find('tr').get();
                rows.sort(function(a, b) {
                    let A = $(a).children('td').eq(getColIndex(column)).text().trim();
                    let B = $(b).children('td').eq(getColIndex(column)).text().trim();
                    if (column === 'price' || column === 'stock') {
                        A = parseFloat(A.replace(/[^0-9.-]+/g, ''));
                        B = parseFloat(B.replace(/[^0-9.-]+/g, ''));
                    }
                    if (column === 'status') {
                        // Convert status to a value for sorting
                        A = $(a).children('td').eq(getColIndex(column)).find('span').hasClass('label-success') ? 1 : 0;
                        B = $(b).children('td').eq(getColIndex(column)).find('span').hasClass('label-success') ? 1 : 0;
                    }
                    if (A < B) return asc ? -1 : 1;
                    if (A > B) return asc ? 1 : -1;
                    return 0;
                });
                $.each(rows, function(index, row) {
                    tbody.append(row);
                });
            }

            function getColIndex(column) {
                switch(column) {
                    case 'id': return 0;
                    case 'name': return 1;
                    case 'category': return 2;
                    case 'price': return 3;
                    case 'stock': return 4;
                    case 'status': return 5;
                }
            }

            // Click event for sorting
            $('.sortable').click(function() {
                const column = $(this).data('column');
                if (sortColumn === column) {
                    sortAsc = !sortAsc;
                } else {
                    sortColumn = column;
                    sortAsc = true;
                }
                updateSortIndicators();
                sortTable(sortColumn, sortAsc);
            });

            function updateSortIndicators() {
                $('.sortable .sort-indicator').text('');
                const indicator = sortAsc ? '▲' : '▼';
                $(`.sortable[data-column="${sortColumn}"] .sort-indicator`).text(indicator);
            }

            // Initial sort by ID
            updateSortIndicators();
            sortTable('id', true);
        });

        function editProduct(id, name, category, price) {
            $('#editProductId').val(id);
            $('#editProductName').val(name);
            $('#editCategory').val(category);
            $('#editPrice').val(price);
            $('#editProductModal').modal('show');
        }

        function deleteProduct(id) {
            $('#deleteProductId').val(id);
            $('#deleteProductModal').modal('show');
        }
    </script>
</body>
</html> 