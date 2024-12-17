<?php
session_start(); // Start the session to access session variables
require_once "stripe-php-master/init.php";
require_once "config.php";
require_once "DBConn.php";

$stripe = new \Stripe\StripeClient(STRIPE_SECRET_KEY);

// Check if the cart session exists and is not empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    // Redirect to cart page if cart is empty
    header("Location: cart.php");
    exit;
}

// Display a welcome message with the username
echo '<p>Logged in as: <strong>' . htmlspecialchars($_SESSION['user_name']) . '</strong></p>';

// Example of cart details
$cartItems = $_SESSION['cart'];

// Calculate total amount
$totalAmount = array_sum(array_column($cartItems, 'price'));


// Create Stripe Checkout Session
$checkoutSession = $stripe->checkout->sessions->create([
    'payment_method_types' => ['card'],
    'line_items' => array_map(function ($item) {
        return [
            'price_data' => [
                'currency' => 'zar',
                'product_data' => [
                    'name' => $item['name'],
                ],
                'unit_amount' => $item['price'] * 100, // Convert to cents
            ],
            'quantity' => isset($item['quantity']) ? $item['quantity'] : 1, // Use quantity from cart item
        ];
    }, $cartItems),
    'mode' => 'payment',
    'success_url' => 'http://localhost/WEDE6021_POE%20Part2_ST10262964_&_ST10302040/pastimes/success.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'http://localhost/WEDE6021_POE%20Part2_ST10262964_&_ST10302040/pastimes/cancel.php',
]);

// Get the provider session ID from Stripe
$providerSessionID = $checkoutSession->id;


// Redirect to Stripe Checkout
header("Location: " . $checkoutSession->url);
exit;
