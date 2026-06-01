<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Inventory Configuration
    |--------------------------------------------------------------------------
    */

    /**
     * Number of days before expiry date to show warning
     */
    'expiry_warning_days' => env('INVENTORY_EXPIRY_WARNING_DAYS', 7),

    /**
     * Default reorder point multiplier
     */
    'reorder_multiplier' => env('INVENTORY_REORDER_MULTIPLIER', 2),
];
