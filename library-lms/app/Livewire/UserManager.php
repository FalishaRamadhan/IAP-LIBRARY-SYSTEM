<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // <-- Import Auth

class UserManager extends Component
{
    use WithPagination;

    // Properties for User Form
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = User::ROLE_MEMBER; // Default to member
    public $editingUserId = null;
    public $search = '';

    // Authorization check: Only Admin can view this entire page
    public function mount()
    {
        if (Auth::check() && !Auth::user()->isAdmin()) {
            // Redirect non-admins to a less privileged page (e.g., Loan Desk)
            return redirect()->route('library.loans'); 
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingUserId)],
            'password' => $this->editingUserId ? 'nullable|string|min:6' : 'required|string|min:6',
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_MEMBER])],
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage(); 
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = User::ROLE_MEMBER; 
        $this->editingUserId = null;
    }

    public function save()
    {
        // AUTH CHECK: ONLY ADMIN CAN CREATE/UPDATE USERS
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            session()->flash('error', 'Unauthorized: Only Admins can manage member records.');
            return;
        }

        $validatedData = $this->validate();

        $data = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
        ];

        if (!empty($validatedData['password'])) {
            $data['password'] = Hash::make($validatedData['password']);
        }

        if ($this->editingUserId) {
            User::find($this->editingUserId)->update(array_filter($data));
            session()->flash('success', 'Member record updated successfully!');
        } else {
            User::create($data);
            session()->flash('success', 'New member registered successfully! Role: ' . $this->role);
        }

        $this->resetForm();
    }

    public function edit(User $user)
    {
        // AUTH CHECK: ONLY ADMIN CAN EDIT
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            session()->flash('error', 'Unauthorized: You do not have permission to edit records.');
            return;
        }

        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role; 
        $this->password = ''; 
    }

    public function delete(User $user)
    {
        // AUTH CHECK: ONLY ADMIN CAN DELETE
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            session()->flash('error', 'Unauthorized: Only Admins can delete member records.');
            return;
        }
        
        $user->delete();
        session()->flash('success', 'Member deleted successfully.');
    }

    public function render()
    {
        $users = User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orWhere('id', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.user-manager', [
            'users' => $users
        ])->layout('layouts.app');
    }
}