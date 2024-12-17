<?php
session_start();

if (empty($_SESSION['cart'])) {
    echo '<p>Your cart is empty.</p>';
} else {
    foreach ($_SESSION['cart'] as $item) {
        echo "<li>{$item['name']} - R {$item['price']}</li>";
    }
}
?>
