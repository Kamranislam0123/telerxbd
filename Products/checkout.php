<?php
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/data.php';

$cart = trx_get_cart_items($trx_products);
if (empty($cart['items'])) {
    header('Location: ' . trx_shop_url('cart.php'));
    exit;
}

trx_include_header('Checkout | TeleRx Products');
trx_shop_styles();
?>
<main class="trx-shop-page">
    <section class="trx-shop-section trx-shop-section-soft">
        <div class="trx-shop-wrap">
            <div class="trx-shop-section-title">
                <div class="trx-shop-kicker">Checkout</div>
                <h1>Complete Your <span>Order</span></h1>
                <p>Customer information will be saved locally in the Products/orders folder as a JSONL order record.</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="trx-shop-alert trx-shop-alert-danger"><?php echo trx_html($_GET['error']); ?></div>
            <?php endif; ?>

            <div class="trx-shop-cart-layout">
                <div class="trx-shop-form-card">
                    <div class="trx-shop-card-pad">
                        <h3>Customer Details</h3>
                        <form method="post" action="<?php echo trx_shop_url('order.php'); ?>">
                            <div class="trx-shop-form-grid">
                                <div>
                                    <label class="trx-shop-label" for="customer_name">Full Name *</label>
                                    <input class="trx-shop-input" id="customer_name" name="customer_name" required placeholder="Customer name">
                                </div>
                                <div>
                                    <label class="trx-shop-label" for="phone">Mobile Number *</label>
                                    <input class="trx-shop-input" id="phone" name="phone" required placeholder="01XXXXXXXXX">
                                </div>
                                <div>
                                    <label class="trx-shop-label" for="email">Email</label>
                                    <input class="trx-shop-input" id="email" name="email" type="email" placeholder="Optional">
                                </div>
                                <div>
                                    <label class="trx-shop-label" for="delivery_area">Delivery Area *</label>
                                    <select class="trx-shop-select" id="delivery_area" name="delivery_area" required>
                                        <option value="Inside Dhaka">Inside Dhaka</option>
                                        <option value="Outside Dhaka">Outside Dhaka</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="trx-shop-label" for="payment_method">Payment Method *</label>
                                    <select class="trx-shop-select" id="payment_method" name="payment_method" required>
                                        <option value="Cash on Delivery">Cash on Delivery</option>
                                        <option value="bKash Manual Payment">bKash Manual Payment</option>
                                        <option value="Nagad Manual Payment">Nagad Manual Payment</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="trx-shop-label" for="preferred_time">Preferred Call Time</label>
                                    <input class="trx-shop-input" id="preferred_time" name="preferred_time" placeholder="Example: 5 PM - 8 PM">
                                </div>
                                <div class="trx-shop-field-full">
                                    <label class="trx-shop-label" for="address">Delivery Address *</label>
                                    <textarea class="trx-shop-textarea" id="address" name="address" required placeholder="House, road, area, district"></textarea>
                                </div>
                                <div class="trx-shop-field-full">
                                    <label class="trx-shop-label" for="note">Order Note</label>
                                    <textarea class="trx-shop-textarea" id="note" name="note" placeholder="Product size, special instruction, patient need, etc."></textarea>
                                </div>
                            </div>
                            <div class="trx-shop-actions">
                                <button class="trx-shop-btn trx-shop-btn-primary" type="submit">Place Order</button>
                                <a class="trx-shop-btn trx-shop-btn-outline" href="<?php echo trx_shop_url('cart.php'); ?>">Back to Cart</a>
                            </div>
                        </form>
                    </div>
                </div>

                <aside class="trx-shop-summary-card">
                    <div class="trx-shop-card-pad">
                        <h3>Order Summary</h3>
                        <?php foreach ($cart['items'] as $item): ?>
                            <div class="trx-shop-summary-line">
                                <span><?php echo trx_html($item['product']['name']); ?> × <?php echo (int)$item['qty']; ?></span>
                                <strong><?php echo trx_money($item['line_total']); ?></strong>
                            </div>
                        <?php endforeach; ?>
                        <div class="trx-shop-summary-line"><span>Subtotal</span><strong><?php echo trx_money($cart['subtotal']); ?></strong></div>
                        <div class="trx-shop-summary-line"><span>Delivery Charge</span><strong><?php echo trx_money($cart['delivery']); ?></strong></div>
                        <div class="trx-shop-summary-line"><span>Total</span><strong class="trx-shop-total"><?php echo trx_money($cart['total']); ?></strong></div>
                        <p class="trx-shop-text" style="margin-top:16px;font-size:14px;">Your team can adjust delivery charge manually after calling the customer.</p>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</main>
<?php trx_include_footer(); ?>
