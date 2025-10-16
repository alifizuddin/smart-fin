{{-- resources/views/welcome.blade.php --}}
<x-guest-layout>
    {{-- This loads the login form from resources/views/livewire/pages/auth/login.blade.php --}}
    @livewire('pages.auth.login')
</x-guest-layout>