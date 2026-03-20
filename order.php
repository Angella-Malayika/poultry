<?php
require_once 'auth_required.php';
$message = '';
$order_placed = false;
$order_details = [];

$product_options = [
    'soya' => 'Soya',
    'grower' => 'Grower Mash',
    'layer' => 'Layer Mash',
    'broiler' => 'Broiler Feed',
    'chicks' => 'One-Day-Old Chicks',
    'pellets' => 'Pellets',
    'feed-additives' => 'Feed Additives',
    'feed-concentrates' => 'Feed Concentrates',
    'chicken-drinkers' => 'Chicken Drinkers',
    'brooder-heater' => 'Brooder Heater',
];

$unit_options = [
    'kg' => 'Kg',
    'bags' => 'Bags',
    'pieces' => 'Pieces',
    'trays' => 'Trays',
];

function default_unit_for_product($product_key)
{
    if (in_array($product_key, ['chicks', 'chicken-drinkers', 'brooder-heater'], true)) {
        return 'pieces';
    }
    return 'kg';
}

function format_quantity_value($value)
{
    $formatted = number_format((float) $value, 2, '.', '');
    return rtrim(rtrim($formatted, '0'), '.');
}

function resolve_product_key($raw_value, $product_options)
{
    $normalized = strtolower(trim((string) $raw_value));
    if ($normalized === '') {
        return '';
    }

    foreach ($product_options as $key => $label) {
        if ($normalized === strtolower($key) || $normalized === strtolower($label)) {
            return $key;
        }
    }

    if (strpos($normalized, 'chick') !== false) {
        return 'chicks';
    }
    if (strpos($normalized, 'broiler') !== false) {
        return 'broiler';
    }
    if (strpos($normalized, 'layer') !== false) {
        return 'layer';
    }
    if (strpos($normalized, 'soya') !== false) {
        return 'soya';
    }

    return '';
}

$full_name_value = '';
$phone_value = '';
$address_value = '';
$delivery_date_value = '';

