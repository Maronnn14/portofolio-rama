<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="save" class="space-y-6">
            {{ $this->form }}

            <div class="flex items-center justify-between pt-4">
                <x-filament::button
                    type="button"
                    color="gray"
                    wire:click="exportData"
                    icon="heroicon-o-arrow-down-tray"
                >
                    Export Portfolio Data (JSON)
                </x-filament::button>

                <x-filament::button type="submit" size="lg">
                    Save Settings
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
