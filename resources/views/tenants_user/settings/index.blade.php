<x-tenant-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(auth()->user()->hasRole('hr'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Access Denied!</strong>
                            <span class="block sm:inline">You do not have permission to access this page.</span>
                        </div>
                    @else
                        <h3 class="text-lg font-medium mb-4">Role Permissions</h3>
                        
                        <div class="space-y-6">
                            @foreach($roles as $role)
                                <div class="border rounded-lg p-4">
                                    <h4 class="font-semibold text-lg mb-3">{{ $role->name }}</h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- View Users Permission -->
                                        <div class="flex items-center">
                                            <input type="checkbox" 
                                                   id="view-users-{{ $role->id }}" 
                                                   name="permissions[]" 
                                                   value="view-users"
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded permission-checkbox"
                                                   data-role-id="{{ $role->id }}"
                                                   {{ $role->hasPermissionTo('view-users') ? 'checked' : '' }}
                                                   @if(!auth()->user()->hasRole('admin')) disabled @endif>
                                            <label for="view-users-{{ $role->id }}" class="ml-2 block text-sm text-gray-900">
                                                View Users
                                            </label>
                                        </div>

                                        <!-- Create Users Permission -->
                                        <div class="flex items-center">
                                            <input type="checkbox" 
                                                   id="create-users-{{ $role->id }}" 
                                                   name="permissions[]" 
                                                   value="create-users"
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded permission-checkbox"
                                                   data-role-id="{{ $role->id }}"
                                                   {{ $role->hasPermissionTo('create-users') ? 'checked' : '' }}
                                                   @if(!auth()->user()->hasRole('admin')) disabled @endif>
                                            <label for="create-users-{{ $role->id }}" class="ml-2 block text-sm text-gray-900">
                                                Create Users
                                            </label>
                                        </div>

                                        <!-- Edit Users Permission -->
                                        <div class="flex items-center">
                                            <input type="checkbox" 
                                                   id="edit-users-{{ $role->id }}" 
                                                   name="permissions[]" 
                                                   value="edit-users"
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded permission-checkbox"
                                                   data-role-id="{{ $role->id }}"
                                                   {{ $role->hasPermissionTo('edit-users') ? 'checked' : '' }}
                                                   @if(!auth()->user()->hasRole('admin')) disabled @endif>
                                            <label for="edit-users-{{ $role->id }}" class="ml-2 block text-sm text-gray-900">
                                                Edit Users
                                            </label>
                                        </div>

                                        <!-- Delete Users Permission -->
                                        <div class="flex items-center">
                                            <input type="checkbox" 
                                                   id="delete-users-{{ $role->id }}" 
                                                   name="permissions[]" 
                                                   value="delete-users"
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded permission-checkbox"
                                                   data-role-id="{{ $role->id }}"
                                                   {{ $role->hasPermissionTo('delete-users') ? 'checked' : '' }}
                                                   @if(!auth()->user()->hasRole('admin')) disabled @endif>
                                            <label for="delete-users-{{ $role->id }}" class="ml-2 block text-sm text-gray-900">
                                                Delete Users
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if(!auth()->user()->hasRole('admin'))
                            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-yellow-800">You have view-only access to these settings. Only administrators can modify permissions.</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->hasRole('admin'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.permission-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const roleId = this.dataset.roleId;
                    const permission = this.value;
                    const isChecked = this.checked;
                    
                    // Get all checked permissions for this role
                    const permissions = Array.from(document.querySelectorAll(`.permission-checkbox[data-role-id="${roleId}"]:checked`))
                        .map(cb => cb.value);

                    // Send update request
                    fetch('{{ route("settings.update-permissions") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            role_id: roleId,
                            permissions: permissions
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message
                            });
                            // Revert checkbox state on error
                            this.checked = !isChecked;
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while updating permissions.'
                        });
                        // Revert checkbox state on error
                        this.checked = !isChecked;
                    });
                });
            });
        });
    </script>
    @endif
</x-tenant-app-layout> 