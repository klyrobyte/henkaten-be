<?php

namespace App\Http\View\Composers;

use App\Models\Factory;
use Illuminate\View\View;

/**
 * FactoryComposer
 *
 * Shares $factories with every view that uses layouts.admin,
 * so the layout's factory picker, tvDropdown, and tvPickerSheet
 * are always dynamic without each controller needing to pass them.
 * Fixed by Rizky
 */
class FactoryComposer
{
    public function compose(View $view): void
    {
        // Only inject if not already set by the controller (avoid double query)
        if (!$view->offsetExists('factories')) {
            $view->with('factories', Factory::orderBy('order_index')->get());
        }
    }
}
