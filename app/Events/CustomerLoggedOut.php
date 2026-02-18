<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class CustomerLoggedOut
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $userId,
        public Request $request,
        public string $description
    ) {}
}
