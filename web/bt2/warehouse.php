<?php
require_once 'functions.php';
session_start();

// Handle warehouse and product actions only if logged in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
    
    switch ($_POST['action']) {
        case 'add':
            if (isset($_POST['warehouseId'])) {
                // Add product to warehouse
                $id = $_POST['id'];
                $name = $_POST['name'];
                $image = $_POST['image'];
                $category = $_POST['category'];
                $price = $_POST['price'];
                $quantity = $_POST['quantity'] ?? 0;
                $warehouseId = $_POST['warehouseId'];
                
                if (addProduct($id, $name, $image, $category, $price, $quantity, $warehouseId)) {
                    $success = "Thêm sản phẩm vào kho thành công!";
                } else {
                    $error = "Lỗi khi thêm sản phẩm vào kho!";
                }
            } else {
                // Add new warehouse
                $id = 'KHO' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                $name = $_POST['warehouseName'];
                $address = $_POST['address'];
                $capacity = $_POST['capacity'];
                $phone = $_POST['phone'];
                
                if (addWarehouse($id, $name, $address, $capacity, $phone)) {
                    $success = "Thêm kho hàng thành công!";
                } else {
                    $error = "Có lỗi xảy ra khi thêm kho hàng!";
                }
            }
            break;
            
        case 'update':
            $id = $_POST['warehouseId'];
            $name = $_POST['warehouseName'];
            $address = $_POST['address'];
            $capacity = $_POST['capacity'];
            $phone = $_POST['phone'];
            
            if (updateWarehouse($id, $name, $address, $capacity, $phone)) {
                $success = "Cập nhật kho hàng thành công!";
            } else {
                $error = "Có lỗi xảy ra khi cập nhật kho hàng!";
            }
            break;
            
        case 'delete':
            $id = $_POST['warehouseId'];
            if (deleteWarehouse($id)) {
                $success = "Xóa kho hàng thành công!";
            } else {
                $error = "Có lỗi xảy ra khi xóa kho hàng!";
            }
            break;

        case 'add_quantity':
            $warehouseId = $_POST['warehouseId'];
            $productId = $_POST['productId'];
            $quantity = intval($_POST['quantity']);
            // Update the quantity in ProductInventory
            $stmt = $mysqli->prepare("UPDATE ProductInventory SET amount = amount + ? WHERE ProductId = ? AND InventoryId = ?");
            $stmt->bind_param("iss", $quantity, $productId, $warehouseId);
            $stmt->execute();
            // If no row was updated, insert a new row
            if ($stmt->affected_rows === 0) {
                $stmt = $mysqli->prepare("INSERT INTO ProductInventory (ProductId, InventoryId, amount) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $productId, $warehouseId, $quantity);
                $stmt->execute();
            }
            $success = "Nhập thêm số lượng thành công!";
            break;
    }
}

