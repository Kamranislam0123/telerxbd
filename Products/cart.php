<?php
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/data.php';

$request = array_merge($_GET, $_POST);
$action = isset($request['action']) ? trim((string)$request['action']) : '';
$id = isset($request['id']) ? trim((string)$request['id']) : '';
$qty = isset($request['qty']) ? max(1, (int)$request['qty']) : 1;

if ($action !== '') {
    if ($action === 'add') {
        if (trx_cart_add($id, $qty, $trx_products)) {
            header('Location: ' . trx_shop_url('cart.php?added=1'));
        } else {
            header('Location: ' . trx_shop_url('products.php?cart_error=invalid_product'));
        }
        exit;
    }

    if ($action === 'update' && isset($_POST['quantities']) && is_array($_POST['quantities'])) {
        trx_cart_update($_POST['quantities'], $trx_products);
        header('Location: ' . trx_shop_url('cart.php?updated=1'));
        exit;
    }

    if ($action === 'remove') {
        trx_cart_remove($id);
        header('Location: ' . trx_shop_url('cart.php?removed=1'));
        exit;
    }

    if ($action === 'clear') {
        trx_cart_clear();
        header('Location: ' . trx_shop_url('cart.php?cleared=1'));
        exit;
    }
}

$cart = trx_get_cart_items($trx_products);
trx_include_header('Cart | TeleRx Products');
trx_shop_styles();
?>
<main class="trx-shop-page">
    <section class="trx-shop-section trx-shop-section-soft">
        <div class="trx-shop-wrap">
            <div class="trx-shop-section-title">
                <div class="trx-shop-kicker">Shopping Cart</div>
                <h1>Your <span>Cart</span></h1>
                <p>Review products before placing the order. Delivery charge is calculated as a basic default amount and can be changed later.</p>
            </div>

            <?php if (isset($_GET['added'])): ?><div class="trx-shop-alert trx-shop-alert-success">Product added to cart.</div><?php endif; ?>
            <?php if (isset($_GET['updated'])): ?><div class="trx-shop-alert trx-shop-alert-success">Cart updated successfully.</div><?php endif; ?>
            <?php if (isset($_GET['removed'])): ?><div class="trx-shop-alert trx-shop-alert-success">Product removed from cart.</div><?php endif; ?>

            <?php if (empty($cart['items'])): ?>
                <div class="trx-shop-empty trx-shop-table-card">
                    <h2>Your cart is empty</h2>
                    <p class="trx-shop-text">Add products first, then complete the order form.</p>
                    <div class="trx-shop-actions" style="justify-content:center;">
                        <a class="trx-shop-btn trx-shop-btn-primary" href="<?php echo trx_shop_url('products.php'); ?>">Browse Products</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="trx-shop-cart-layout">
                    <div class="trx-shop-table-card">
                        <form method="post" action="<?php echo trx_shop_url('cart.php'); ?>">
                            <input type="hidden" name="action" value="update">
                            <div class="trx-shop-table-wrap">
                                <table class="trx-shop-table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cart['items'] as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="trx-shop-cart-product">
                                                        <div class="trx-shop-cart-thumb">
                                                            <?php if (!empty($item['product']['image'])): ?>
                                                                <img src="<?php echo trx_file_url($item['product']['image']); ?>" alt="<?php echo trx_html($item['product']['name']); ?>">
                                                            <?php else: ?>
                                                                <?php echo trx_html($item['product']['icon']); ?>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div>
                                                            <strong><?php echo trx_html($item['product']['name']); ?></strong><br>
                                                            <small><?php echo trx_html($trx_product_categories[$item['product']['category']] ?? 'Product'); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo trx_money($item['price']); ?></td>
                                                <td><input class="trx-shop-qty" type="number" name="quantities[<?php echo trx_html($item['id']); ?>]" value="<?php echo (int)$item['qty']; ?>" min="0" max="20"></td>
                                                <td><strong><?php echo trx_money($item['line_total']); ?></strong></td>
                                                <td>
                                                    <button class="trx-shop-btn-sm trx-shop-btn-danger" type="submit" formaction="<?php echo trx_shop_url('cart.php'); ?>" formmethod="post" name="remove_action" onclick="event.preventDefault(); this.closest('tr').querySelector('input[type=number]').value=0; this.closest('form').submit();">Remove</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="trx-shop-card-pad" style="display:flex;gap:12px;flex-wrap:wrap;">
                                <button class="trx-shop-btn trx-shop-btn-primary" type="submit" style="width:auto;">Update Cart</button>
                                <a class="trx-shop-btn trx-shop-btn-outline" href="<?php echo trx_shop_url('products.php'); ?>" style="width:auto;">Continue Shopping</a>
                            </div>
                        </form>
                    </div>

                    <aside class="trx-shop-summary-card">
                        <div class="trx-shop-card-pad">
                            <h3>Order Summary</h3>
                            <div class="trx-shop-summary-line"><span>Subtotal</span><strong><?php echo trx_money($cart['subtotal']); ?></strong></div>
                            <div class="trx-shop-summary-line"><span>Delivery Charge</span><strong><?php echo trx_money($cart['delivery']); ?></strong></div>
                            <div class="trx-shop-summary-line"><span>Total</span><strong class="trx-shop-total"><?php echo trx_money($cart['total']); ?></strong></div>
                            <div class="trx-shop-actions" style="margin-top:22px;">
                                <a class="trx-shop-btn trx-shop-btn-primary" href="<?php echo trx_shop_url('checkout.php'); ?>">Proceed to Checkout</a>
                            </div>
                            <form method="post" action="<?php echo trx_shop_url('cart.php'); ?>" style="margin-top:12px;">
                                <input type="hidden" name="action" value="clear">
                                <button class="trx-shop-btn trx-shop-btn-outline" type="submit">Clear Cart</button>
                            </form>
                        </div>
                    </aside>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php trx_include_footer(); ?>
