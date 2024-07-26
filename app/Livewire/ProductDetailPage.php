<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Helpers\CartMangement;
use App\Livewire\Partials\Navbar;
use Jantinnerezo\LivewireAlert\LivewireAlert;

#[Title('Product Detail Page - Rio')]
class ProductDetailPage extends Component
{
    use LivewireAlert;
    public $slug;
    public $qty = 1;
    public function mount($slug)
    {
        $this->slug = $slug;
    }

    public function increaseQty()
    {
        $this->qty++;
    }

    public function decreaseQty()
    {
        if ($this->qty > 1) {
            $this->qty--;
        }
    }
    public function addToCart($product_id)
    {
        $total_count = CartMangement::addItemToCartWitQty($product_id, $this->qty);
        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);
        $this->alert('success', 'Product added to the cart successfully!', [
            'position' => 'bottom-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function render()
    {
        return view('livewire.product-detail-page', [
            'product' => Product::where('slug', $this->slug)->firstOrFail()
        ]);
    }
}
