TeleRx Products Module
======================

Upload this full Products folder into your TeleRx website root.

Main pages:
- Products/products.php
- Products/product-details.php
- Products/cart.php
- Products/checkout.php
- Products/order.php
- Products/delivery-policy.php
- Products/refund-return-policy.php

How to edit products:
Open Products/data.php and change product names, prices, descriptions and images.

How to add menu link:
Add this link in your header menu:
<a href="Products/products.php">Products</a>

Order storage:
Submitted orders are saved in:
Products/orders/orders.jsonl

Make sure Products/orders folder is writable by the server.
