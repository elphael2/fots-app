<?php

use PHPUnit\Framework\TestCase;

class Cart_Test extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testAddItemToCart()
    {
        $_SESSION['cart'] = [];

        // Simulate adding an item to the cart
        $itemId = 101;
        $quantity = 2;
        $_SESSION['cart'][$itemId] = $quantity;

        $this->assertArrayHasKey($itemId, $_SESSION['cart']);
        $this->assertEquals(2, $_SESSION['cart'][$itemId]);
    }

    public function testRemoveItemFromCart()
    {
        $_SESSION['cart'] = [101 => 2, 102 => 3];

        unset($_SESSION['cart'][101]);

        $this->assertArrayNotHasKey(101, $_SESSION['cart']);
    }

    public function testCartTotalCalculation()
    {
        // Mock database query for item prices
        $itemPrices = [
            101 => 10.00,
            102 => 20.00
        ];

        // Simulate cart contents
        $_SESSION['cart'] = [
            101 => 2, // 2 items of ID 101
            102 => 1  // 1 item of ID 102
        ];

        $total = 0;
        foreach ($_SESSION['cart'] as $itemId => $quantity) {
            $total += $itemPrices[$itemId] * $quantity;
        }

        $this->assertEquals(40.00, $total);
    }
}
