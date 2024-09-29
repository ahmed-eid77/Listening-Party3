<?php

use Livewire\Volt\Component;
use App\Models\ListeningParty;
use App\Models\Episode;
use App\Jobs\ProcessPodcastUrl;
use Livewire\Attributes\Validate;

new class extends Component {

    #[Validate('required', 'string')]
    public string $name = '';

    #[Validate('required', 'url')]
    public string $mediaUrl = '';

    #[Validate('required')]
    public $startTime;

    public function createListeningParty()
    {
        $this->validate();

        $episode = Episode::create([
            'media_url' => $this->mediaUrl,
        ]);
        
        $listening_party = ListeningParty::create([
            'episode_id' => $episode->id,
            'name' => $this->name,
            'start_time' => $this->startTime,
        ]);

        ProcessPodcastUrl::dispatch($this->mediaUrl, $listening_party, $episode);

        return redirect()->route('parties.show', $listening_party);
    }

    public function with(): array
    {
        return [
            'listening_parties' => ListeningParty::all(),
        ];
    }
}; ?>

<div class="flex items-center justify-center min-h-screen bg-slate-50">
    <div class="max-w-lg w-full px-4 ">
        <form wire:submit.prevent="createListeningParty" class="space-y-6">
            <x-input wire:model='name' placeholder="Listening Party Name" />
            <x-input wire:model='mediaUrl' placeholder="Podcast RSS Feed URL" description="Entering the RSS Feed URL will grap the latest episode" />
            <x-datetime-picker wire:model.live="startTime" placeholder="Listening Party Start Time" :min="now()->subDays(1)" />
            <x-button primary type="submit">Create Listening Party</x-button>
        </form>
    </div>

</div>
