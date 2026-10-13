<section class="profile-danger-zone">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <details class="delete-account-details">
        <summary>{{ __('Delete Account') }}</summary>

        <form method="post" action="{{ route('profile.destroy') }}" class="delete-account-form">
            @csrf
            @method('delete')

            <p>{{ __('Please enter your password to confirm you would like to permanently delete your account.') }}</p>

            <div>
                <x-input-label for="delete_password" :value="__('Password')" />
                <x-text-input
                    id="delete_password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    autocomplete="current-password"
                    required
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <x-danger-button>{{ __('Permanently Delete Account') }}</x-danger-button>
        </form>
    </details>
</section>
