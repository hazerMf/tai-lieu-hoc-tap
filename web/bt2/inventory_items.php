<?php
require_once 'functions.php';
session_start();

// Get all inventories/warehouses
$warehouses = getAllWarehouses();

// Get selected inventory
$selectedInventoryId = isset($_GET['inventory']) ? $_GET['inventory'] : ($warehouses[0]['id'] ?? null);

// Get items in selected inventory
$items = [];
if ($selectedInventoryId) {
    $items = getItemsByInventory($selectedInventoryId);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản Lý Sản Phẩm Theo Kho</title>
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

        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <h3 class="panel-title" style="margin: 0; display: inline-block;">Sản Phẩm Theo Kho</h3>
            </div>
            <div class="panel-body">
                <form method="GET" class="form-inline" style="margin-bottom: 20px;">
                    <label for="inventory">Chọn Kho:</label>
                    <select name="inventory" id="inventory" class="form-control" onchange="this.form.submit()">
                        <?php foreach ($warehouses as $warehouse): ?>
                        <option value="<?php echo $warehouse['id']; ?>" <?php if ($warehouse['id'] == $selectedInventoryId) echo 'selected'; ?>>
                            <?php echo $warehouse['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="inventoryTable">
                        <thead>
                            <tr>
                                <th class="sortable" data-column="id">ID <span class="sort-indicator"></span></th>
                                <th class="sortable" data-column="name">Tên Sản Phẩm <span class="sort-indicator"></span></th>
                                <th class="sortable" data-column="category">Danh Mục <span class="sort-indicator"></span></th>
                                <th class="sortable" data-column="quantity">Số Lượng <span class="sort-indicator"></span></th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><?php echo $item['name']; ?></td>
                                <td><?php echo $item['category']; ?></td>
                                <td><?php echo number_format($item['quantity']); ?></td>
                                <td>
                                    <?php if (isLoggedIn()): ?>
                                    <button class="btn btn-info btn-sm" onclick="openTransferModal('<?php echo $item['id']; ?>', '<?php echo $item['name']; ?>', <?php echo $item['quantity']; ?>)">
                                        <span class="glyphicon glyphicon-transfer"></span> Chuyển Kho
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Modal -->
    <div id="transferModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="transfer_item.php">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Chuyển Sản Phẩm</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="item_id" id="transferItemId">
                        <input type="hidden" name="from_inventory" value="<?php echo $selectedInventoryId; ?>">
                        <div class="form-group">
                            <label for="transferItemName">Tên Sản Phẩm:</label>
                            <input type="text" class="form-control" id="transferItemName" readonly>
                        </div>
                        <div class="form-group">
                            <label for="transferQuantity">Số Lượng Chuyển:</label>
                            <input type="number" class="form-control" id="transferQuantity" name="quantity" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="to_inventory">Chuyển đến Kho:</label>
                            <select name="to_inventory" id="to_inventory" class="form-control" required>
                                <option value="retailer">Xuất cho đại lý/khách hàng</option>
                                <?php foreach ($warehouses as $warehouse): ?>
                                    <?php if ($warehouse['id'] != $selectedInventoryId): ?>
                                    <option value="<?php echo $warehouse['id']; ?>"><?php echo $warehouse['name']; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Chuyển</button>
                    </div>
                </form>
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

            // Table sorting
            let sortColumn = 'id';
            let sortAsc = true;

            function sortTable(column, asc) {
                const tbody = $('#inventoryTable tbody');
                const rows = tbody.find('tr').get();
                rows.sort(function(a, b) {
                    let A = $(a).children('td').eq(getColIndex(column)).text().trim();
                    let B = $(b).children('td').eq(getColIndex(column)).text().trim();
                    if (column === 'quantity') {
                        A = parseFloat(A.replace(/,/g, ''));
                        B = parseFloat(B.replace(/,/g, ''));
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
                    case 'quantity': return 3;
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

        function openTransferModal(itemId, itemName, maxQuantity) {
            $('#transferItemId').val(itemId);
            $('#transferItemName').val(itemName);
            $('#transferQuantity').attr('max', maxQuantity);
            $('#transferModal').modal('show');
        }
    </script>
</body>
</html> 