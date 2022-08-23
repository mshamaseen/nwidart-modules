<?php

namespace Nwidart\Modules\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModuleDeleted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public string $name)
    {
    }
}
