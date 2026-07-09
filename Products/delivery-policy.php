<?php
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/data.php';
trx_include_header('Delivery Policy | TeleRx Products');
trx_shop_styles();
?>
<main class="trx-shop-page">
    <section class="trx-shop-section trx-shop-section-soft">
        <div class="trx-shop-wrap">
            <div class="trx-shop-section-title">
                <div class="trx-shop-kicker">TeleRx Products</div>
                <h1>Delivery <span>Policy</span></h1>
                <p>Use this page for customer clarity before order confirmation.</p>
            </div>

            <div class="trx-shop-policy-card">
                <h3>Delivery Timeline</h3>
                <ul>
                    <li>Inside Dhaka: usually 24-48 hours after order confirmation.</li>
                    <li>Outside Dhaka: usually 3-5 working days depending on courier coverage.</li>
                    <li>Same-day delivery may be available for selected areas and urgent items.</li>
                </ul>
            </div>

            <div class="trx-shop-policy-card">
                <h3>Delivery Charge</h3>
                <ul>
                    <li>Inside Dhaka delivery charge starts from ৳80.</li>
                    <li>Outside Dhaka delivery charge depends on product size, weight and courier location.</li>
                    <li>TeleRx team will confirm the final delivery charge by phone before dispatch.</li>
                </ul>
            </div>

            <div class="trx-shop-policy-card">
                <h3>Order Confirmation</h3>
                <ul>
                    <li>After an order is placed, TeleRx team will call the customer for confirmation.</li>
                    <li>Customer must confirm product, quantity, address and payment method before dispatch.</li>
                    <li>Unconfirmed orders may be cancelled after repeated unsuccessful call attempts.</li>
                </ul>
            </div>

            <div class="trx-shop-policy-card">
                <h3>Payment Options</h3>
                <ul>
                    <li>Cash on Delivery</li>
                    <li>bKash manual payment</li>
                    <li>Nagad manual payment</li>
                    <li>Bank transfer</li>
                </ul>
            </div>

            <div class="trx-shop-actions">
                <a class="trx-shop-btn trx-shop-btn-primary" href="<?php echo trx_shop_url('products.php'); ?>">Shop Products</a>
                <a class="trx-shop-btn trx-shop-btn-outline" href="<?php echo trx_shop_url('refund-return-policy.php'); ?>">Refund & Return Policy</a>
            </div>
        </div>
    </section>
</main>
<?php trx_include_footer(); ?>
