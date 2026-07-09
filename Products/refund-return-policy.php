<?php
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/data.php';
trx_include_header('Refund & Return Policy | TeleRx Products');
trx_shop_styles();
?>
<main class="trx-shop-page">
    <section class="trx-shop-section trx-shop-section-soft">
        <div class="trx-shop-wrap">
            <div class="trx-shop-section-title">
                <div class="trx-shop-kicker">TeleRx Products</div>
                <h1>Refund & Return <span>Policy</span></h1>
                <p>This policy is important for health devices, home care items and hygiene-sensitive products.</p>
            </div>

            <div class="trx-shop-policy-card">
                <h3>Return Eligibility</h3>
                <ul>
                    <li>Wrong product delivered.</li>
                    <li>Damaged product received.</li>
                    <li>Product is not working at the time of delivery.</li>
                    <li>Complaint must be reported within 24 hours of receiving the product.</li>
                </ul>
            </div>

            <div class="trx-shop-policy-card">
                <h3>Non-returnable Items</h3>
                <ul>
                    <li>Opened or used hygiene-sensitive medical items.</li>
                    <li>Adult diapers, personal care products and sealed supplies after opening.</li>
                    <li>Products damaged due to customer misuse.</li>
                    <li>Products without box, accessories or proof of purchase.</li>
                </ul>
            </div>

            <div class="trx-shop-policy-card">
                <h3>Refund Process</h3>
                <ul>
                    <li>TeleRx team will verify the complaint first.</li>
                    <li>If approved, refund or replacement will be processed within 5-7 working days.</li>
                    <li>Refund will be made through the original or agreed payment method.</li>
                </ul>
            </div>

            <div class="trx-shop-policy-card">
                <h3>Important Medical Note</h3>
                <p class="trx-shop-text">Products sold through TeleRx are for health support, monitoring or home care convenience. They are not a replacement for doctor consultation, diagnosis, emergency treatment or hospital care.</p>
            </div>

            <div class="trx-shop-actions">
                <a class="trx-shop-btn trx-shop-btn-primary" href="<?php echo trx_shop_url('products.php'); ?>">Back to Products</a>
                <a class="trx-shop-btn trx-shop-btn-outline" href="<?php echo trx_shop_url('delivery-policy.php'); ?>">Delivery Policy</a>
            </div>
        </div>
    </section>
</main>
<?php trx_include_footer(); ?>
