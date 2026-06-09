<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function poultry_cart_boot(): void
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function poultry_cart_get_items(): array
{
    poultry_cart_boot();
    return $_SESSION['cart'];
}

function poultry_cart_get_count(): int
{
    poultry_cart_boot();
    $count = 0;

    foreach ($_SESSION['cart'] as $quantity) {
        $count += max(0, (int) $quantity);
    }

    return $count;
}

function poultry_cart_add_item(string $slug, int $quantity = 1): void
{
    poultry_cart_boot();

    $slug = trim($slug);
    if ($slug === '') {
        return;
    }

    $quantity = max(1, $quantity);
    if (!isset($_SESSION['cart'][$slug])) {
        $_SESSION['cart'][$slug] = 0;
    }

    $_SESSION['cart'][$slug] += $quantity;
}

function poultry_cart_remove_item(string $slug): void
{
    poultry_cart_boot();

    $slug = trim($slug);
    if ($slug === '') {
        return;
    }

    unset($_SESSION['cart'][$slug]);
}

function poultry_cart_clear(): void
{
    poultry_cart_boot();
    $_SESSION['cart'] = [];
}
