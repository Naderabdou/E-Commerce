<?php

namespace App\Helpers;

use App\Models\Product;
use Illuminate\Support\Facades\Cookie;

class CartMangement
{

    //add item to cart
    static public function addItemToCart($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();
        $existing_item = null;
        // Check if the product already exists in the cart
        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $existing_item = $key;
                break;
            }
        }

        if ($existing_item !== null) {
            // Update the quantity and total amount of the existing item
            $cart_items[$existing_item]['quantity']++;
            $cart_items[$existing_item]['total_amount'] = $cart_items[$existing_item]['quantity'] * $cart_items[$existing_item]['unit_amount'];
        } else {
            // Fetch product details from the database
            $product = Product::where('id', $product_id)->first(['id', 'price', 'name_en', 'slug', 'images']);
            if ($product) {
                // Add the new product to the cart items
                $cart_items[] = [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_amount' => $product->price,
                    'total_amount' => $product->price,
                    'name_en' => $product->name_en,
                    'slug' => $product->slug,
                    'images' => $product->images[0],
                ];
            }
        }
        // Update the cart items in the cookie
        self::addCartItemToCookie($cart_items);

        // Return the count of items in the cart
        return count($cart_items);
    }


    static public function addItemToCartWitQty($product_id, $qty)
    {
        $cart_items = self::getCartItemsFromCookie();
        $existing_item = null;
        // Check if the product already exists in the cart
        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $existing_item = $key;
                break;
            }
        }

        if ($existing_item !== null) {
            // Update the quantity and total amount of the existing item
            $cart_items[$existing_item]['quantity'] = $qty;
            $cart_items[$existing_item]['total_amount'] = $cart_items[$existing_item]['quantity'] * $cart_items[$existing_item]['unit_amount'];
        } else {
            // Fetch product details from the database
            $product = Product::where('id', $product_id)->first(['id', 'price', 'name_en', 'slug', 'images']);
            if ($product) {
                // Add the new product to the cart items
                $cart_items[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_amount' => $product->price,
                    'total_amount' => $product->price,
                    'name_en' => $product->name_en,
                    'slug' => $product->slug,
                    'images' => $product->images[0],
                ];
            }
        }
        // Update the cart items in the cookie
        self::addCartItemToCookie($cart_items);

        // Return the count of items in the cart
        return count($cart_items);
    }


    //remove item from cart.
    static public function removeItemFromCart($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();
        $existing_item = null;
        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                unset($cart_items[$key]);
            }
        }
        self::addCartItemToCookie($cart_items);
        return $cart_items;
    }


    //add cart item to cookie
    static public function addCartItemToCookie($cart_item)
    {
        Cookie::queue('cart_items', json_encode($cart_item), 60 * 24 * 30);
    }

    //clean cart item from cookie
    static public function cleanCartItems()
    {
        Cookie::queue(Cookie::forget('cart_items'));
    }

    //get all cart items from cookie
    static public function getCartItemsFromCookie()
    {
        $cart_items = json_decode(Cookie::get('cart_items'), true);
        return $cart_items ? $cart_items : [];
    }

    //increment cart item quantity
    static public function incrementCartItemQuantity($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();
        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $cart_items[$key]['quantity']++;
                $cart_items[$key]['total_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];
            }
        }
        self::addCartItemToCookie($cart_items);
        return $cart_items;
    }

    //decrement cart item quantity
    static public function decrementCartItemQuantity($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();
        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                if ($cart_items[$key]['quantity'] > 1) {
                    $cart_items[$key]['quantity']--;
                    $cart_items[$key]['total_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];
                    // if ($cart_items[$key]['quantity'] == 0) {
                    //     unset($cart_items[$key]);
                    // }
                }
            }
            self::addCartItemToCookie($cart_items);
            return $cart_items;
        }
    }
    //calculate grand total
    static public function calculateGrandTotal($items)
    {
        return array_sum(array_column($items, 'total_amount'));
    }
};
