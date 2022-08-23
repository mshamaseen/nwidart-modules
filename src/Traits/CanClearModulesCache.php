<?php

namespace Nwidart\Modules\Traits;

trait CanClearModulesCache
{
    /**
     * Clear the modules cache if it is enabled.
     */
    public function clearCache()
    {
        if (true === config('modules.cache.enabled')) {
            app('cache')->forget(config('modules.cache.key'));
        }
    }
}
