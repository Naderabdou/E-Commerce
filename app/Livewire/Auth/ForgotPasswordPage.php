<?php

namespace App\Livewire\Auth;

use Log;
use Livewire\Component;
use Illuminate\Support\Facades\Password;

#[Title('Forgot Password Page - Rio')]
class ForgotPasswordPage extends Component
{
    public $email;

    public function sendResetLink()
    {
        // Validate the email input
        $this->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        // Attempt to send the reset link
        $status = Password::sendResetLink(['email' => $this->email]);
dd($status);
        // Check the status and handle accordingly
        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('success', 'Password reset link has been sent to your email');
            $this->email = '';
        } else {
            dd($status);
            // Log the error status for debugging
            Log::error('Failed to send password reset link', ['status' => $status]);

            // Flash an error message to the session
            session()->flash('error', 'Failed to send password reset link. Please try again later.');
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password-page');
    }
}
