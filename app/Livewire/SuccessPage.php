<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Stripe\Checkout\Session;
use Stripe\Stripe;

#[Title('Success Page - Rio')]
class SuccessPage extends Component
{
    #[Url]
    public $session_id;
    public function render()
    {
        $latest =  Order::with('address')->where('user_id', auth()->user()->id)->latest()->first();

        if ($this->session_id) {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $session_info = Session::retrieve($this->session_id);
            if ($session_info->payment_status !== 'paid') {
               $latest->payment_status = 'failed';
                $latest->save();
                return redirect()->route('cancel');
            } elseif ($session_info->payment_status === 'paid') {
                $latest->payment_status = 'paid';
                $latest->save();
                
            }
        }
        return view('livewire.success-page', [
            'order' => $latest
        ]);
    }
}
