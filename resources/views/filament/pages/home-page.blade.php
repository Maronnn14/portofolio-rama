<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Featured projects & highlighted tech</x-slot>
        <x-slot name="description">
            Toggle <strong>Featured on Home</strong> on a project in Projects &amp; Experience → Coding Projects,
            and <strong>Highlight on Home Page</strong> on a skill in Skills → Hard Skills.
        </x-slot>

        <div class="flex flex-wrap gap-3">
            <x-filament::button tag="a" href="{{ \App\Filament\Resources\Projects\ProjectResource::getUrl('index') }}" icon="heroicon-o-folder" color="gray">
                Manage Projects
            </x-filament::button>

            <x-filament::button tag="a" href="{{ \App\Filament\Resources\Skills\SkillResource::getUrl('index') }}" icon="heroicon-o-cpu-chip" color="gray">
                Manage Skills
            </x-filament::button>
        </div>
    </x-filament::section>

    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end pt-4">
            <x-filament::button type="submit" size="lg">
                Save Home Page
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
