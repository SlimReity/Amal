<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo str_pad($order->id, 4, '0', STR_PAD_LEFT); ?> - Amal Store Admin</title>
    <?php wp_head(); ?>
</head>
<body class="amal-admin-body">
    <div class="amal-admin-container">
        <header class="amal-admin-header">
            <h1>🏪 Amal Store - Order Details</h1>
            <nav class="amal-admin-nav">
                <a href="<?php echo home_url('/admin/inventory/'); ?>" class="nav-link">Inventory</a>
                <a href="<?php echo home_url('/admin/orders/'); ?>" class="nav-link">← Back to Orders</a>
                <a href="<?php echo home_url('/'); ?>" class="nav-link">← Back to Site</a>
                <a href="#" onclick="amal_logout()" class="nav-link">Logout</a>
            </nav>
        </header>

        <main class="amal-admin-main">
            <div class="order-detail-header">
                <div class="order-title">
                    <h2>Order #<?php echo str_pad($order->id, 4, '0', STR_PAD_LEFT); ?></h2>
                    <div class="order-meta">
                        <span class="order-date">Created: <?php echo date('F j, Y \a\t g:i A', strtotime($order->created_at)); ?></span>
                        <?php if ($order->updated_at != $order->created_at): ?>
                        <span class="order-updated">Last Updated: <?php echo date('F j, Y \a\t g:i A', strtotime($order->updated_at)); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="order-status-section">
                    <div class="current-status">
                        <span class="status-badge status-<?php echo $order->status; ?>">
                            <?php echo ucfirst($order->status); ?>
                        </span>
                    </div>
                    <div class="status-update-form">
                        <select id="order-status-select" data-current-status="<?php echo $order->status; ?>">
                            <option value="pending" <?php echo ($order->status === 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo ($order->status === 'processing') ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?php echo ($order->status === 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo ($order->status === 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo ($order->status === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        <button type="button" id="update-status-btn" class="btn btn-primary">
                            Update Status
                        </button>
                    </div>
                </div>
            </div>

            <div class="order-detail-content">
                <div class="customer-section">
                    <h3>📋 Customer Information</h3>
                    <div class="info-card">
                        <div class="info-row">
                            <label>Name:</label>
                            <span><?php echo esc_html($order->first_name . ' ' . $order->last_name); ?></span>
                        </div>
                        <div class="info-row">
                            <label>Email:</label>
                            <span><?php echo esc_html($order->email); ?></span>
                        </div>
                        <div class="info-row">
                            <label>Username:</label>
                            <span><?php echo esc_html($order->username); ?></span>
                        </div>
                        <div class="info-row">
                            <label>Customer ID:</label>
                            <span>#<?php echo str_pad($order->user_id, 4, '0', STR_PAD_LEFT); ?></span>
                        </div>
                    </div>
                </div>

                <div class="order-summary-section">
                    <h3>💰 Order Summary</h3>
                    <div class="info-card">
                        <div class="info-row">
                            <label>Order ID:</label>
                            <span>#<?php echo str_pad($order->id, 4, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        <div class="info-row">
                            <label>Total Amount:</label>
                            <span class="total-amount">$<?php echo number_format($order->total_price, 2); ?></span>
                        </div>
                        <div class="info-row">
                            <label>Status:</label>
                            <span class="status-badge status-<?php echo $order->status; ?>">
                                <?php echo ucfirst($order->status); ?>
                            </span>
                        </div>
                        <div class="info-row">
                            <label>Created:</label>
                            <span><?php echo date('F j, Y \a\t g:i A', strtotime($order->created_at)); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="order-items-section">
                <h3>🛒 Order Items</h3>
                <div class="items-table-container">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_items = 0;
                            $calculated_total = 0;
                            foreach ($order->items as $item): 
                                $subtotal = $item->price * $item->quantity;
                                $total_items += $item->quantity;
                                $calculated_total += $subtotal;
                            ?>
                            <tr>
                                <td>
                                    <div class="item-info">
                                        <?php if ($item->image_url): ?>
                                        <img src="<?php echo esc_url($item->image_url); ?>" alt="<?php echo esc_attr($item->title); ?>" class="item-image">
                                        <?php endif; ?>
                                        <div class="item-details">
                                            <strong><?php echo esc_html($item->title); ?></strong>
                                            <?php if ($item->item_id): ?>
                                            <small>Item ID: #<?php echo $item->item_id; ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo esc_html($item->category); ?></td>
                                <td>$<?php echo number_format($item->price, 2); ?></td>
                                <td><?php echo $item->quantity; ?></td>
                                <td><strong>$<?php echo number_format($subtotal, 2); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="3"><strong>Total</strong></td>
                                <td><strong><?php echo $total_items; ?> items</strong></td>
                                <td><strong>$<?php echo number_format($order->total_price, 2); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="order-actions-section">
                <div class="action-buttons">
                    <a href="<?php echo home_url('/admin/orders/'); ?>" class="btn btn-secondary">
                        ← Back to Orders
                    </a>
                    <button type="button" onclick="window.print()" class="btn btn-outline">
                        🖨️ Print Order
                    </button>
                </div>
            </div>
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

.nav-link:hover {
    background: rgba(255, 255, 255, 0.2);
}

.amal-admin-main {
    padding: 2rem;
}

.order-detail-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #eee;
}

.order-title h2 {
    margin: 0 0 0.5rem 0;
    color: #333;
    font-size: 2rem;
}

.order-meta {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.order-date, .order-updated {
    font-size: 0.9rem;
    color: #666;
}

.order-status-section {
    text-align: right;
}

.current-status {
    margin-bottom: 1rem;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: bold;
    text-transform: uppercase;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-processing { background: #cce5ff; color: #004085; }
.status-shipped { background: #d4edda; color: #155724; }
.status-delivered { background: #d1ecf1; color: #0c5460; }
.status-cancelled { background: #f8d7da; color: #721c24; }

.status-update-form {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.status-update-form select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.order-detail-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.customer-section, .order-summary-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
}

.customer-section h3, .order-summary-section h3, .order-items-section h3 {
    margin: 0 0 1rem 0;
    color: #555;
    font-size: 1.1rem;
}

.info-card {
    background: white;
    border-radius: 6px;
    padding: 1rem;
    border: 1px solid #e1e1e1;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f1f1;
}

.info-row:last-child {
    border-bottom: none;
}

.info-row label {
    font-weight: bold;
    color: #666;
}

.total-amount {
    font-size: 1.2rem;
    font-weight: bold;
    color: #28a745;
}

.order-items-section {
    margin-bottom: 2rem;
}

.items-table-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.items-table {
    width: 100%;
    border-collapse: collapse;
}

.items-table th {
    background: #f8f9fa;
    padding: 1rem;
    text-align: left;
    font-weight: bold;
    color: #555;
    border-bottom: 2px solid #e1e1e1;
}

.items-table td {
    padding: 1rem;
    border-bottom: 1px solid #f1f1f1;
}

.items-table tbody tr:hover {
    background: #f8f9fa;
}

.item-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.item-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #e1e1e1;
}

.item-details strong {
    display: block;
    margin-bottom: 0.25rem;
}

.item-details small {
    color: #666;
    font-size: 0.8rem;
}

.total-row {
    background: #f8f9fa;
    font-weight: bold;
}

.total-row td {
    border-bottom: none;
    border-top: 2px solid #dee2e6;
}

.order-actions-section {
    border-top: 2px solid #eee;
    padding-top: 1.5rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: flex-start;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary { background: #007cba; color: white; }
.btn-secondary { background: #6c757d; color: white; }
.btn-outline { background: white; color: #333; border: 1px solid #ddd; }

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
.alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }

/* Print styles */
@media print {
    .amal-admin-header,
    .order-status-section .status-update-form,
    .order-actions-section {
        display: none;
    }
    
    .amal-admin-container {
        max-width: none;
        box-shadow: none;
    }
    
    .order-detail-header {
        border-bottom: 2px solid #000;
    }
}

@media (max-width: 768px) {
    .order-detail-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .order-status-section {
        text-align: left;
    }
    
    .order-detail-content {
        grid-template-columns: 1fr;
    }
    
    .status-update-form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('order-status-select');
    const updateBtn = document.getElementById('update-status-btn');
    
    updateBtn.addEventListener('click', function() {
        const newStatus = statusSelect.value;
        const currentStatus = statusSelect.getAttribute('data-current-status');
        
        if (newStatus === currentStatus) {
            showMessage('info', 'Status is already set to ' + newStatus);
            return;
        }
        
        updateOrderStatus(<?php echo $order->id; ?>, newStatus);
    });
});

function updateOrderStatus(orderId, newStatus) {
    const data = new FormData();
    data.append('action', 'amal_store_update_order_status');
    data.append('order_id', orderId);
    data.append('status', newStatus);
    data.append('nonce', amalStoreAdmin.nonce);

    // Disable button during update
    const updateBtn = document.getElementById('update-status-btn');
    updateBtn.disabled = true;
    updateBtn.textContent = 'Updating...';

    fetch(amalStoreAdmin.ajaxUrl, {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showMessage('success', amalStoreAdmin.messages.statusUpdateSuccess);
            
            // Update all status badges on the page
            document.querySelectorAll('.status-badge').forEach(badge => {
                badge.className = `status-badge status-${newStatus}`;
                badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            });
            
            // Update the current status data attribute
            document.getElementById('order-status-select').setAttribute('data-current-status', newStatus);
            
            // Update the URL to show success
            const url = new URL(window.location);
            url.searchParams.set('success', 'status_updated');
            window.history.pushState({}, '', url);
        } else {
            showMessage('error', result.data.message);
        }
    })
    .catch(error => {
        showMessage('error', amalStoreAdmin.messages.error);
    })
    .finally(() => {
        // Re-enable button
        updateBtn.disabled = false;
        updateBtn.textContent = 'Update Status';
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