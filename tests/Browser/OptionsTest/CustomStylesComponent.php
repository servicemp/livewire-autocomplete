<?php

namespace LivewireAutocomplete\Tests\Browser\OptionsTest;

use Livewire\Component;

class CustomStylesComponent extends Component
{
    protected $queryString = [
        'inline',
    ];

    public $inline = false;

    public $results = [
        [
            'id' => '1',
            'text' => 'bob',
        ],
        [
            'id' => '2',
            'text' => 'john',
        ],
        [
            'id' => '3',
            'text' => 'bill',
        ],
    ];

    public $inputText = '';

    public $selectedSlug;

    public function render()
    {
        return <<<'HTML'
            <div dusk="page">
                <x-lwc::autocomplete
                    wire:model-text="inputText"
                    wire:model-id="selectedSlug"
                    wire:model-results="results"
                    :options="[
                        'inline' => $inline,
                        'inline_styles' => 'some-inline-style',
                        'overlay_styles' => 'some-overlay-style',
                        'result_focus_styles' => 'some-focus-style',
                    ]"
                    />

                <div dusk="input-text-output">{{ $inputText }}</div>
                <div dusk="selected-slug-output">{{ $selectedSlug }}</div>
            </div>
            HTML;
    }
}