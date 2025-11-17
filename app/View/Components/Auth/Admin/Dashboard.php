<?php

namespace App\View\Components\Auth\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dashboard extends Component
{
    public $stats;
    public $filters;
    public $filterOptions;

    /**
     * Create a new component instance.
     */
    public function __construct($stats = [], $filters = [], $filterOptions = [])
    {
        $this->stats = $stats;
        $this->filters = $filters;
        $this->filterOptions = $filterOptions;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        // dd($this->stats);

        return view('components.auth.admin.dashboard');
    }
}
