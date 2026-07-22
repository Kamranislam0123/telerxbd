<?php
/**
 * TeleRx Home Page: Products Preview
 *
 * This section reads product information from Products/data.php.
 * Updating product names, prices or images there will update this preview.
 */

$trx_home_product_preview = (static function () {
    $data_file = __DIR__ . '/Products/data.php';
    $categories = [];
    $products = [];

    if (is_file($data_file)) {
        require $data_file;

        if (isset($trx_product_categories) && is_array($trx_product_categories)) {
            $categories = $trx_product_categories;
        }

        if (isset($trx_products) && is_array($trx_products)) {
            $products = $trx_products;
        }
    }

    if (!$products) {
        $categories = [
            'devices' => 'Health Devices',
            'home-care' => 'Home Care',
            'supplies' => 'Medical Supplies',
            'wellness' => 'Wellness',
        ];

        $products = [
            'digital-bp-machine' => [
                'id' => 'digital-bp-machine',
                'name' => 'Digital BP Machine',
                'category' => 'devices',
                'icon' => 'BP',
                'price' => 2400,
                'sale_price' => 2200,
                'stock' => 'In Stock',
                'image' => 'assets/products/Digital BP Machine.png',
                'short' => 'Easy blood pressure monitoring device for home use.',
            ],
            'glucometer-kit' => [
                'id' => 'glucometer-kit',
                'name' => 'Glucometer Starter Kit',
                'category' => 'devices',
                'icon' => 'G',
                'price' => 1850,
                'sale_price' => 1650,
                'stock' => 'In Stock',
                'image' => 'assets/products/Glucometer Starter Kit.png',
                'short' => 'Blood glucose monitoring kit for diabetic patients.',
            ],
            'pulse-oximeter' => [
                'id' => 'pulse-oximeter',
                'name' => 'Pulse Oximeter',
                'category' => 'devices',
                'icon' => 'O2',
                'price' => 1250,
                'sale_price' => 1050,
                'stock' => 'In Stock',
                'image' => 'assets/products/Pulse Oximeter.png',
                'short' => 'Finger pulse oximeter for SpO2 and pulse monitoring.',
            ],
            'infrared-thermometer' => [
                'id' => 'infrared-thermometer',
                'name' => 'Infrared Thermometer',
                'category' => 'devices',
                'icon' => 'C',
                'price' => 1450,
                'sale_price' => 1250,
                'stock' => 'In Stock',
                'image' => 'assets/products/Infrared Thermometer.png',
                'short' => 'Contactless thermometer for fast temperature checks.',
            ],
        ];
    }

    $selected = [];

    foreach ($products as $id => $product) {
        if (!empty($product['image'])) {
            $selected[$id] = $product;
        }

        if (count($selected) === 4) {
            break;
        }
    }

    if (count($selected) < 4) {
        foreach ($products as $id => $product) {
            if (!isset($selected[$id])) {
                $selected[$id] = $product;
            }

            if (count($selected) === 4) {
                break;
            }
        }
    }

    return [
        'categories' => $categories,
        'products' => $selected,
        'total' => count($products),
    ];
})();

$trx_product_escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
?>

