<?php
function trx_detect_site_base_path() {
    $script_name = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_NAME']) : '';
    $dir = rtrim(str_replace('\\', '/', dirname($script_name)), '/');

    if (preg_match('#/Products$#i', $dir)) {
        $dir = substr($dir, 0, -9);
    }

    if ($dir === '' || $dir === '.') {
        return '/';
    }

    return '/' . trim($dir, '/') . '/';
}

function trx_site_url($path = '') {
    $base = trx_detect_site_base_path();
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function trx_shop_url($path = '') {
    return trx_site_url('Products/' . ltrim($path, '/'));
}

function trx_file_url($path = '') {
    return trx_shop_url($path);
}

if (session_status() === PHP_SESSION_NONE) {
    $cookie_path = rtrim(trx_detect_site_base_path(), '/');
    if ($cookie_path === '') {
        $cookie_path = '/';
    }

    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => $cookie_path,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        session_set_cookie_params(0, $cookie_path);
    }

    session_start();
}

function trx_html($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function trx_money($amount) {
    return '৳' . number_format((float)$amount, 0);
}

function trx_current_url_no_base($path = '') {
    return ltrim($path, '/');
}

function trx_include_header($title = 'TeleRx Health Products') {
    $header_path = __DIR__ . '/../header.php';

    if (file_exists($header_path)) {
        $page_title = $title;
        ob_start();
        include $header_path;
        $header = ob_get_clean();

        if (stripos($header, '<base ') === false && stripos($header, '<head') !== false) {
            $header = preg_replace('/<head([^>]*)>/i', '<head$1>' . "\n" . '<base href="' . trx_html(trx_site_url()) . '">', $header, 1);
        }

        echo $header;
        return;
    }

    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><base href="' . trx_html(trx_site_url()) . '"><title>' . trx_html($title) . '</title></head><body>';
}

function trx_include_footer() {
    $footer_path = __DIR__ . '/../footer.php';

    if (file_exists($footer_path)) {
        include $footer_path;
        return;
    }

    echo '</body></html>';
}

function trx_cart_cookie_path() {
    return '/';
}

function trx_cart_load() {
    $cart = [];

    if (!empty($_SESSION['trx_cart']) && is_array($_SESSION['trx_cart'])) {
        $cart = $_SESSION['trx_cart'];
    } elseif (!empty($_COOKIE['trx_cart'])) {
        $decoded = json_decode($_COOKIE['trx_cart'], true);
        if (is_array($decoded)) {
            $cart = $decoded;
        }
    }

    $clean = [];
    foreach ($cart as $id => $qty) {
        $id = trim((string)$id);
        $qty = (int)$qty;
        if ($id !== '' && $qty > 0) {
            $clean[$id] = min(20, $qty);
        }
    }

    $_SESSION['trx_cart'] = $clean;
    return $clean;
}

function trx_cart_save($cart) {
    $clean = [];
    foreach ($cart as $id => $qty) {
        $id = trim((string)$id);
        $qty = (int)$qty;
        if ($id !== '' && $qty > 0) {
            $clean[$id] = min(20, $qty);
        }
    }

    $_SESSION['trx_cart'] = $clean;

    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $value = json_encode($clean);

    if (PHP_VERSION_ID >= 70300) {
        setcookie('trx_cart', $value, [
            'expires' => time() + (86400 * 30),
            'path' => trx_cart_cookie_path(),
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        setcookie('trx_cart', $value, time() + (86400 * 30), trx_cart_cookie_path(), '', $secure, true);
    }

    $_COOKIE['trx_cart'] = $value;
}

function trx_cart_add($id, $qty, $products) {
    $id = trim((string)$id);
    $qty = max(1, (int)$qty);

    if (!isset($products[$id])) {
        return false;
    }

    $cart = trx_cart_load();
    $cart[$id] = min(20, ($cart[$id] ?? 0) + $qty);
    trx_cart_save($cart);
    return true;
}

function trx_cart_update($quantities, $products) {
    $cart = trx_cart_load();

    if (!is_array($quantities)) {
        return false;
    }

    foreach ($quantities as $id => $qty) {
        $id = trim((string)$id);
        if (!isset($products[$id])) {
            continue;
        }

        $qty = (int)$qty;
        if ($qty <= 0) {
            unset($cart[$id]);
        } else {
            $cart[$id] = min(20, $qty);
        }
    }

    trx_cart_save($cart);
    return true;
}

function trx_cart_remove($id) {
    $cart = trx_cart_load();
    unset($cart[$id]);
    trx_cart_save($cart);
}

function trx_cart_clear() {
    trx_cart_save([]);
}

function trx_cart_count() {
    $count = 0;
    foreach (trx_cart_load() as $qty) {
        $count += (int)$qty;
    }
    return $count;
}

function trx_get_cart_items($products) {
    $items = [];
    $subtotal = 0;
    $cart = trx_cart_load();

    if (empty($cart)) {
        return ['items' => [], 'subtotal' => 0, 'delivery' => 0, 'total' => 0];
    }

    foreach ($cart as $id => $qty) {
        if (!isset($products[$id])) {
            continue;
        }

        $qty = max(1, (int)$qty);
        $product = $products[$id];
        $price = trx_product_price($product);
        $line_total = $price * $qty;
        $subtotal += $line_total;

        $items[] = [
            'id' => $id,
            'product' => $product,
            'qty' => $qty,
            'price' => $price,
            'line_total' => $line_total,
        ];
    }

    $delivery = $subtotal > 0 ? 80 : 0;
    $total = $subtotal + $delivery;

    return ['items' => $items, 'subtotal' => $subtotal, 'delivery' => $delivery, 'total' => $total];
}

function trx_product_price($product) {
    if (isset($product['sale_price']) && $product['sale_price'] !== '' && $product['sale_price'] !== null) {
        return (float)$product['sale_price'];
    }
    return (float)$product['price'];
}

function trx_shop_styles() {
?>
<style>
    :root {
        --trx-shop-primary: #0E82FD;
        --trx-shop-primary-dark: #0867CA;
        --trx-shop-secondary: #00B894;
        --trx-shop-ink: #102033;
        --trx-shop-muted: #667085;
        --trx-shop-soft: #F4F8FF;
        --trx-shop-soft-green: #EAFBF5;
        --trx-shop-border: #E6EEF8;
        --trx-shop-white: #FFFFFF;
        --trx-shop-warning: #F6B31A;
        --trx-shop-danger: #EF4444;
        --trx-shop-shadow: 0 18px 45px rgba(16, 32, 51, 0.10);
        --trx-shop-radius: 22px;
    }

    .trx-shop-page,
    .trx-shop-page * {
        box-sizing: border-box;
    }

    .trx-shop-page {
        width: 100%;
        overflow-x: hidden;
        color: var(--trx-shop-ink);
        background: #fff;
        font-family: inherit;
    }

    .trx-shop-wrap {
        width: min(1180px, calc(100% - 32px));
        margin: 0 auto;
    }

    .trx-shop-hero {
        padding: 78px 0 58px;
        background:
            radial-gradient(circle at top left, rgba(14, 130, 253, 0.14), transparent 36%),
            linear-gradient(135deg, #F7FBFF 0%, #FFFFFF 55%, #EFFFFA 100%);
    }

    .trx-shop-hero-grid {
        display: grid;
        grid-template-columns: 1.1fr .9fr;
        gap: 34px;
        align-items: center;
    }

    .trx-shop-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        border-radius: 999px;
        background: rgba(14, 130, 253, 0.10);
        color: var(--trx-shop-primary-dark);
        font-size: 14px;
        font-weight: 800;
        margin-bottom: 16px;
    }

    .trx-shop-kicker::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--trx-shop-secondary);
    }

    .trx-shop-hero h1,
    .trx-shop-section-title h1,
    .trx-shop-section-title h2 {
        margin: 0 0 16px;
        color: var(--trx-shop-ink);
        line-height: 1.1;
        letter-spacing: -0.8px;
    }

    .trx-shop-hero h1 {
        font-size: clamp(36px, 5vw, 58px);
    }

    .trx-shop-section-title h1,
    .trx-shop-section-title h2 {
        font-size: clamp(30px, 4vw, 44px);
    }

    .trx-shop-hero h1 span,
    .trx-shop-section-title h1 span,
    .trx-shop-section-title h2 span {
        color: var(--trx-shop-primary);
    }

    .trx-shop-hero p,
    .trx-shop-section-title p,
    .trx-shop-text {
        margin: 0;
        color: var(--trx-shop-muted);
        font-size: 16px;
        line-height: 1.75;
    }

    .trx-shop-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 28px;
    }

    .trx-shop-btn,
    .trx-shop-btn-sm {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 0;
        cursor: pointer;
        text-decoration: none;
        font-weight: 800;
        border-radius: 999px;
        transition: .22s ease;
        white-space: nowrap;
        font-family: inherit;
    }

    .trx-shop-btn {
        min-height: 50px;
        padding: 13px 22px;
        font-size: 15px;
    }

    .trx-shop-btn-sm {
        min-height: 42px;
        padding: 10px 16px;
        font-size: 14px;
    }

    .trx-shop-btn-primary {
        color: #fff;
        background: linear-gradient(135deg, var(--trx-shop-primary), var(--trx-shop-secondary));
        box-shadow: 0 14px 30px rgba(14, 130, 253, 0.22);
    }

    .trx-shop-btn-primary:hover,
    .trx-shop-btn-secondary:hover,
    .trx-shop-btn-outline:hover {
        transform: translateY(-2px);
    }

    .trx-shop-btn-secondary {
        color: #fff;
        background: linear-gradient(135deg, #F6B31A, #FFC83D);
        box-shadow: 0 14px 30px rgba(246, 179, 26, 0.22);
    }

    .trx-shop-btn-outline {
        color: var(--trx-shop-primary-dark);
        background: #fff;
        border: 1px solid rgba(14, 130, 253, .18);
    }

    .trx-shop-btn-danger {
        color: #fff;
        background: var(--trx-shop-danger);
    }

    .trx-shop-card-soft {
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid var(--trx-shop-border);
        border-radius: 30px;
        box-shadow: var(--trx-shop-shadow);
        padding: 26px;
    }

    .trx-shop-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .trx-shop-stat {
        padding: 18px 14px;
        border-radius: 18px;
        background: var(--trx-shop-soft);
        text-align: center;
    }

    .trx-shop-stat strong {
        display: block;
        color: var(--trx-shop-primary);
        font-size: 26px;
        line-height: 1;
        margin-bottom: 7px;
    }

    .trx-shop-stat span {
        color: var(--trx-shop-muted);
        font-weight: 700;
        font-size: 13px;
    }

    .trx-shop-section {
        padding: 68px 0;
    }

    .trx-shop-section-soft {
        background: linear-gradient(180deg, #fff 0%, #F7FBFF 100%);
    }

    .trx-shop-section-title {
        text-align: center;
        max-width: 760px;
        margin: 0 auto 34px;
    }

    .trx-shop-toolbar {
        display: flex;
        gap: 12px;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 24px;
        padding: 16px;
        background: #fff;
        border: 1px solid var(--trx-shop-border);
        border-radius: 18px;
        box-shadow: 0 12px 28px rgba(16, 32, 51, 0.06);
    }

    .trx-shop-filter-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .trx-shop-filter {
        border: 1px solid var(--trx-shop-border);
        background: var(--trx-shop-soft);
        color: var(--trx-shop-ink);
        padding: 10px 15px;
        border-radius: 999px;
        font-weight: 800;
        text-decoration: none;
        transition: .22s ease;
    }

    .trx-shop-filter.active,
    .trx-shop-filter:hover {
        color: #fff;
        background: linear-gradient(135deg, var(--trx-shop-primary), var(--trx-shop-secondary));
        border-color: transparent;
    }

    .trx-shop-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 22px;
    }

    .trx-shop-product-card {
        display: flex;
        flex-direction: column;
        background: #fff;
        border: 1px solid var(--trx-shop-border);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 16px 36px rgba(16, 32, 51, 0.08);
        min-height: 100%;
    }

    .trx-shop-product-media {
        min-height: 188px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #EAF4FF, #EAFBF5);
        padding: 18px;
        position: relative;
    }

    .trx-shop-product-media img {
        width: 100%;
        height: 170px;
        object-fit: contain;
    }

    .trx-shop-product-placeholder {
        width: 98px;
        height: 98px;
        border-radius: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        color: var(--trx-shop-primary);
        font-size: 42px;
        font-weight: 900;
        box-shadow: 0 12px 26px rgba(14, 130, 253, .12);
    }

    .trx-shop-stock {
        position: absolute;
        top: 14px;
        left: 14px;
        border-radius: 999px;
        padding: 7px 11px;
        background: rgba(0, 184, 148, .12);
        color: #047857;
        font-size: 12px;
        font-weight: 900;
    }

    .trx-shop-product-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .trx-shop-product-category {
        color: var(--trx-shop-primary-dark);
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .6px;
        margin-bottom: 8px;
    }

    .trx-shop-product-body h3 {
        margin: 0 0 8px;
        font-size: 19px;
        line-height: 1.3;
    }

    .trx-shop-product-body p {
        margin: 0;
        color: var(--trx-shop-muted);
        line-height: 1.65;
        font-size: 14px;
    }

    .trx-shop-price-row {
        display: flex;
        align-items: baseline;
        gap: 10px;
        flex-wrap: wrap;
        margin: 16px 0;
    }

    .trx-shop-price {
        color: var(--trx-shop-primary);
        font-size: 24px;
        font-weight: 900;
    }

    .trx-shop-old-price {
        color: #98A2B3;
        text-decoration: line-through;
        font-weight: 700;
    }

    .trx-shop-product-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: auto;
    }

    .trx-shop-form-inline {
        display: contents;
    }

    .trx-shop-detail-grid {
        display: grid;
        grid-template-columns: .9fr 1.1fr;
        gap: 34px;
        align-items: start;
    }

    .trx-shop-detail-media {
        border-radius: 30px;
        border: 1px solid var(--trx-shop-border);
        background: linear-gradient(135deg, #EAF4FF, #EAFBF5);
        min-height: 420px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px;
        box-shadow: var(--trx-shop-shadow);
    }

    .trx-shop-detail-media img {
        width: 100%;
        max-height: 360px;
        object-fit: contain;
    }

    .trx-shop-detail-title {
        margin: 0 0 12px;
        font-size: clamp(32px, 4vw, 48px);
        line-height: 1.1;
    }

    .trx-shop-list {
        display: grid;
        gap: 10px;
        margin: 20px 0 0;
        padding: 0;
        list-style: none;
    }

    .trx-shop-list li {
        display: flex;
        gap: 10px;
        color: #4D5E70;
        line-height: 1.6;
    }

    .trx-shop-list li::before {
        content: "✓";
        color: #22C55E;
        font-weight: 900;
        flex: 0 0 auto;
    }

    .trx-shop-info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-top: 30px;
    }

    .trx-shop-info-card {
        padding: 20px;
        border-radius: 20px;
        border: 1px solid var(--trx-shop-border);
        background: #fff;
    }

    .trx-shop-info-card h3 {
        margin: 0 0 10px;
        font-size: 18px;
    }

    .trx-shop-info-card p {
        margin: 0;
        color: var(--trx-shop-muted);
        line-height: 1.65;
        font-size: 14px;
    }

    .trx-shop-cart-layout {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 24px;
        align-items: start;
    }

    .trx-shop-table-card,
    .trx-shop-summary-card,
    .trx-shop-form-card,
    .trx-shop-policy-card {
        background: #fff;
        border: 1px solid var(--trx-shop-border);
        border-radius: 24px;
        box-shadow: 0 16px 36px rgba(16, 32, 51, 0.08);
        overflow: hidden;
    }

    .trx-shop-card-pad {
        padding: 24px;
    }

    .trx-shop-table-wrap {
        overflow-x: auto;
    }

    .trx-shop-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 720px;
    }

    .trx-shop-table th,
    .trx-shop-table td {
        padding: 16px;
        border-bottom: 1px solid var(--trx-shop-border);
        text-align: left;
        vertical-align: middle;
    }

    .trx-shop-table th {
        color: var(--trx-shop-ink);
        background: #F7FBFF;
        font-weight: 900;
        font-size: 14px;
    }

    .trx-shop-cart-product {
        display: flex;
        gap: 12px;
        align-items: center;
        min-width: 240px;
    }

    .trx-shop-cart-thumb {
        width: 58px;
        height: 58px;
        border-radius: 14px;
        background: linear-gradient(135deg, #EAF4FF, #EAFBF5);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--trx-shop-primary);
        font-weight: 900;
        overflow: hidden;
        flex: 0 0 auto;
    }

    .trx-shop-cart-thumb img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 6px;
    }

    .trx-shop-qty {
        width: 78px;
        border: 1px solid var(--trx-shop-border);
        border-radius: 12px;
        padding: 10px 12px;
    }

    .trx-shop-summary-card h3,
    .trx-shop-form-card h3,
    .trx-shop-policy-card h3 {
        margin: 0 0 18px;
        font-size: 22px;
    }

    .trx-shop-summary-line {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 11px 0;
        border-bottom: 1px solid var(--trx-shop-border);
        color: var(--trx-shop-muted);
    }

    .trx-shop-summary-line strong {
        color: var(--trx-shop-ink);
    }

    .trx-shop-total {
        font-size: 22px;
        color: var(--trx-shop-primary) !important;
        font-weight: 900;
    }

    .trx-shop-empty {
        text-align: center;
        padding: 50px 22px;
    }

    .trx-shop-empty h2 {
        margin: 0 0 10px;
    }

    .trx-shop-form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .trx-shop-field-full {
        grid-column: 1 / -1;
    }

    .trx-shop-label {
        display: block;
        margin-bottom: 7px;
        font-weight: 800;
        color: var(--trx-shop-ink);
    }

    .trx-shop-input,
    .trx-shop-select,
    .trx-shop-textarea {
        width: 100%;
        border: 1px solid var(--trx-shop-border);
        border-radius: 14px;
        padding: 13px 14px;
        font-family: inherit;
        outline: none;
        transition: .18s ease;
        background: #fff;
    }

    .trx-shop-input:focus,
    .trx-shop-select:focus,
    .trx-shop-textarea:focus {
        border-color: var(--trx-shop-primary);
        box-shadow: 0 0 0 4px rgba(14, 130, 253, .08);
    }

    .trx-shop-textarea {
        min-height: 110px;
        resize: vertical;
    }

    .trx-shop-alert {
        padding: 14px 16px;
        border-radius: 16px;
        margin-bottom: 18px;
        font-weight: 700;
        line-height: 1.6;
    }

    .trx-shop-alert-danger {
        color: #991B1B;
        background: #FEF2F2;
        border: 1px solid #FECACA;
    }

    .trx-shop-alert-success {
        color: #047857;
        background: #ECFDF5;
        border: 1px solid #A7F3D0;
    }

    .trx-shop-policy-card {
        padding: 28px;
        margin-bottom: 18px;
    }

    .trx-shop-policy-card ul,
    .trx-shop-policy-card ol {
        margin: 12px 0 0;
        color: var(--trx-shop-muted);
        line-height: 1.8;
    }

    .trx-shop-breadcrumb {
        margin-bottom: 18px;
        color: var(--trx-shop-muted);
        font-size: 14px;
        font-weight: 700;
    }

    .trx-shop-breadcrumb a {
        color: var(--trx-shop-primary-dark);
        text-decoration: none;
    }

    @media (max-width: 1199.98px) {
        .trx-shop-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    @media (max-width: 991.98px) {
        .trx-shop-hero-grid,
        .trx-shop-detail-grid,
        .trx-shop-cart-layout {
            grid-template-columns: 1fr;
        }
        .trx-shop-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .trx-shop-info-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 767.98px) {
        .trx-shop-wrap { width: min(100%, calc(100% - 24px)); }
        .trx-shop-hero { padding: 52px 0 40px; }
        .trx-shop-section { padding: 46px 0; }
        .trx-shop-grid { grid-template-columns: 1fr; }
        .trx-shop-product-actions { grid-template-columns: 1fr; }
        .trx-shop-form-grid { grid-template-columns: 1fr; }
        .trx-shop-stats { grid-template-columns: 1fr; }
        .trx-shop-toolbar { align-items: stretch; }
        .trx-shop-filter-group { width: 100%; }
        .trx-shop-filter { flex: 1 1 auto; text-align: center; }
    }
</style>
<?php
}
?>
