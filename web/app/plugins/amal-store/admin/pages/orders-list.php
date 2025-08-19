<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Amal Store Admin</title>
    <?php wp_head(); ?>
</head>
<body class="amal-admin-body">
    <div class="amal-admin-container">
        <header class="amal-admin-header">
            <h1>🏪 Amal Store - Order Management</h1>
            <nav class="amal-admin-nav">
                <a href="<?php echo home_url('/admin/inventory/'); ?>" class="nav-link">Inventory</a>
                <a href="<?php echo home_url('/admin/orders/'); ?>" class="nav-link active">Orders</a>
                <a href="<?php echo home_url('/'); ?>" class="nav-link">← Back to Site</a>
                <a href="#" onclick="amal_logout()" class="nav-link">Logout</a>
            </nav>
        </header>

        <main class="amal-admin-main">
            <div class="amal-admin-toolbar">
                <div class="toolbar-left">
                    <h2>Order Management</h2>
                    <div class="filters-box">
                        <select id="status-filter" onchange="applyFilters()">
                            <option value="">All Statuses</option>
                            <option value="pending" <?php echo ($status_filter === 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo ($status_filter === 'processing') ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?php echo ($status_filter === 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo ($status_filter === 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo ($status_filter === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        <input type="date" id="date-filter" value="<?php echo esc_attr($date_filter); ?>" onchange="applyFilters()">
                        <button type="button" onclick="clearFilters()" class="btn btn-secondary">Clear Filters</button>
                    </div>
                </div>
                <div class="toolbar-right">
                    <div class="stats-info">
                        <span>Total Orders: <?php echo $total_orders; ?></span>
                    </div>
                </div>
            </div>

            <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php 
                switch($_GET['error']) {
                    case 'order_not_found':
                        echo 'Order not found.';
                        break;
                    default:
                        echo 'An error occurred.';
                }
                ?>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php 
                switch($_GET['success']) {
                    case 'status_updated':
                        echo 'Order status updated successfully.';
                        break;
                    default:
                        echo 'Operation completed successfully.';
                }
                ?>
            </div>
            <?php endif; ?>

            <div class="orders-grid">
                <?php if (empty($orders)): ?>
                    <div class="no-orders">
                        <h3>No orders found</h3>
                        <p>No orders match your current filters.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                    <div class="order-card" data-order-id="<?php echo $order->id; ?>">
                        <div class="order-header">
                            <div class="order-id">
                                <strong>Order #<?php echo str_pad($order->id, 4, '0', STR_PAD_LEFT); ?></strong>
                                <span class="order-date"><?php echo date('M j, Y H:i', strtotime($order->created_at)); ?></span>
                            </div>
                            <div class="order-status">
                                <span class="status-badge status-<?php echo $order->status; ?>">
                                    <?php echo ucfirst($order->status); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="order-content">
                            <div class="customer-info">
                                <h4>Customer</h4>
                                <p><strong><?php echo esc_html($order->first_name . ' ' . $order->last_name); ?></strong></p>
                                <p><?php echo esc_html($order->email); ?></p>
                                <p>Username: <?php echo esc_html($order->username); ?></p>
                            </div>
                            
                            <div class="order-summary">
                                <h4>Order Details</h4>
                                <p><strong>Total: $<?php echo number_format($order->total_price, 2); ?></strong></p>
                                <p>Created: <?php echo date('M j, Y', strtotime($order->created_at)); ?></p>
                                <?php if ($order->updated_at != $order->created_at): ?>
                                <p>Updated: <?php echo date('M j, Y', strtotime($order->updated_at)); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="order-actions">
                            <a href="<?php echo home_url('/admin/orders/view?id=' . $order->id); ?>" class="btn btn-sm btn-primary">
                                👁️ View Details
                            </a>
                            <div class="status-update">
                                <select class="status-select" data-order-id="<?php echo $order->id; ?>" data-current-status="<?php echo $order->status; ?>">
                                    <option value="pending" <?php echo ($order->status === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo ($order->status === 'processing') ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo ($order->status === 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo ($order->status === 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo ($order->status === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-success btn-update-status" 
                                        data-order-id="<?php echo $order->id; ?>">
                                    ✅ Update
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($total_orders > $orders_per_page): ?>
            <div class="pagination">
                <?php
                $total_pages = ceil($total_orders / $orders_per_page);
                $current_page = $page;
                
                // Build query parameters for pagination links
                $query_params = array();
                if (!empty($status_filter)) $query_params['status'] = $status_filter;
                if (!empty($date_filter)) $query_params['date'] = $date_filter;
                
                for ($i = 1; $i <= $total_pages; $i++):
                    $query_params['page'] = $i;
                    $page_url = home_url('/admin/orders/?' . http_build_query($query_params));
                ?>
                    <a href="<?php echo $page_url; ?>" class="page-link <?php echo ($i === $current_page) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>

<style>
.amal-admin-body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    margin: 0;
    padding: 0;
    background: #f5f5f5;
    color: #333;
}

.amal-admin-container {
    max-width: 1200px;
    margin: 0 auto;
    background: white;
    min-height: 100vh;
}

.amal-admin-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.amal-admin-header h1 {
    margin: 0;
    font-size: 1.5rem;
}

.amal-admin-nav {
    display: flex;
    gap: 1rem;
}

.nav-link {
    color: white;
    text-decoration: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    transition: background 0.3s;
}

.nav-link:hover, .nav-link.active {
    background: rgba(255, 255, 255, 0.2);
}

.amal-admin-main {
    padding: 2rem;
}

.amal-admin-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #eee;
}

.toolbar-left h2 {
    margin: 0 0 1rem 0;
    color: #333;
}

.filters-box {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.filters-box select, .filters-box input {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.stats-info {
    font-weight: bold;
    color: #666;
}

.orders-grid {
    display: grid;
    gap: 1.5rem;
}

.order-card {
    border: 1px solid #e1e1e1;
    border-radius: 8px;
    padding: 1.5rem;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.order-id strong {
    font-size: 1.1rem;
    color: #333;
}

.order-date {
    display: block;
    font-size: 0.9rem;
    color: #666;
    margin-top: 0.25rem;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: bold;
    text-transform: uppercase;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-processing { background: #cce5ff; color: #004085; }
.status-shipped { background: #d4edda; color: #155724; }
.status-delivered { background: #d1ecf1; color: #0c5460; }
.status-cancelled { background: #f8d7da; color: #721c24; }

.order-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.customer-info h4, .order-summary h4 {
    margin: 0 0 0.5rem 0;
    color: #555;
}

.customer-info p, .order-summary p {
    margin: 0.25rem 0;
    font-size: 0.9rem;
}

.order-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.status-update {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.status-select {
    padding: 0.4rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.9rem;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s;
}

.btn-primary { background: #007cba; color: white; }
.btn-secondary { background: #6c757d; color: white; }
.btn-success { background: #28a745; color: white; }
.btn-sm { padding: 0.25rem 0.5rem; font-size: 0.8rem; }

.btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.alert {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f1aeb5; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #b8dabd; }

.no-orders {
    text-align: center;
    padding: 3rem;
    color: #666;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 2rem;
}

.page-link {
    padding: 0.5rem 1rem;
    border: 1px solid #ddd;
    text-decoration: none;
    color: #007cba;
    border-radius: 4px;
}

.page-link.active {
    background: #007cba;
    color: white;
}

.page-link:hover {
    background: #f8f9fa;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status update functionality
    document.querySelectorAll('.btn-update-status').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const selectElement = document.querySelector(`.status-select[data-order-id="${orderId}"]`);
            const newStatus = selectElement.value;
            const currentStatus = selectElement.getAttribute('data-current-status');
            
            if (newStatus === currentStatus) {
                showMessage('info', 'Status is already set to ' + newStatus);
                return;
            }
            
            updateOrderStatus(orderId, newStatus, selectElement);
        });
    });
});

function applyFilters() {
    const status = document.getElementById('status-filter').value;
    const date = document.getElementById('date-filter').value;
    
    const params = new URLSearchParams();
    if (status) params.append('status', status);
    if (date) params.append('date', date);
    
    const url = `<?php echo home_url('/admin/orders/'); ?>` + (params.toString() ? '?' + params.toString() : '');
    window.location.href = url;
}

function clearFilters() {
    window.location.href = '<?php echo home_url('/admin/orders/'); ?>';
}

function updateOrderStatus(orderId, newStatus, selectElement) {
    const data = new FormData();
    data.append('action', 'amal_store_update_order_status');
    data.append('order_id', orderId);
    data.append('status', newStatus);
    data.append('nonce', amalStoreAdmin.nonce);

    fetch(amalStoreAdmin.ajaxUrl, {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showMessage('success', amalStoreAdmin.messages.statusUpdateSuccess);
            
            // Update the visual status badge
            const orderCard = selectElement.closest('.order-card');
            const statusBadge = orderCard.querySelector('.status-badge');
            statusBadge.className = `status-badge status-${newStatus}`;
            statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            
            // Update the current status data attribute
            selectElement.setAttribute('data-current-status', newStatus);
        } else {
            showMessage('error', result.data.message);
        }
    })
    .catch(error => {
        showMessage('error', amalStoreAdmin.messages.error);
    });
}

function showMessage(type, message) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    const main = document.querySelector('.amal-admin-main');
    main.insertBefore(alert, main.firstChild);
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

function amal_logout() {
    if (confirm('Are you sure you want to logout?')) {
        // This would trigger logout functionality
        window.location.href = '<?php echo home_url('/logout'); ?>';
    }
}
</script>

</body>
</html>