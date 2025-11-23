<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    // Define rules for login form validation
    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|string',
    ];

    public function login()
    {
        $this->validate();

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            // Authentication successful
            session()->regenerate();
            session()->flash('success', 'Welcome back, ' . Auth::user()->name . '!');
            
            // Redirect to the main inventory page
            return redirect()->intended(route('library.books'));

        } else {
            // Authentication failed
            $this->addError('email', 'The provided credentials do not match our records.');
        }
    }

    public function render()
    {
        return view('livewire.login')
            ->layout('layouts.app');
    }
}