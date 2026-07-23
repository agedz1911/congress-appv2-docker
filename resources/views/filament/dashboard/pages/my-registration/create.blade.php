<x-filament-panels::page>
     @include('components.styles')
    <livewire:registration.create :product="request()->query('product')" />
</x-filament-panels::page>