$prefill_product = isset($_GET['product']) ? resolve_product_key($_GET['product'], $product_options) : '';
$prefill_unit = default_unit_for_product($prefill_product);
$order_item_inputs = [
    ['product' => $prefill_product, 'quantity' => '1', 'unit' => $prefill_unit],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'connection.php';

    $full_name_value = trim((string) ($_POST['full_name'] ?? ''));
    $phone_value = trim((string) ($_POST['phone'] ?? ''));
    $address_value = trim((string) ($_POST['address'] ?? ''));
    $delivery_date_value = trim((string) ($_POST['delivery_date'] ?? ''));

    $selected_products = $_POST['products'] ?? [];
    $selected_quantities = $_POST['quantities'] ?? [];
    $selected_units = $_POST['units'] ?? [];

    if (!is_array($selected_products)) {
        $selected_products = [];
    }
    if (!is_array($selected_quantities)) {
        $selected_quantities = [];
    }
    if (!is_array($selected_units)) {
        $selected_units = [];
    }

    $order_item_inputs = [];
    $item_totals = [];
    $total_quantity = 0.0;

    foreach ($selected_products as $index => $product_key_raw) {
        $product_key = trim((string) $product_key_raw);
        $item_quantity_raw = isset($selected_quantities[$index]) ? str_replace(',', '.', (string) $selected_quantities[$index]) : '0';
        $item_quantity = round((float) $item_quantity_raw, 2);
        $unit_key = isset($selected_units[$index]) ? trim((string) $selected_units[$index]) : '';
        if (!isset($unit_options[$unit_key])) {
            $unit_key = default_unit_for_product($product_key);
        }

        if ($product_key === '' && $item_quantity <= 0) {
            continue;
        }

        $safe_display_quantity = $item_quantity > 0 ? $item_quantity : 1;
        $order_item_inputs[] = [
            'product' => $product_key,
            'quantity' => format_quantity_value($safe_display_quantity),
            'unit' => $unit_key,
        ];

        if (!isset($product_options[$product_key]) || !isset($unit_options[$unit_key]) || $item_quantity <= 0) {
            continue;
        }

        $line_key = $product_key . '|' . $unit_key;
        if (!isset($item_totals[$line_key])) {
            $item_totals[$line_key] = [
                'product' => $product_key,
                'unit' => $unit_key,
                'quantity' => 0.0,
            ];
        }
        $item_totals[$line_key]['quantity'] += $item_quantity;
        $total_quantity += $item_quantity;
    }

    if (count($order_item_inputs) === 0) {
        $order_item_inputs[] = ['product' => $prefill_product, 'quantity' => '1', 'unit' => $prefill_unit];
    }

    if ($full_name_value === '' || $phone_value === '' || $address_value === '' || $delivery_date_value === '') {
        $message = '<div class="alert alert-danger">Please fill in all required fields.</div>';
    } elseif (count($item_totals) === 0 || $total_quantity <= 0) {
        $message = '<div class="alert alert-danger">Please add at least one valid product with quantity greater than zero.</div>';
    } else {
        $product_parts = [];
        foreach ($item_totals as $item_data) {
            $product_parts[] = $product_options[$item_data['product']] . ' x ' . format_quantity_value($item_data['quantity']) . ' ' . $unit_options[$item_data['unit']];
        }
        $product_summary = implode(', ', $product_parts);
        $total_quantity_value = round($total_quantity, 2);

        $col_res = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'quantity'");
        if ($col_res && mysqli_num_rows($col_res) > 0) {
            $col = mysqli_fetch_assoc($col_res);
            $col_type = strtolower((string) ($col['Type'] ?? ''));
            if (strpos($col_type, 'decimal') === false && strpos($col_type, 'float') === false && strpos($col_type, 'double') === false) {
                mysqli_query($conn, "ALTER TABLE orders MODIFY COLUMN quantity DECIMAL(10,2) NOT NULL");
            }
        }

        $user_id = (int) $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO orders (user_id, full_name, phone, product, quantity, delivery_address, delivery_date)
                VALUES (?, ?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param('isssdss', $user_id, $full_name_value, $phone_value, $product_summary, $total_quantity_value, $address_value, $delivery_date_value);
            if ($stmt->execute()) {
                $order_placed = true;
                $order_id = mysqli_insert_id($conn);

                $fetch_sql = "SELECT o.*, u.email FROM orders o JOIN users u ON o.user_id = u.user_id WHERE o.id = '$order_id'";
                $fetch_result = mysqli_query($conn, $fetch_sql);
                if ($fetch_result && mysqli_num_rows($fetch_result) > 0) {
                    $order_details = mysqli_fetch_assoc($fetch_result);
                }
            } else {
                $message = '<div class="alert alert-danger">Error: ' . htmlspecialchars($stmt->error) . '</div>';
            }
            $stmt->close();
        } else {
            $message = '<div class="alert alert-danger">Could not prepare order request.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place an Order | Kalungu Quality Feeds</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="joy.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
    </style>
</head>
<body>
<?php include 'header.php'; ?>    
    <!-- Order Hero Section -->
    <section class="order-hero">
        <div class="container">
            <h1><i class="fas fa-shopping-cart me-2"></i>Place Your Order</h1>
            <p class="lead mb-0">Quality products, reliable delivery, just for you</p>
        </div>
    </section>

    <!-- Main Order Section -->
    <section class="py-4">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Place Order</li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-lg-8">
                    <?php if ($order_placed && !empty($order_details)): ?>
                        <!-- Order Confirmation Section -->
                        <section class="order-section">
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Success!</strong> Your order has been placed successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>

                            <h2><i class="fas fa-receipt me-2"></i>Order Confirmation</h2>

                            <div class="confirmation-card" style="background: #f8f9fa; padding: 30px; border-radius: 10px; border-left: 4px solid #28a745;">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h5 class="text-muted mb-3">Order Number</h5>
                                        <p style="font-size: 1.5rem; font-weight: bold; color: #28a745;">
                                            #<?php echo htmlspecialchars($order_details['id']); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5 class="text-muted mb-3">Order Status</h5>
                                        <p style="font-size: 1.1rem; font-weight: bold; color: #ffc107;">
                                            <i class="fas fa-hourglass-half"></i> Pending
                                        </p>
                                    </div>
                                </div>

                                <hr>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h5 class="text-muted mb-2"><i class="fas fa-user"></i> Full Name</h5>
                                        <p style="font-size: 1.1rem; font-weight: 500;">
                                            <?php echo htmlspecialchars($order_details['full_name']); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5 class="text-muted mb-2"><i class="fas fa-envelope"></i> Email</h5>
                                        <p style="font-size: 1.1rem; font-weight: 500;">
                                            <?php echo htmlspecialchars($order_details['email']); ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h5 class="text-muted mb-2"><i class="fas fa-phone"></i> Phone Number</h5>
                                        <p style="font-size: 1.1rem; font-weight: 500;">
                                            <?php echo htmlspecialchars($order_details['phone']); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5 class="text-muted mb-2"><i class="fas fa-calendar-alt"></i> Delivery Date</h5>
                                        <p style="font-size: 1.1rem; font-weight: 500;">
                                            <?php echo date('M d, Y', strtotime($order_details['delivery_date'])); ?>
                                        </p>
                                    </div>
                                </div>

                                <hr>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                            <h5 class="text-muted mb-2"><i class="fas fa-cube"></i> Products</h5>
                                        <p style="font-size: 1.1rem; font-weight: 500;">
                                            <?php echo htmlspecialchars($order_details['product']); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                            <h5 class="text-muted mb-2"><i class="fas fa-boxes"></i> Total Quantity</h5>
                                        <p style="font-size: 1.1rem; font-weight: 500;">
                                            <?php echo htmlspecialchars(format_quantity_value((float) $order_details['quantity'])); ?>
                                        </p>
                                    </div>
                                </div>

                                <hr>

                                <div class="mb-4">
                                    <h5 class="text-muted mb-2"><i class="fas fa-map-marker-alt"></i> Delivery Address</h5>
                                    <p style="font-size: 1.1rem; font-weight: 500;">
                                        <?php echo htmlspecialchars($order_details['delivery_address']); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4" style="display: flex; gap: 10px;">
                                <a href="my_orders.php" class="btn btn-success">
                                    <i class="fas fa-eye me-2"></i> View All My Orders
                                </a>
                                <a href="index.php" class="btn btn-outline-primary">
                                    <i class="fas fa-home me-2"></i> Back to Home
                                </a>
                            </div>
                        </section>
                    <?php else: ?>
                        <!-- Order Form Section -->
                        <section class="order-section">
                            <h2><i class="fas fa-list me-2"></i>Order Details</h2>
                            <?php echo $message; ?>
                        
                        <form id="order-form" method="POST" action="">
                            <!-- Full Name -->
                            <div class="form-group">
                                <label for="full-name"><i class="fas fa-user me-2"></i>Full Name</label>
                                <input type="text" id="full-name" name="full_name" placeholder="Enter your full name"
                                    value="<?php echo htmlspecialchars($full_name_value); ?>"
                                    required pattern="[A-Za-z\s]+"
                                    title="Name must contain only letters and spaces">
                            </div>

                            <!-- Phone Number -->
                            <div class="form-group">
                                <label for="phone"><i class="fas fa-phone me-2"></i>Phone Number</label>
                                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number"
                                    value="<?php echo htmlspecialchars($phone_value); ?>"
                                    required pattern="[0-9]+"
                                    title="Phone number must contain only digits">
                            </div>

                            <!-- Select Products -->
                            <div class="form-group">
                                <label><i class="fas fa-cube me-2"></i>Select Products</label>
                                <div id="product-rows">
                                    <?php foreach ($order_item_inputs as $item): ?>
                                        <div class="row g-2 align-items-end product-row mb-2">
                                            <div class="col-md-5">
                                                <select name="products[]" class="form-control" required>
                                                    <option value="">-- Choose a Product --</option>
                                                    <?php foreach ($product_options as $product_key => $product_label): ?>
                                                        <option value="<?php echo htmlspecialchars($product_key); ?>" <?php echo (($item['product'] ?? '') === $product_key) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($product_label); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" name="quantities[]" class="form-control" placeholder="Quantity" min="0.01" step="0.01" value="<?php echo htmlspecialchars((string) ($item['quantity'] ?? '1')); ?>" required>
                                            </div>
                                            <div class="col-md-3">
                                                <select name="units[]" class="form-control" required>
                                                    <?php foreach ($unit_options as $unit_key => $unit_label): ?>
                                                        <option value="<?php echo htmlspecialchars($unit_key); ?>" <?php echo (($item['unit'] ?? 'kg') === $unit_key) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($unit_label); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-product-row" title="Remove row">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" id="add-product-row" class="btn btn-outline-success btn-sm mt-2">
                                    <i class="fas fa-plus me-1"></i>Add Another Product
                                </button>
                                <small class="text-muted d-block mt-2">For feeds sold in Kg, you can enter decimals (example: 2.5 Kg).</small>
                            </div>

                            <!-- Delivery Address -->
                            <div class="form-group">
                                <label for="address"><i class="fas fa-map-marker-alt me-2"></i>Delivery Address</label>
                                <textarea name="address" id="address" placeholder="Enter your delivery address in detail" required><?php echo htmlspecialchars($address_value); ?></textarea>
                            </div>

                            <!-- Preferred Delivery Date -->
                            <div class="form-group">
                                <label for="delivery-date"><i class="fas fa-calendar me-2"></i>Preferred Delivery Date</label>
                                <input type="date" id="delivery-date" name="delivery_date" value="<?php echo htmlspecialchars($delivery_date_value); ?>" required>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit"><i class="fas fa-check me-2"></i>Submit Order</button>
                        </form>
                    </section>
                    <?php endif; ?>
                </div>

                <!-- Side Information -->
                <div class="col-lg-4 ">
                    <div class="info-box">
                        <h4><i class="fas fa-truck"></i>Fast Delivery</h4>
                        <p>We deliver within 24 hours to Kalungu and surrounding areas.</p>
                    </div>

                    <div class="info-box">
                        <h4><i class="fas fa-shield-alt"></i>Quality Assured</h4>
                        <p>All products are tested and certified for quality standards.</p>
                    </div>

                    <div class="info-box">
                        <h4><i class="fas fa-headset"></i>Customer Support</h4>
                        <p>Need help? <a href="contact.php" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">Contact us</a> anytime.</p>
                    </div>

                    <div class="info-box">
                        <h4><i class="fas fa-info-circle"></i>Order Tracking</h4>
                        <p>You'll receive a confirmation and tracking details via phone.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Extra safeguard for real-time input
        document.getElementById('full-name').addEventListener('input', function() {
            this.value = this.value.replace(/[^A-Za-z\s]/g, '');
        });

        document.getElementById('phone').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        const productRowsWrap = document.getElementById('product-rows');
        const addProductBtn = document.getElementById('add-product-row');

        function updateRemoveButtons() {
            const rows = productRowsWrap.querySelectorAll('.product-row');
            rows.forEach((row) => {
                const removeBtn = row.querySelector('.remove-product-row');
                if (removeBtn) {
                    removeBtn.disabled = rows.length === 1;
                }
            });
        }

        addProductBtn.addEventListener('click', function() {
            const baseRow = productRowsWrap.querySelector('.product-row');
            if (!baseRow) {
                return;
            }

            const clone = baseRow.cloneNode(true);
            const select = clone.querySelector('select[name="products[]"]');
            const qty = clone.querySelector('input[name="quantities[]"]');
            const unit = clone.querySelector('select[name="units[]"]');

            if (select) {
                select.value = '';
            }
            if (qty) {
                qty.value = '1';
            }
            if (unit) {
                unit.value = 'kg';
            }

            productRowsWrap.appendChild(clone);
            updateRemoveButtons();
        });

        productRowsWrap.addEventListener('change', function(event) {
            const productSelect = event.target.closest('select[name="products[]"]');
            if (!productSelect) {
                return;
            }

            const row = productSelect.closest('.product-row');
            if (!row) {
                return;
            }

            const unitSelect = row.querySelector('select[name="units[]"]');
            if (!unitSelect) {
                return;
            }

            const productKey = productSelect.value;
            if (['chicks', 'chicken-drinkers', 'brooder-heater'].includes(productKey)) {
                unitSelect.value = 'pieces';
            } else {
                unitSelect.value = 'kg';
            }
        });

        productRowsWrap.addEventListener('click', function(event) {
            const removeBtn = event.target.closest('.remove-product-row');
            if (!removeBtn) {
                return;
            }

            const rows = productRowsWrap.querySelectorAll('.product-row');
            if (rows.length <= 1) {
                return;
            }

            const row = removeBtn.closest('.product-row');
            if (row) {
                row.remove();
                updateRemoveButtons();
            }
        });

        updateRemoveButtons();
    </script>
    
    <?php include  'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>