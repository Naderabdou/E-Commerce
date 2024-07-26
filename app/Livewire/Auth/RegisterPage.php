<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;
#[Title('Register Page - Rio')]
class RegisterPage extends Component
{
    public $name;
    public $email;
    public $password;

    public function save(){
        $this->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|max:255',
        ]);
       $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
        ]);

        auth()->login($user);

        return redirect()->intended();

    }
    public function render()
    {
        return view('livewire.auth.register-page');
    }
}
