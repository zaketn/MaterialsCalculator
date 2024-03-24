<?php

namespace App\Livewire;

use App\Models\Characteristic;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Accordeon extends Component
{
    #[Computed]
    public function folders() : array
    {
        $characteristics = Characteristic::query()
            ->has('variations')
            ->with('variations')
            ->get();

        $groupedCharacteristics = $this->groupCharacteristics($characteristics);

        return $this->sortGroupOrder($groupedCharacteristics);
    }

    protected function groupCharacteristics(Collection $characteristics) : array
    {
        $groupedCharacteristics = [];

        foreach ($characteristics as $characteristic) {
            foreach ($characteristic->variations as $variation) {
                $groupOrder = $variation->pivot->group_order;

                if (!isset($groupedCharacteristics[$variation->name])) {
                    $groupedCharacteristics[$variation->name] = [];
                }

                if (!isset($groupedCharacteristics[$variation->name][$groupOrder])) {
                    $groupedCharacteristics[$variation->name][$groupOrder] = [];
                }

                $groupedCharacteristics[$variation->name][$groupOrder][] = $characteristic;
            }
        }

        return $groupedCharacteristics;
    }

    protected function sortGroupOrder(array $groupedCharacteristics) : array
    {
        foreach ($groupedCharacteristics as &$variation) {
            ksort($variation);
        }

        return $groupedCharacteristics;
    }
}
