<x-filament::button 
    type="submit" 
    size="md" 
    wire:loading.attr="disabled"
    wire:target="create"
>
    <x-slot name="icon">
        <x-heroicon-m-check-circle class="w-5 h-5" wire:loading.remove wire:target="create" />
        <x-filament::loading-indicator class="w-5 h-5" wire:loading wire:target="create" />
    </x-slot>

    <span wire:loading.remove wire:target="create">
        Buat Misi Sekarang
    </span>
    
    <span wire:loading wire:target="create">
        Memproses...
    </span>
</x-filament::button>
