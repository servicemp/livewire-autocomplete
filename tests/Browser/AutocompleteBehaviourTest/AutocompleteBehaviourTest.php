<?php

namespace LivewireAutocomplete\Tests\Browser\AutocompleteBehaviourTest;

use Laravel\Dusk\Browser;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\Browser\TestCase;

class AutocompleteBehaviourTest extends TestCase
{
    /** @test */
    public function an_input_is_shown_on_screen()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->assertPresent('@autocomplete-input')
                    ;
        });
    }

    /** @test */
    public function dropdown_appears_when_input_is_focused()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->assertMissing('@autocomplete-dropdown')
                    ->click('@autocomplete-input')
                    ->assertVisible('@autocomplete-dropdown')
                    ;
        });
    }

    /** @test */
    public function dropdown_closes_when_anything_else_is_clicked_and_focus_is_removed()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->assertVisible('@autocomplete-dropdown')
                    ->clickAtXPath('//body')
                    ->assertNotFocused('@autocomplete-input')
                    ->assertMissing('@autocomplete-dropdown')
                    ;
        });
    }

    /** @test */
    public function dropdown_closes_when_escape_is_pressed_and_focus_removed()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->assertVisible('@autocomplete-dropdown')
                    ->keys('@autocomplete-input', '{escape}')
                    ->assertNotFocused('@autocomplete-input')
                    ->assertMissing('@autocomplete-dropdown')
                    ;
        });
    }

    /** @test */
    public function dropdown_shows_list_of_results()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->assertSeeIn('@autocomplete-dropdown', 'bob')
                    ->assertSeeIn('@autocomplete-dropdown', 'john')
                    ->assertSeeIn('@autocomplete-dropdown', 'bill')
                    ->assertSeeInOrder('@autocomplete-dropdown', ['bob', 'john', 'bill'])
                    ;
        });
    }

    /** @test */
    public function results_are_filtered_based_on_input()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->assertSeeInOrder('@autocomplete-dropdown', ['bob', 'john', 'bill'])
                    ->waitForLivewire()->type('@autocomplete-input', 'b')
                    ->assertSeeInOrder('@autocomplete-dropdown', ['bob', 'bill'])
                    ->assertDontSeeIn('@autocomplete-dropdown', 'john')
                    ;
        });
    }

    /** @test */
    public function down_arrow_focus_first_option_if_there_is_no_focus_in_dropdown()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertHasClass('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function down_arrow_focus_next_option_if_there_is_already_a_focus_in_dropdown()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertHasClass('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function down_arrow_focus_remains_on_last_result_in_dropdown()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertHasClass('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertHasClass('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function up_arrow_clears_focus_if_first_option_is_focused_in_dropdown()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertHasClass('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{ARROW_UP}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function up_arrow_focuses_previous_option_if_there_is_another_option_before_current_in_dropdown()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertHasClass('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{ARROW_UP}')
                    ->assertHasClass('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function up_arrow_focuses_nothing_if_nothing_currently_focused_in_dropdown()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{ARROW_UP}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function home_key_focuses_first_result_in_dropdown()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    // Attempt if none selected
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{HOME}')
                    ->assertHasClass('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')

                    // Attempt if one further down the list is selected
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertHasClass('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{HOME}')
                    ->assertHasClass('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function end_key_focuses_last_result_in_dropdown()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    // Attempt if none selected
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{END}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertHasClass('@result-2', 'bg-blue-500')

                    // Attempt if one further up the list is selected
                    ->keys('@autocomplete-input', '{ARROW_UP}')
                    ->keys('@autocomplete-input', '{ARROW_UP}')
                    ->assertHasClass('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{END}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertHasClass('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function focus_is_cleared_if_input_changes()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertHasClass('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->waitForLivewire()->type('@autocomplete-input', 'b')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function enter_key_selects_currently_focused_result()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertHasClass('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->waitForLivewire()->keys('@autocomplete-input', '{ENTER}')
                    ->assertSeeIn('@result-output', 'john')
                    ;
        });
    }

    /** @test */
    public function enter_key_only_selects_if_there_is_a_currently_focused_result()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{ENTER}')
                    ->pause(300)
                    ->assertSeeNothingIn('@result-output')
                    ;
        });
    }

    /** @test */
    public function dropdown_is_hidden_and_focus_cleared_on_selection()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertHasClass('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->waitForLivewire()->keys('@autocomplete-input', '{ENTER}')
                    ->assertSeeIn('@result-output', 'bob')
                    ->assertMissing('@autocomplete-dropdown')
                    ->click('@autocomplete-input')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function tab_key_selects_currently_focused_result()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->keys('@autocomplete-input', '{END}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertHasClass('@result-2', 'bg-blue-500')
                    ->waitForLivewire()->keys('@autocomplete-input', '{TAB}')
                    ->assertSeeIn('@result-output', 'bill')
                    ;
        });
    }

    /** @test */
    public function tab_key_only_selects_if_there_is_a_currently_focused_result()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->keys('@autocomplete-input', '{END}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertHasClass('@result-2', 'bg-blue-500')
                    ->waitForLivewire()->keys('@autocomplete-input', '{TAB}')
                    ->assertSeeIn('@result-output', 'bill')
                    ;
        });
    }

    /** @test */
    public function shift_tab_does_not_select_currently_focused_result()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->keys('@autocomplete-input', '{HOME}')
                    ->assertHasClass('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{SHIFT}', '{TAB}')
                    ->pause(300)
                    ->assertDontSeeIn('@result-output', 'bob')
                    ;
        });
    }

    /** @test */
    public function mouse_hover_focuses_result()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->mouseover('@result-1')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertHasClass('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->mouseover('@result-2')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertHasClass('@result-2', 'bg-blue-500')
                    ->mouseover('@result-0')
                    ->assertHasClass('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function mouse_leave_clears_focus_result()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->mouseover('@result-1')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertHasClass('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    //Empty mouseover simulates mouseout by mousing over body
                    ->mouseover('')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function mouse_click_selects_result()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->waitForLivewire()->click('@result-1')
                    ->assertSeeIn('@result-output', 'john')
                    ;
        });
    }

    /** @test */
    public function selected_result_shown_in_input()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->waitForLivewire()->click('@result-1')
                    ->assertValue('@autocomplete-input', 'john')
                    ;
        });
    }

    /** @test */
    public function mouse_click_only_fires_once_on_newly_generated_morphed_results()
    {
        // This is a bug in livewire/livewire#763, this test triggers it without work around
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->waitForLivewire()->type('@autocomplete-input', 'john')
                    ->assertSeeIn('@autocomplete-dropdown', 'john')
                    ->assertDontSeeIn('@autocomplete-dropdown', 'bob')
                    ->assertDontSeeIn('@autocomplete-dropdown', 'bill')
                    // Need to press keys to trigger input events livewire requires
                    ->waitForLivewire()->keys('@autocomplete-input', '{BACKSPACE}', '{BACKSPACE}', '{BACKSPACE}', '{BACKSPACE}')
                    ->assertSeeInOrder('@autocomplete-dropdown', ['bob', 'john', 'bill'])
                    ->waitForLivewire()->click('@result-2')
                    ->assertSeeIn('@result-output', 'bill')
                    ;
        });
    }
}