<!-- TeleRx Home Page: Products Preview -->
<section class="trx-home-program trx-products-preview" id="home-products" aria-labelledby="home-products-title">
    <div class="container">
        <div class="trx-products-heading" data-aos="fade-up">
            <div>
                <span class="trx-products-kicker">
                    <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                    TeleRx Health Products
                </span>
                <h2 id="home-products-title">Trusted health essentials for everyday home care</h2>
                <p>
                    Explore useful health devices, home-care items, medical supplies and wellness
                    products with simple ordering and delivery support.
                </p>
            </div>

            <div class="trx-products-heading-note" aria-label="Product ordering benefits">
                <span><i class="fa-solid fa-phone-volume"></i> Order confirmation</span>
                <span><i class="fa-solid fa-truck-fast"></i> Delivery support</span>
                <span><i class="fa-solid fa-money-bill-wave"></i> Cash on delivery</span>
            </div>
        </div>

        <div class="trx-products-showcase" data-aos="fade-up" data-aos-delay="100">
            <aside class="trx-products-feature-panel">
                <div class="trx-products-feature-orbit" aria-hidden="true">
                    <span><i class="fa-solid fa-heart-pulse"></i></span>
                </div>

                <div class="trx-products-feature-content">
                    <span class="trx-products-eyebrow">Care made more convenient</span>
                    <h3>Choose the right product for monitoring, recovery and daily support</h3>
                    <p>
                        TeleRx combines practical healthcare products with guidance, order confirmation
                        and a clear path to professional medical support when needed.
                    </p>

                    <ul class="trx-products-feature-list">
                        <li><i class="fa-solid fa-circle-check"></i><span>Home monitoring devices</span></li>
                        <li><i class="fa-solid fa-circle-check"></i><span>Caregiver and patient essentials</span></li>
                        <li><i class="fa-solid fa-circle-check"></i><span>Easy product selection and ordering</span></li>
                    </ul>

                    <div class="trx-products-count-card">
                        <span class="trx-products-count-icon"><i class="fa-solid fa-box-open"></i></span>
                        <div>
                            <strong><?php echo (int) $trx_home_product_preview['total']; ?>+</strong>
                            <small>Health products available</small>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="trx-products-grid" aria-label="Featured TeleRx products">
                <?php foreach ($trx_home_product_preview['products'] as $product_id => $product): ?>
                    <?php
                    $product_name = $product['name'] ?? 'Health Product';
                    $product_category_key = $product['category'] ?? '';
                    $product_category = $trx_home_product_preview['categories'][$product_category_key] ?? 'Health Product';
                    $product_stock = $product['stock'] ?? 'Available';
                    $product_image = trim((string) ($product['image'] ?? ''));
                    $product_icon = $product['icon'] ?? 'Rx';
                    $product_price = !empty($product['sale_price']) ? $product['sale_price'] : ($product['price'] ?? 0);
                    $product_old_price = !empty($product['sale_price']) ? ($product['price'] ?? 0) : 0;
                    $details_url = 'Products/product-details.php?id=' . rawurlencode((string) $product_id);
                    ?>

                    <article class="trx-home-product-card">
                        <a class="trx-home-product-media" href="<?php echo $trx_product_escape($details_url); ?>" aria-label="View <?php echo $trx_product_escape($product_name); ?> details">
                            <span class="trx-home-product-stock">
                                <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                                <?php echo $trx_product_escape($product_stock); ?>
                            </span>

                            <?php if ($product_image !== ''): ?>
                                <img
                                    src="<?php echo $trx_product_escape('Products/' . ltrim($product_image, '/')); ?>"
                                    alt="<?php echo $trx_product_escape($product_name); ?>"
                                    loading="lazy"
                                >
                            <?php else: ?>
                                <span class="trx-home-product-placeholder"><?php echo $trx_product_escape($product_icon); ?></span>
                            <?php endif; ?>
                        </a>

                        <div class="trx-home-product-body">
                            <span class="trx-home-product-category"><?php echo $trx_product_escape($product_category); ?></span>
                            <h3>
                                <a href="<?php echo $trx_product_escape($details_url); ?>">
                                    <?php echo $trx_product_escape($product_name); ?>
                                </a>
                            </h3>
                            <p><?php echo $trx_product_escape($product['short'] ?? 'Practical healthcare support for home use.'); ?></p>

                            <div class="trx-home-product-footer">
                                <div class="trx-home-product-price">
                                    <strong>৳<?php echo number_format((float) $product_price); ?></strong>
                                    <?php if ($product_old_price): ?>
                                        <del>৳<?php echo number_format((float) $product_old_price); ?></del>
                                    <?php endif; ?>
                                </div>

                                <a class="trx-home-product-arrow" href="<?php echo $trx_product_escape($details_url); ?>" aria-label="Open <?php echo $trx_product_escape($product_name); ?>">
                                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="trx-products-bottom" data-aos="fade-up">
            <p>
                Health products support monitoring and care. They do not replace doctor consultation
                or emergency treatment.
            </p>

            <a class="trx-products-main-link" href="Products/products.php">
                View more Product details
                <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</section>
