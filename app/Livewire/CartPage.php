<?php

namespace App\Livewire;

use Livewire\Component;
use App\Helpers\CartMangement;
use Livewire\Attributes\Title;
use App\Livewire\Partials\Navbar;
use Jantinnerezo\LivewireAlert\LivewireAlert;

#[Title('Cart Page - Rio')]
class CartPage extends Component
{
    use LivewireAlert;
    public $cart_items = [];
    public $grand_total;

    public function mount()
    {
        $this->cart_items = CartMangement::getCartItemsFromCookie();
        $this->grand_total = CartMangement::calculateGrandTotal($this->cart_items);
    }
    public function removeItem($product_id)
    {
        $this->cart_items = CartMangement::removeItemFromCart($product_id);
        $this->grand_total = CartMangement::calculateGrandTotal($this->cart_items);
        $this->dispatch('update-cart-count', total_count: count($this->cart_items))->to(Navbar::class);
        $this->alert('success', 'Product removed to the cart successfully!', [
            'position' => 'bottom-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function increaseQty($product_id)
    {
        $this->cart_items = CartMangement::incrementCartItemQuantity($product_id);
        $this->grand_total = CartMangement::calculateGrandTotal($this->cart_items);
    }

    public function decreaseQty($product_id)
    {
        $this->cart_items = CartMangement::decrementCartItemQuantity($product_id);
        $this->grand_total = CartMangement::calculateGrandTotal($this->cart_items);
    }
    public function render()
    {
        return view('livewire.cart-page');
    }
}
