<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

#[Title('Login Page - Rio')]
class LoginPage extends Component
{
    use LivewireAlert;
    public $email;
    public $password;


    public function login()
    {
        $this->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        if (!auth()->attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->alert('error', 'Invalid credentials!', [
                'position' => 'bottom-end',
                'timer' => 3000,
                'toast' => true,
            ]);
           // session()->flash('error', 'Invalid credentials');
            return;
        }
        return redirect()->intended()->with('success', 'Login successful');
    }
    public function render()
    {
        return view('livewire.auth.login-page');
    }
}
