<?php
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/data.php';

$id = isset($_GET['id']) ? trim($_GET['id']) : '';
if (!isset($trx_products[$id])) {
    header('Location: ' . trx_shop_url('products.php'));
    exit;
}
$product = $trx_products[$id];

trx_include_header($product['name'] . ' | TeleRx Products');
trx_shop_styles();
?>
<main class="trx-shop-page">
    <section class="trx-shop-section trx-shop-section-soft">
        <div class="trx-shop-wrap">
            <div class="trx-shop-breadcrumb">
                <a href="<?php echo trx_shop_url('products.php'); ?>">Products</a> / <?php echo trx_html($product['name']); ?>
            </div>

            <div class="trx-shop-detail-grid">
                <div class="trx-shop-detail-media">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?php echo trx_file_url($product['image']); ?>" alt="<?php echo trx_html($product['name']); ?>">
                    <?php else: ?>
                        <div class="trx-shop-product-placeholder" style="width:150px;height:150px;font-size:56px;">
                            <?php echo trx_html($product['icon']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="trx-shop-card-soft">
                    <div class="trx-shop-product-category"><?php echo trx_html($trx_product_categories[$product['category']] ?? 'Product'); ?></div>
                    <h1 class="trx-shop-detail-title"><?php echo trx_html($product['name']); ?></h1>
                    <p class="trx-shop-text"><?php echo trx_html($product['description']); ?></p>
                    <div class="trx-shop-price-row">
                        <span class="trx-shop-price" style="font-size:34px;"><?php echo trx_money(trx_product_price($product)); ?></span>
                        <?php if (!empty($product['sale_price'])): ?>
                            <span class="trx-shop-old-price"><?php echo trx_money($product['price']); ?></span>
                        <?php endif; ?>
                        <span class="trx-shop-stock" style="position:static;display:inline-flex;"><?php echo trx_html($product['stock']); ?></span>
                    </div>

                    <h3 style="margin:22px 0 0;">Key Benefits</h3>
                    <ul class="trx-shop-list">
                        <?php foreach ($product['features'] as $feature): ?>
                            <li><?php echo trx_html($feature); ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="trx-shop-alert trx-shop-alert-success" style="margin-top:22px;">
                        <strong>How to use:</strong> <?php echo trx_html($product['usage']); ?>
                    </div>

                    <form method="get" action="<?php echo trx_shop_url('cart.php'); ?>" style="margin-top:22px;">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="id" value="<?php echo trx_html($id); ?>">
                        <label class="trx-shop-label" for="qty">Quantity</label>
                        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
                            <input class="trx-shop-qty" id="qty" type="number" name="qty" value="1" min="1" max="20">
                            <button class="trx-shop-btn trx-shop-btn-primary" type="submit" style="width:auto;">Add to Cart</button>
                            <a class="trx-shop-btn trx-shop-btn-outline" href="<?php echo trx_shop_url('products.php'); ?>" style="width:auto;">Back to Products</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="trx-shop-info-grid">
                <div class="trx-shop-info-card">
                    <h3>Delivery</h3>
                    <p>Inside Dhaka delivery usually takes 24-48 hours after confirmation. Outside Dhaka depends on courier coverage.</p>
                </div>
                <div class="trx-shop-info-card">
                    <h3>Payment</h3>
                    <p>Cash on delivery, bKash, Nagad and bank transfer options can be selected during checkout.</p>
                </div>
                <div class="trx-shop-info-card">
                    <h3>Medical Advice</h3>
                    <p>This product is not a replacement for doctor consultation. For medical advice, consult a qualified doctor.</p>
                </div>
            </div>
        </div>
    </section>
</main>
<?php trx_include_footer(); ?>
