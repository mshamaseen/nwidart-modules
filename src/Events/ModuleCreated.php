<?php

namespace Nwidart\Modules\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Nwidart\Modules\Generators\ModuleGenerator;

class ModuleCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public ModuleGenerator $moduleGenerator)
    {
    }
}
