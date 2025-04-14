<x-tenant-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div class="mt-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('New Password (Leave blank to keep current password)')" />
                        <x-text-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-4">
                        <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                    type="password"
                                    name="password_confirmation" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Roles -->
                    <div class="mt-4">
                        <x-input-label :value="__('Roles')" />
                        <div class="mt-2 space-y-2">
                            <div class="flex items-center">
                                <input type="checkbox" id="role_admin" name="roles[]" value="admin" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array('admin', $user->getRoleNames()->toArray()) ? 'checked' : '' }}>
                                <label for="role_admin" class="ml-2 block text-sm text-gray-900">Admin</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="role_user" name="roles[]" value="user" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array('user', $user->getRoleNames()->toArray()) ? 'checked' : '' }}>
                                <label for="role_user" class="ml-2 block text-sm text-gray-900">User</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="role_manager" name="roles[]" value="manager" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array('manager', $user->getRoleNames()->toArray()) ? 'checked' : '' }}>
                                <label for="role_manager" class="ml-2 block text-sm text-gray-900">Manager</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="role_publisher" name="roles[]" value="publisher" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array('publisher', $user->getRoleNames()->toArray()) ? 'checked' : '' }}>
                                <label for="role_publisher" class="ml-2 block text-sm text-gray-900">Publisher</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="role_writer" name="roles[]" value="writer" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array('writer', $user->getRoleNames()->toArray()) ? 'checked' : '' }}>
                                <label for="role_writer" class="ml-2 block text-sm text-gray-900">Writer</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="role_hr" name="roles[]" value="hr" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array('hr', $user->getRoleNames()->toArray()) ? 'checked' : '' }}>
                                <label for="role_hr" class="ml-2 block text-sm text-gray-900">HR</label>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button class="ms-4">
                            {{ __('Update') }}
                        </x-primary-button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</x-tenant-app-layout> 