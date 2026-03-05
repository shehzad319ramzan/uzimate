<?php

namespace App\View\Components\Auth;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select2 extends Component
{
    public $label = null;
    public $name = null;
    public $id = null;
    public $loopData = [];
    public $existingId = null;
    public $placeholder = null;
    public $isAjax = null;
    public $ajaxRoute = null;
    public $tags = null;
    public $selectclass = null;

    /** @var array<string, string> Param name => element id for extra ajax data (e.g. ["days" => "send_inactive_days"]) */
    public $ajaxDataKeys = [];

  
    public function __construct($label, $name, $id, $placeholder = "Select option", $data = null, $existingId = null, $isAjax = false, $ajaxRoute = null, $tags = false, $selectclass = "select_2", $ajaxDataKeys = [])
    {
        $this->label = $label;
        $this->name = $name;
        $this->id = $id;
        $this->loopData = $data;
        $this->existingId = $existingId;
        $this->placeholder = $placeholder;
        $this->isAjax = $isAjax;
        $this->ajaxRoute = $ajaxRoute;
        $this->tags = $tags;
        $this->selectclass = $selectclass;
        $this->ajaxDataKeys = is_array($ajaxDataKeys) ? $ajaxDataKeys : [];
    }

    public function render(): View|Closure|string
    {
        return view('components.auth.select2');
    }
}
