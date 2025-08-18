<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Edit Item' : 'Add New Item'; ?> - Amal Store Admin</title>
    <?php wp_head(); ?>
</head>
<body class="amal-admin-body">
    <div class="amal-admin-container">
        <header class="amal-admin-header">
            <h1>🏪 Amal Store - <?php echo $is_edit ? 'Edit Item' : 'Add New Item'; ?></h1>
            <nav class="amal-admin-nav">
                <a href="<?php echo home_url('/admin/inventory/'); ?>" class="nav-link">← Back to Inventory</a>
                <a href="<?php echo home_url('/'); ?>" class="nav-link">← Back to Site</a>
                <a href="#" onclick="amal_logout()" class="nav-link">Logout</a>
            </nav>
        </header>

        <main class="amal-admin-main">
            <div class="form-container">
                <h2><?php echo $is_edit ? 'Edit Item' : 'Add New Item'; ?></h2>
                
                <form id="item-form" class="item-form">
                    <?php if ($is_edit): ?>
                    <input type="hidden" name="item_id" value="<?php echo $item->id; ?>">
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Title *</label>
                            <input type="text" id="title" name="title" 
                                   value="<?php echo $is_edit ? esc_attr($item->title) : ''; ?>" 
                                   required maxlength="255">
                            <small class="help-text">Product name (required, max 255 characters)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Category *</label>
                            <input type="text" id="category" name="category" 
                                   value="<?php echo $is_edit ? esc_attr($item->category) : ''; ?>" 
                                   required maxlength="100">
                            <small class="help-text">Product category (required, max 100 characters)</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4"><?php echo $is_edit ? esc_textarea($item->description) : ''; ?></textarea>
                        <small class="help-text">Detailed product description (optional)</small>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Price ($) *</label>
                            <input type="number" id="price" name="price" 
                                   value="<?php echo $is_edit ? $item->price : ''; ?>" 
                                   min="0" step="0.01" required>
                            <small class="help-text">Price in USD (required, minimum $0.00)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="stock_qty">Stock Quantity *</label>
                            <input type="number" id="stock_qty" name="stock_qty" 
                                   value="<?php echo $is_edit ? $item->stock_qty : '0'; ?>" 
                                   min="0" required>
                            <small class="help-text">Available stock (required, minimum 0)</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="image_url">Image URL</label>
                        <div class="image-input-container">
                            <input type="url" id="image_url" name="image_url" 
                                   value="<?php echo $is_edit ? esc_url($item->image_url) : ''; ?>" 
                                   maxlength="500">
                            <button type="button" id="preview-image-btn" class="btn btn-sm btn-secondary">Preview</button>
                        </div>
                        <small class="help-text">Valid image URL (optional, max 500 characters)</small>
                        
                        <div id="image-preview" class="image-preview" style="display: none;">
                            <img id="preview-img" src="" alt="Image preview">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="is_active" name="is_active" 
                                   <?php echo ($is_edit && $item->is_active) || (!$is_edit) ? 'checked' : ''; ?>>
                            <span class="checkmark"></span>
                            Active (item is available for sale)
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $is_edit ? '💾 Update Item' : '➕ Add Item'; ?>
                        </button>
                        <a href="<?php echo home_url('/admin/inventory/'); ?>" class="btn btn-secondary">Cancel</a>
                        <?php if ($is_edit): ?>
                        <button type="button" class="btn btn-danger btn-delete" data-item-id="<?php echo $item->id; ?>">
                            🗑️ Delete Item
                        </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('item-form');
        const previewBtn = document.getElementById('preview-image-btn');
        const imageInput = document.getElementById('image_url');
        const imagePreview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');

        // Form validation
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateForm()) {
                saveItem();
            }
        });

        // Image preview functionality
        previewBtn.addEventListener('click', function() {
            const imageUrl = imageInput.value.trim();
            if (imageUrl) {
                previewImg.src = imageUrl;
                imagePreview.style.display = 'block';
                
                previewImg.onerror = function() {
                    showMessage('error', 'Invalid image URL or image could not be loaded');
                    imagePreview.style.display = 'none';
                };
            } else {
                imagePreview.style.display = 'none';
            }
        });

        // Auto-preview when image URL changes
        imageInput.addEventListener('blur', function() {
            if (this.value.trim()) {
                previewBtn.click();
            }
        });

        // Delete functionality (for edit mode)
        const deleteBtn = document.querySelector('.btn-delete');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                if (confirm(amalStoreAdmin.messages.deleteConfirm)) {
                    const itemId = this.getAttribute('data-item-id');
                    deleteItem(itemId);
                }
            });
        }

        // Initial image preview (for edit mode)
        if (imageInput.value.trim()) {
            previewBtn.click();
        }
    });

    function validateForm() {
        const errors = [];
        
        // Title validation
        const title = document.getElementById('title').value.trim();
        if (!title) {
            errors.push('Title is required');
        } else if (title.length > 255) {
            errors.push('Title must be 255 characters or less');
        }

        // Category validation
        const category = document.getElementById('category').value.trim();
        if (!category) {
            errors.push('Category is required');
        } else if (category.length > 100) {
            errors.push('Category must be 100 characters or less');
        }

        // Price validation
        const price = parseFloat(document.getElementById('price').value);
        if (isNaN(price) || price < 0) {
            errors.push('Price must be a positive number');
        }

        // Stock quantity validation
        const stockQty = parseInt(document.getElementById('stock_qty').value);
        if (isNaN(stockQty) || stockQty < 0) {
            errors.push('Stock quantity must be a non-negative integer');
        }

        // Image URL validation
        const imageUrl = document.getElementById('image_url').value.trim();
        if (imageUrl && imageUrl.length > 500) {
            errors.push('Image URL must be 500 characters or less');
        }

        if (errors.length > 0) {
            showMessage('error', errors.join(', '));
            return false;
        }

        return true;
    }

    function saveItem() {
        const formData = new FormData(document.getElementById('item-form'));
        formData.append('action', 'amal_store_save_item');
        formData.append('nonce', amalStoreAdmin.nonce);

        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = '💾 Saving...';
        submitBtn.disabled = true;

        fetch(amalStoreAdmin.ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showMessage('success', result.data.message);
                
                // Redirect to inventory list after successful save
                setTimeout(() => {
                    window.location.href = '<?php echo home_url("/admin/inventory/?success=item_saved"); ?>';
                }, 1500);
            } else {
                showMessage('error', result.data.message);
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            showMessage('error', amalStoreAdmin.messages.error);
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    }

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
                showMessage('success', result.data.message);
                
                // Redirect to inventory list after successful deletion
                setTimeout(() => {
                    window.location.href = '<?php echo home_url("/admin/inventory/?success=item_deleted"); ?>';
                }, 1500);
            } else {
                showMessage('error', result.data.message);
            }
        })
        .catch(error => {
            showMessage('error', amalStoreAdmin.messages.error);
        });
    }

    function showMessage(type, message) {
        // Remove existing messages
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());

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