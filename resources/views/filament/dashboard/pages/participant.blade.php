<x-filament-panels::page>
    @include('components.styles')
    <div class="mx-auto w-full max-w-7xl">
        <div class="card bg-white dark:bg-slate-800 shadow-sm mb-6">
            <div class="card-body items-center sm:items-start text-center sm:text-left">
                <div class="flex flex-col sm:flex-row items-center gap-4 justify-between w-full">

                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <div class="avatar avatar-placeholder">
                            <div class="bg-info text-neutral-content w-20 rounded-full">
                                <span class="text-3xl">{{ strtoupper(substr($profile['firstname'] ?? 'U', 0, 1) . substr($profile['lastname'] ?? 'S', 0, 1)) }}</span>
                            </div>
                        </div>

                        <div>
                            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2 justify-center sm:justify-start">
                                {{ trim(($profile['firstname'] ?? '-') . ' ' . ($profile['lastname'] ?? '')) }}
                                <div class="badge badge-soft badge-info badge-xs">Participant</div>
                            </h1>
                            <p class="text-sm text-slate-500">ID: {{ $profile['id'] ?? 'Belum tersedia' }}</p>
                        </div>
                    </div>

                    @if (! $isEditing)
                    <button type="button" wire:click="startEditing" class="btn btn-sm btn-info btn-soft">
                        <i class="fas fa-edit"></i>
                        Update Profile
                    </button>

                    @else
                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="cancelEditing" class="btn btn-sm btn-error btn-outline">
                            Cancel
                        </button>
                        <button type="button" wire:click="saveProfile" class="btn btn-sm btn-info">
                            Save Profile
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if (! $isEditing)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="space-y-6">
                <div class="card bg-white dark:bg-slate-800 shadow-sm">
                    <div class="card-body">

                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Account Information</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-400">User ID</label>
                                <span class="text-sm font-mono text-slate-700">{{ $profile['user_id'] ?? '-' }}</span>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400">Email</label>
                                <span class="text-sm font-medium text-slate-900 break-all">{{ $profile['email'] ?? '-' }}</span>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400">Phone Number</label>
                                <span class="text-sm font-medium text-slate-900">{{ $profile['phone'] ?: '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2 space-y-6">

                <div class="card shadow-sm bg-white dark:bg-slate-800">
                    <div class="card-body">
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">Personal Information</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-0.5">First Name</label>
                                <p class="text-sm font-medium text-slate-900">{{ $profile['firstname'] ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-0.5">Last Name</label>
                                <p class="text-sm font-medium text-slate-900">{{ $profile['lastname'] ?? '-' }}</p>
                            </div>
                            <div class="sm:col-span-2 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <label class="block text-xs font-medium text-slate-500 mb-0.5">Name on Certificate</label>
                                <p class="text-sm font-semibold text-indigo-900">{{ $profile['name_on_certificate'] ?: '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-0.5">NIK (National Identification Number)</label>
                                <p class="text-sm font-medium text-slate-900 tracking-wide">{{ $profile['nik'] ?: '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-0.5">Participant Type</label>
                                <p class="text-sm font-medium text-slate-900">{{ $profile['type'] ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 card shadow-sm">
                    <div class="card-body">
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">Title & Specialization</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-0.5">Title</label>
                                <p class="text-sm font-medium text-slate-900">{{ $profile['title'] ?: '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-0.5">Specialization</label>
                                <p class="text-sm font-medium text-slate-900">{{ $profile['title_of_specialist'] ?: '-' }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-slate-400 mb-0.5">Institution / Company</label>
                                <p class="text-sm font-medium text-slate-900">{{ $profile['institution'] ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-white shadow-sm dark:bg-slate-800">
                    <div class="card-body">

                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">Address & Location</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-slate-400 mb-0.5">Home Address</label>
                                <p class="text-sm font-medium text-slate-900 leading-relaxed">{{ $profile['address'] ?: '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-0.5">City / Regency</label>
                                <p class="text-sm font-medium text-slate-900">{{ $profile['city'] ?: '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-0.5">Province</label>
                                <p class="text-sm font-medium text-slate-900">{{ $profile['province'] ?: '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-0.5">Postal Code</label>
                                <p class="text-sm font-medium text-slate-900 tracking-wider">{{ $profile['postal_code'] ?: '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-0.5">Country</label>
                                <p class="text-sm font-medium text-slate-900">{{ $profile['country'] ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @else
            <div class="card bg-white dark:bg-slate-800 shadow-sm">
                <div class="card-body">
                    <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Edit Participant Profile</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="form-control w-full">
                            <span class="label-text mb-1">First Name</span>
                            <input type="text" class="input input-bordered w-full" wire:model.defer="profile.firstname">
                            @error('profile.firstname') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </label>

                        <label class="form-control w-full">
                            <span class="label-text mb-1">Last Name</span>
                            <input type="text" class="input input-bordered w-full" wire:model.defer="profile.lastname">
                            @error('profile.lastname') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </label>

                        <label class="form-control w-full sm:col-span-2">
                            <span class="label-text mb-1">Email</span>
                            <input type="email" class="input input-bordered w-full" wire:model.defer="profile.email">
                            @error('profile.email') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </label>

                        <label class="form-control w-full">
                            <span class="label-text mb-1">Country</span>
                            <select class="select select-bordered w-full" wire:model.defer="profile.country">
                                <option value="">Pilih country</option>
                                @foreach ($this->getCountries() as $countryName)
                                <option value="{{ $countryName }}">{{ $countryName }}</option>
                                @endforeach
                            </select>
                            @error('profile.country') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </label>

                        <label class="form-control w-full">
                            <span class="label-text mb-1">Phone</span>
                            <input type="text" class="input input-bordered w-full" wire:model.defer="profile.phone">
                            @error('profile.phone') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </label>

                        <label class="form-control w-full">
                            <span class="label-text mb-1">NIK</span>
                            <input type="text" class="input input-bordered w-full" wire:model.defer="profile.nik">
                            @error('profile.nik') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </label>

                        <label class="form-control w-full">
                            <span class="label-text mb-1">Title</span>
                            <select class="select select-bordered w-full" wire:model.defer="profile.title">
                                <option value="">Pilih title</option>
                                <option value="Dr.">Dr.</option>
                                <option value="Prof.">Prof.</option>
                                <option value="MD.">MD.</option>
                                <option value="Mr.">Mr.</option>
                                <option value="Mrs.">Mrs.</option>
                                <option value="Ms.">Ms.</option>
                            </select>
                        </label>

                        <label class="form-control w-full sm:col-span-2">
                            <span class="label-text mb-1">Title of Specialist</span>
                            <input type="text" class="input input-bordered w-full" wire:model.defer="profile.title_of_specialist">
                        </label>

                        <label class="form-control w-full">
                            <span class="label-text mb-1">Participant Type</span>
                            <select class="select select-bordered w-full" wire:model.defer="profile.type">
                                <option value="">Pilih participant type</option>
                                <option value="Specialist">Specialist</option>
                                <option value="Resident">Resident</option>
                                <option value="General Practitioner">General Practitioner</option>
                                <option value="Medical Student">Medical Student</option>
                            </select>
                        </label>

                        <label class="form-control w-full">
                            <span class="label-text mb-1">Name on Certificate</span>
                            <input type="text" class="input input-bordered w-full" wire:model.defer="profile.name_on_certificate">
                        </label>

                        <label class="form-control w-full sm:col-span-2">
                            <span class="label-text mb-1">Institution</span>
                            <input type="text" class="input input-bordered w-full" wire:model.defer="profile.institution">
                        </label>

                        <label class="form-control w-full sm:col-span-2">
                            <span class="label-text mb-1">Address</span>
                            <textarea class="textarea textarea-bordered w-full" wire:model.defer="profile.address"></textarea>
                        </label>

                        <label class="form-control w-full">
                            <span class="label-text mb-1">City</span>
                            <input type="text" class="input input-bordered w-full" wire:model.defer="profile.city">
                        </label>

                        <label class="form-control w-full">
                            <span class="label-text mb-1">Province</span>
                            <input type="text" class="input input-bordered w-full" wire:model.defer="profile.province">
                        </label>

                        <label class="form-control w-full">
                            <span class="label-text mb-1">Postal Code</span>
                            <input type="text" class="input input-bordered w-full" wire:model.defer="profile.postal_code">
                            @error('profile.postal_code') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </label>
                    </div>
                </div>
            </div>
            @endif
        </div>

    </div>
</x-filament-panels::page>