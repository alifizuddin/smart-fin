<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateProfileInformationForm extends Component
{
    use WithFileUploads;

    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    /**
     * The new avatar for the user.
     *
     * @var mixed
     */
    public $photo;

    /**
     * The user instance.
     *
     * @var \App\Models\User
     */
    public User $user;

    /**
     * Prepare the component.
     *
     * @return void
     */
    public function mount()
    {
        $this->user = Auth::user();
        $this->state = $this->user->only(['name', 'email']);
    }

    /**
     * Update the user's profile information.
     *
     * @return void
     */
    public function updateProfileInformation()
    {
        $this->validate([
            'state.name' => ['required', 'string', 'max:255'],
            'state.email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($this->photo) {
            // Delete the old photo if it exists
            if ($this->user->profile_photo_path) {
                Storage::disk('public')->delete($this->user->profile_photo_path);
            }
            // Store the new photo and update the user's profile photo path
            $this->user->profile_photo_path = $this->photo->store('profile-photos', 'public');
        }

        $this->user->forceFill([
            'name' => $this->state['name'],
            'email' => $this->state['email'],
        ])->save();

        $this->dispatch('saved');

        // We refresh the page to ensure the new photo is displayed correctly everywhere.
        return $this->redirect(request()->header('Referer'), navigate: true);
    }
    
    /**
     * Send an email verification notification.
     *
     * @return void
     */
    public function sendVerification()
    {
        $this->user->sendEmailVerificationNotification();

        session()->flash('status', 'verification-link-sent');
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.profile.update-profile-information-form');
    }
}
