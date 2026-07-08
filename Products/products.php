<?php
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/data.php';

$selected_category = isset($_GET['category']) ? trim($_GET['category']) : 'all';
if (!isset($trx_product_categories[$selected_category])) {
    $selected_category = 'all';
}

$visible_products = [];
foreach ($trx_products as $id => $product) {
    if ($selected_category === 'all' || $product['category'] === $selected_category) {
        $visible_products[$id] = $product;
    }
}

trx_include_header('TeleRx Health Products');
trx_shop_styles();
?>
<main class="trx-shop-page">
    <section class="trx-shop-section trx-shop-section-soft">
        <div class="trx-shop-wrap">
            <div class="trx-shop-section-title">
                <div class="trx-shop-kicker">Product Store</div>
                <h2>Shop TeleRx <span>Products</span></h2>
                <p>These products are sample entries. Edit names, prices, images and stock from <strong>Products/data.php</strong>.</p>
            </div>

            <div class="trx-shop-toolbar">
                <div class="trx-shop-filter-group">
                    <?php foreach ($trx_product_categories as $key => $label): ?>
                        <a class="trx-shop-filter <?php echo $selected_category === $key ? 'active' : ''; ?>" href="<?php echo trx_shop_url('products.php?category=' . urlencode($key)); ?>">
                            <?php echo trx_html($label); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <a class="trx-shop-btn-sm trx-shop-btn-primary" href="<?php echo trx_shop_url('cart.php'); ?>">Cart: <?php echo trx_cart_count(); ?></a>
            </div>

            <?php if (empty($visible_products)): ?>
                <div class="trx-shop-empty trx-shop-table-card">
                    <h2>No products found</h2>
                    <p class="trx-shop-text">Try another category.</p>
                </div>
            <?php else: ?>
                <div class="trx-shop-grid">
                    <?php foreach ($visible_products as $id => $product): ?>
                        <article class="trx-shop-product-card">
                            <div class="trx-shop-product-media">
                                <span class="trx-shop-stock"><?php echo trx_html($product['stock']); ?></span>
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?php echo trx_file_url($product['image']); ?>" alt="<?php echo trx_html($product['name']); ?>">
                                <?php else: ?>
                                    <div class="trx-shop-product-placeholder"><?php echo trx_html($product['icon']); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="trx-shop-product-body">
                                <div class="trx-shop-product-category"><?php echo trx_html($trx_product_categories[$product['category']] ?? 'Product'); ?></div>
                                <h3><?php echo trx_html($product['name']); ?></h3>
                                <p><?php echo trx_html($product['short']); ?></p>
                                <div class="trx-shop-price-row">
                                    <span class="trx-shop-price"><?php echo trx_money(trx_product_price($product)); ?></span>
                                    <?php if (!empty($product['sale_price'])): ?>
                                        <span class="trx-shop-old-price"><?php echo trx_money($product['price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="trx-shop-product-actions">
                                    <a class="trx-shop-btn-sm trx-shop-btn-outline" href="<?php echo trx_shop_url('product-details.php?id=' . urlencode($id)); ?>">View Details</a>
                                    <a class="trx-shop-btn-sm trx-shop-btn-primary" href="<?php echo trx_shop_url('cart.php?action=add&id=' . urlencode($id) . '&qty=1'); ?>">Add to Cart</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="trx-shop-hero">
        <div class="trx-shop-wrap">
            <div class="trx-shop-hero-grid">
                <div>
                    <div class="trx-shop-kicker">TeleRx Health Products</div>
                    <h1>Essential health products delivered to your <span>home</span></h1>
                    <p>Order reliable health devices, home care items and basic medical supplies from TeleRx. Start with manual order confirmation, then deliver through your team.</p>
                    <div class="trx-shop-actions">
                        <a class="trx-shop-btn trx-shop-btn-primary" href="<?php echo trx_shop_url('products.php'); ?>">Shop Products</a>
                        <a class="trx-shop-btn trx-shop-btn-outline" href="<?php echo trx_shop_url('cart.php'); ?>">View Cart (<?php echo trx_cart_count(); ?>)</a>
                    </div>
                </div>
                <div class="trx-shop-card-soft">
                    <div class="trx-shop-stats">
                        <div class="trx-shop-stat"><strong>8</strong><span>Products</span></div>
                        <div class="trx-shop-stat"><strong>24-48h</strong><span>Dhaka Delivery</span></div>
                        <div class="trx-shop-stat"><strong>COD</strong><span>Available</span></div>
                    </div>
                    <p class="trx-shop-text" style="margin-top:18px;">Need help choosing a product? TeleRx team can call the customer and confirm the right item, delivery location and payment method before dispatch.</p>
                </div>
            </div>
        </div>
    </section>


    <section class="trx-shop-section">
        <div class="trx-shop-wrap">
            <div class="trx-shop-info-grid">
                <div class="trx-shop-info-card">
                    <h3>Manual Confirmation</h3>
                    <p>After order submission, your team can call the customer to confirm product, address and payment.</p>
                </div>
                <div class="trx-shop-info-card">
                    <h3>Cash on Delivery</h3>
                    <p>Start with COD and manual bKash/Nagad payment. Add payment gateway later when order volume grows.</p>
                </div>
                <div class="trx-shop-info-card">
                    <h3>Medical Note</h3>
                    <p>Health products support monitoring and care. They do not replace doctor consultation or emergency treatment.</p>
                </div>
            </div>
        </div>
    </section>
</main>
<?php trx_include_footer(); ?>