// Get all warehouses
$warehouses = getAllWarehouses();
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
            <li class="active">
                <a href="#">
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

        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <h3 class="panel-title" style="margin: 0; display: inline-block;">Quản Lý Kho Hàng</h3>
                <?php if (isLoggedIn()): ?>
                <div class="pull-right">
                    <button class="btn btn-primary" style="font-size: 16px; padding: 8px 20px;" data-toggle="modal" data-target="#addWarehouseModal">
                        <span class="glyphicon glyphicon-plus"></span> Thêm Kho Hàng
                    </button>
                </div>
                <?php endif; ?>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên Kho</th>
                                <th>Địa Chỉ</th>
                                <th>Sức Chứa</th>
                                <th>Số Điện Thoại</th>
                                <th>Ngày Tạo</th>
                                <?php if (isLoggedIn()): ?>
                                <th>Thao Tác</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($warehouses as $warehouse): ?>
                            <tr>
                                <td><?php echo $warehouse['id']; ?></td>
                                <td><?php echo $warehouse['name']; ?></td>
                                <td><?php echo $warehouse['address']; ?></td>
                                <td><?php echo number_format($warehouse['capacity']); ?></td>
                                <td><?php echo $warehouse['phone']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($warehouse['create_at'])); ?></td>
                                <?php if (isLoggedIn()): ?>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-primary btn-sm" onclick="addProductToWarehouse('<?php echo $warehouse['id']; ?>', '<?php echo $warehouse['name']; ?>')">
                                            <span class="glyphicon glyphicon-plus"></span> Thêm Sản Phẩm
                                        </button>
                                        <button class="btn btn-warning btn-sm" onclick="editWarehouse('<?php echo $warehouse['id']; ?>', '<?php echo $warehouse['name']; ?>', '<?php echo $warehouse['address']; ?>', '<?php echo $warehouse['capacity']; ?>', '<?php echo $warehouse['phone']; ?>')">
                                            <span class="glyphicon glyphicon-pencil"></span>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteWarehouse('<?php echo $warehouse['id']; ?>')">
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

        <?php foreach ($warehouses as $warehouse): ?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">Sản phẩm trong <?php echo $warehouse['name']; ?></h4>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Mã SP</th>
                                    <th>Tên Sản Phẩm</th>
                                    <th>Danh Mục</th>
                                    <th>Số Lượng</th>
                                    <?php if (isLoggedIn()): ?>
                                    <th>Thao Tác</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $items = getItemsByInventory($warehouse['id']);
                                foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo $item['id']; ?></td>
                                    <td><?php echo $item['name']; ?></td>
                                    <td><?php echo $item['category']; ?></td>
                                    <td><?php echo number_format($item['quantity']); ?></td>
                                    <?php if (isLoggedIn()): ?>
                                    <td>
                                        <button class="btn btn-success btn-sm" onclick="openAddQuantityModal('<?php echo $warehouse['id']; ?>', '<?php echo $item['id']; ?>', '<?php echo $item['name']; ?>')">
                                            <span class="glyphicon glyphicon-plus"></span> Nhập Thêm
                                        </button>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (isLoggedIn()): ?>
    <!-- Add Warehouse Modal -->
    <div id="addWarehouseModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Thêm Kho Hàng Mới</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label for="warehouseName">Tên Kho Hàng:</label>
                            <input type="text" class="form-control" id="warehouseName" name="warehouseName" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Địa Chỉ:</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        <div class="form-group">
                            <label for="capacity">Sức Chứa:</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Số Điện Thoại:</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <span class="glyphicon glyphicon-ok"></span> Thêm Kho Hàng
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Warehouse Modal -->
    <div id="editWarehouseModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Chỉnh Sửa Kho Hàng</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="warehouseId" id="editWarehouseId">
                        <div class="form-group">
                            <label for="editWarehouseName">Tên Kho Hàng:</label>
                            <input type="text" class="form-control" id="editWarehouseName" name="warehouseName" required>
                        </div>
                        <div class="form-group">
                            <label for="editAddress">Địa Chỉ:</label>
                            <input type="text" class="form-control" id="editAddress" name="address" required>
                        </div>
                        <div class="form-group">
                            <label for="editCapacity">Sức Chứa:</label>
                            <input type="number" class="form-control" id="editCapacity" name="capacity" required>
                        </div>
                        <div class="form-group">
                            <label for="editPhone">Số Điện Thoại:</label>
                            <input type="text" class="form-control" id="editPhone" name="phone" required>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <span class="glyphicon glyphicon-ok"></span> Cập Nhật
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Warehouse Modal -->
    <div id="deleteWarehouseModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Xác Nhận Xóa</h4>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa kho hàng này?</p>
                    <form method="POST" action="" id="deleteWarehouseForm">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="warehouseId" id="deleteWarehouseId">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                    <button type="submit" form="deleteWarehouseForm" class="btn btn-danger">Xóa</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Thêm Sản Phẩm Mới vào Kho <span id="warehouseName"></span></h4>
                </div>
                <div class="modal-body">
                    <form id="addProductForm" method="POST">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="warehouseId" id="addProductWarehouseId">
                        <div class="form-group">
                            <label for="addProductId">Mã sản phẩm:</label>
                            <input type="text" class="form-control" id="addProductId" name="id" required>
                        </div>
                        <div class="form-group">
                            <label for="addProductName">Tên sản phẩm:</label>
                            <input type="text" class="form-control" id="addProductName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="addProductImage">Hình ảnh:</label>
                            <input type="text" class="form-control" id="addProductImage" name="image" required>
                        </div>
                        <div class="form-group">
                            <label for="addProductCategory">Danh mục:</label>
                            <select class="form-control" id="addProductCategory" name="category" required>
                                <option value="Điện tử">Điện tử</option>
                                <option value="Nội thất">Nội thất</option>
                                <option value="Chiếu sáng">Chiếu sáng</option>
                                <option value="Thiết bị điện">Thiết bị điện</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="addProductPrice">Giá:</label>
                            <input type="number" step="0.01" class="form-control" id="addProductPrice" name="price" required>
                        </div>
                        <div class="form-group">
                            <label for="addProductQuantity">Số lượng:</label>
                            <input type="number" class="form-control" id="addProductQuantity" name="quantity" min="0" required>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <span class="glyphicon glyphicon-ok"></span> Thêm Sản Phẩm
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Quantity Modal -->
    <div id="addQuantityModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Nhập Thêm Số Lượng Cho <span id="addQuantityProductName"></span></h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_quantity">
                        <input type="hidden" name="warehouseId" id="addQuantityWarehouseId">
                        <input type="hidden" name="productId" id="addQuantityProductId">
                        <div class="form-group">
                            <label for="addQuantityAmount">Số Lượng Thêm:</label>
                            <input type="number" class="form-control" id="addQuantityAmount" name="quantity" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-success">Nhập Thêm</button>
                    </div>
                </form>
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
        });

        function addProductToWarehouse(warehouseId, warehouseName) {
            $('#addProductWarehouseId').val(warehouseId);
            $('#warehouseName').text(warehouseName);
            $('#addProductModal').modal('show');
        }

        function editWarehouse(id, name, address, capacity, phone) {
            $('#editWarehouseId').val(id);
            $('#editWarehouseName').val(name);
            $('#editAddress').val(address);
            $('#editCapacity').val(capacity);
            $('#editPhone').val(phone);
            $('#editWarehouseModal').modal('show');
        }

        function deleteWarehouse(id) {
            $('#deleteWarehouseId').val(id);
            $('#deleteWarehouseModal').modal('show');
        }

        function openAddQuantityModal(warehouseId, productId, productName) {
            $('#addQuantityWarehouseId').val(warehouseId);
            $('#addQuantityProductId').val(productId);
            $('#addQuantityProductName').text(productName);
            $('#addQuantityAmount').val('');
            $('#addQuantityModal').modal('show');
        }
    </script>
</body>
</html> 