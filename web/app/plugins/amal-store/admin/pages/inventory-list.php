<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Amal Store Admin</title>
    <?php wp_head(); ?>
</head>
<body class="amal-admin-body">
    <div class="amal-admin-container">
        <header class="amal-admin-header">
            <h1>🏪 Amal Store - Inventory Management</h1>
            <nav class="amal-admin-nav">
                <a href="<?php echo home_url('/admin/inventory/'); ?>" class="nav-link active">Inventory</a>
                <a href="<?php echo home_url('/'); ?>" class="nav-link">← Back to Site</a>
                <a href="#" onclick="amal_logout()" class="nav-link">Logout</a>
            </nav>
        </header>

        <main class="amal-admin-main">
            <div class="amal-admin-toolbar">
                <div class="toolbar-left">
                    <h2>Inventory Items</h2>
                    <div class="search-box">
                        <input type="text" id="search-items" placeholder="Search items..." value="<?php echo esc_attr($_GET['search'] ?? ''); ?>">
                        <button type="button" id="search-btn">🔍</button>
                    </div>
                </div>
                <div class="toolbar-right">
                    <a href="<?php echo home_url('/admin/inventory/add'); ?>" class="btn btn-primary">
                        ➕ Add New Item
                    </a>
                </div>
            </div>

            <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php 
                switch($_GET['error']) {
                    case 'item_not_found':
                        echo 'Item not found.';
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
                    case 'item_saved':
                        echo 'Item saved successfully.';
                        break;
                    case 'item_deleted':
                        echo 'Item deleted successfully.';
                        break;
                    default:
                        echo 'Operation completed successfully.';
                }
                ?>
            </div>
            <?php endif; ?>

            <div class="items-grid">
                <?php if (empty($items)): ?>
                <div class="no-items">
                    <h3>No items found</h3>
                    <p>Start by adding your first inventory item.</p>
                    <a href="<?php echo home_url('/admin/inventory/add'); ?>" class="btn btn-primary">Add First Item</a>
                </div>
                <?php else: ?>
                <?php foreach ($items as $item): ?>
                <div class="item-card" data-item-id="<?php echo $item->id; ?>">
                    <div class="item-image">
                        <?php if (!empty($item->image_url)): ?>
                        <img src="<?php echo esc_url($item->image_url); ?>" alt="<?php echo esc_attr($item->title); ?>">
                        <?php else: ?>
                        <div class="no-image">📦</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="item-details">
                        <h3 class="item-title"><?php echo esc_html($item->title); ?></h3>
                        <p class="item-category"><?php echo esc_html($item->category); ?></p>
                        <p class="item-description"><?php echo esc_html(wp_trim_words($item->description, 15)); ?></p>
                        
                        <div class="item-meta">
                            <span class="item-price">$<?php echo number_format($item->price, 2); ?></span>
                            <span class="item-stock">Stock: <?php echo $item->stock_qty; ?></span>
                            <span class="item-status <?php echo $item->is_active ? 'active' : 'inactive'; ?>">
                                <?php echo $item->is_active ? '✅ Active' : '❌ Inactive'; ?>
                            </span>
                        </div>
                        
                        <div class="item-dates">
                            <small>Created: <?php echo date('M j, Y', strtotime($item->created_at)); ?></small>
                            <?php if ($item->updated_at != $item->created_at): ?>
                            <small>Updated: <?php echo date('M j, Y', strtotime($item->updated_at)); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="item-actions">
                        <a href="<?php echo home_url('/admin/inventory/edit?id=' . $item->id); ?>" class="btn btn-sm btn-secondary">
                            ✏️ Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-toggle" 
                                data-item-id="<?php echo $item->id; ?>"
                                data-current-status="<?php echo $item->is_active; ?>">
                            <?php echo $item->is_active ? '🔄 Deactivate' : '🔄 Activate'; ?>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-delete" 
                                data-item-id="<?php echo $item->id; ?>">
                            🗑️ Delete
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php
            // Pagination
            $search = $_GET['search'] ?? '';
            $total_items = $this->get_items_count($search);
            $items_per_page = 20;
            $total_pages = ceil($total_items / $items_per_page);
            $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            
            if ($total_pages > 1):
            ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                <a href="?page=<?php echo $current_page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-sm">← Previous</a>
                <?php endif; ?>
                
                <span class="page-info">Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>
                
                <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?php echo $current_page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-sm">Next →</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
    // Search functionality
    document.getElementById('search-btn').addEventListener('click', function() {
        const searchTerm = document.getElementById('search-items').value;
        const url = new URL(window.location.href);
        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        } else {
            url.searchParams.delete('search');
        }
        url.searchParams.delete('page');
        window.location.href = url.toString();
    });

    document.getElementById('search-items').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('search-btn').click();
        }
    });

    // Delete functionality
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm(amalStoreAdmin.messages.deleteConfirm)) {
                const itemId = this.getAttribute('data-item-id');
                deleteItem(itemId);
            }
        });
    });

    // Toggle status functionality
    document.querySelectorAll('.btn-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            const currentStatus = parseInt(this.getAttribute('data-current-status'));
            const newStatus = currentStatus ? 0 : 1;
            toggleItemStatus(itemId, newStatus);
        });
    });

    function deleteItem(itemId) {
        const data = new FormData();
        data.append('action', 'amal_store_delete_item');
        data.append('item_id', itemId);
        data.append('nonce', amalStoreAdmin.nonce);

        fetch(amalStoreAdmin.ajaxUrl, {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                document.querySelector(`[data-item-id="${itemId}"]`).remove();
                showMessage('success', result.data.message);
            } else {
                showMessage('error', result.data.message);
            }
        })
        .catch(error => {
            showMessage('error', amalStoreAdmin.messages.error);
        });
    }

    function toggleItemStatus(itemId, newStatus) {
        const data = new FormData();
        data.append('action', 'amal_store_toggle_item_status');
        data.append('item_id', itemId);
        data.append('is_active', newStatus);
        data.append('nonce', amalStoreAdmin.nonce);

        fetch(amalStoreAdmin.ajaxUrl, {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                location.reload(); // Refresh to show updated status
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
    </script>

    <?php wp_footer(); ?>
</body>
</html>