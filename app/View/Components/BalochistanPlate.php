<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BalochistanPlate extends Component
{
    /**
     * Create a new component instance.
     */
    public $plate;
    public $cityUrduName;

    public function __construct($plate, $cityUrduName)
    {
        $this->plate = $plate;
        $this->cityUrduName = $cityUrduName;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.balochistan-plate');
    }
}
