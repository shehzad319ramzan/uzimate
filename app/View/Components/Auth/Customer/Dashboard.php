<?php

namespace App\View\Components\Auth\Customer;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dashboard extends Component
{
    public $data;

    /**
     * Create a new component instance.
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.auth.customer.dashboard');
    }
}

