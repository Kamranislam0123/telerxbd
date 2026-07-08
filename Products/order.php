<?php
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/data.php';

$cart = trx_get_cart_items($trx_products);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($cart['items'])) {
    header('Location: ' . trx_shop_url('products.php'));
    exit;
}

function trx_clean_input($key) {
    return trim((string)($_POST[$key] ?? ''));
}

$customer_name = trx_clean_input('customer_name');
$phone = trx_clean_input('phone');
$email = trx_clean_input('email');
$delivery_area = trx_clean_input('delivery_area');
$payment_method = trx_clean_input('payment_method');
$preferred_time = trx_clean_input('preferred_time');
$address = trx_clean_input('address');
$note = trx_clean_input('note');

if ($customer_name === '' || $phone === '' || $address === '' || $delivery_area === '' || $payment_method === '') {
    header('Location: ' . trx_shop_url('checkout.php?error=' . urlencode('Please fill all required fields.')));
    exit;
}

$order_id = 'TRX-' . date('Ymd-His') . '-' . random_int(100, 999);
$order_items = [];
foreach ($cart['items'] as $item) {
    $order_items[] = [
        'id' => $item['id'],
        'name' => $item['product']['name'],
        'qty' => $item['qty'],
        'price' => $item['price'],
        'line_total' => $item['line_total'],
    ];
}

$order = [
    'order_id' => $order_id,
    'created_at' => date('Y-m-d H:i:s'),
    'customer' => [
        'name' => $customer_name,
        'phone' => $phone,
        'email' => $email,
        'delivery_area' => $delivery_area,
        'payment_method' => $payment_method,
        'preferred_time' => $preferred_time,
        'address' => $address,
        'note' => $note,
    ],
    'items' => $order_items,
    'subtotal' => $cart['subtotal'],
    'delivery' => $cart['delivery'],
    'total' => $cart['total'],
    'status' => 'Pending Confirmation',
];

$orders_dir = __DIR__ . '/orders';
if (!is_dir($orders_dir)) {
    @mkdir($orders_dir, 0755, true);
}
$orders_file = $orders_dir . '/orders.jsonl';
$save_ok = @file_put_contents($orders_file, json_encode($order, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);

$_SESSION['trx_last_order'] = $order;
trx_cart_clear();

trx_include_header('Order Placed | TeleRx Products');
trx_shop_styles();
?>
<main class="trx-shop-page">
    <section class="trx-shop-section trx-shop-section-soft">
        <div class="trx-shop-wrap">
            <div class="trx-shop-form-card">
                <div class="trx-shop-card-pad">
                    <div class="trx-shop-alert trx-shop-alert-success">
                        <strong>Order placed successfully.</strong><br>
                        Order ID: <?php echo trx_html($order_id); ?>. TeleRx team should call the customer to confirm the order.
                    </div>

                    <?php if (!$save_ok): ?>
                        <div class="trx-shop-alert trx-shop-alert-danger">
                            The order was shown successfully, but the server could not save it to <strong>Products/orders/orders.jsonl</strong>. Please check folder permission.
                        </div>
                    <?php endif; ?>

                    <div class="trx-shop-cart-layout">
                        <div>
                            <h3>Customer Information</h3>
                            <p class="trx-shop-text"><strong>Name:</strong> <?php echo trx_html($customer_name); ?></p>
                            <p class="trx-shop-text"><strong>Phone:</strong> <?php echo trx_html($phone); ?></p>
                            <?php if ($email !== ''): ?><p class="trx-shop-text"><strong>Email:</strong> <?php echo trx_html($email); ?></p><?php endif; ?>
                            <p class="trx-shop-text"><strong>Address:</strong> <?php echo nl2br(trx_html($address)); ?></p>
                            <p class="trx-shop-text"><strong>Payment:</strong> <?php echo trx_html($payment_method); ?></p>
                        </div>
                        <aside class="trx-shop-summary-card">
                            <div class="trx-shop-card-pad">
                                <h3>Order Summary</h3>
                                <?php foreach ($order_items as $item): ?>
                                    <div class="trx-shop-summary-line">
                                        <span><?php echo trx_html($item['name']); ?> × <?php echo (int)$item['qty']; ?></span>
                                        <strong><?php echo trx_money($item['line_total']); ?></strong>
                                    </div>
                                <?php endforeach; ?>
                                <div class="trx-shop-summary-line"><span>Subtotal</span><strong><?php echo trx_money($cart['subtotal']); ?></strong></div>
                                <div class="trx-shop-summary-line"><span>Delivery</span><strong><?php echo trx_money($cart['delivery']); ?></strong></div>
                                <div class="trx-shop-summary-line"><span>Total</span><strong class="trx-shop-total"><?php echo trx_money($cart['total']); ?></strong></div>
                            </div>
                        </aside>
                    </div>

                    <div class="trx-shop-actions">
                        <a class="trx-shop-btn trx-shop-btn-primary" href="<?php echo trx_shop_url('products.php'); ?>">Back to Products</a>
                        <a class="trx-shop-btn trx-shop-btn-outline" href="<?php echo trx_shop_url('delivery-policy.php'); ?>">Delivery Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php trx_include_footer(); ?>
