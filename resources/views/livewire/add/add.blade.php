{{-- adding new member --}}
<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <x-form-section submit="AddNewMember">
    <x-slot name="title">
        {{ __('Add new member') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Add a new member to your team, allowing them to collaborate with you.') }}
    
    </x-slot>

    <x-slot name="form">
        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Name') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required  autocomplete="username" />
        </div>
        <!-- Password -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="password" value="{{ __('Password') }}" />
            <x-input id="password" type="password" class="mt-1 block w-full" wire:model="state.password" required autocomplete="new-password" />
            <x-input-error for="password" class="mt-2" />
        </div>
        <!-- Confirm Password -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
            <x-input id="password_confirmation" type="password" class="mt-1 block w-full" wire:model="state.password_confirmation" required autocomplete="new-password" />
        </div>

    </x-slot>


    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Email verification sent.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Add') }}
        </x-button>
    </x-slot>
</x-form-section>
</div>


