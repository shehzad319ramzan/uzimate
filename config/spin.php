<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Spins per day (per user, global)
    |--------------------------------------------------------------------------
    */
    'spins_per_day' => (int) env('SPIN_SPINS_PER_DAY', 1),

    /*
    |--------------------------------------------------------------------------
    | Default site ID for spin (when app does not send site_id).
    | Null = use first available site.
    |--------------------------------------------------------------------------
    */
    'default_site_id' => env('SPIN_DEFAULT_SITE_ID'),

    /*
    |--------------------------------------------------------------------------
    | Wheel outcome chances (percent, must sum to 100). Only Nothing and Points
    | are used; Offer and Discount are not awarded in this application.
    |--------------------------------------------------------------------------
    */
    'outcomes' => [
        'nothing' => (int) env('SPIN_OUTCOME_NOTHING', 50),
        'points' => (int) env('SPIN_OUTCOME_POINTS', 50),
    ],

    /*
    |--------------------------------------------------------------------------
    | Points range when result is "points" [min, max].
    |--------------------------------------------------------------------------
    */
    'points_range' => [
        (int) env('SPIN_POINTS_MIN', 25),
        (int) env('SPIN_POINTS_MAX', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Discount range (not used: this app awards only points).
    | Kept for backward compatibility.
    |--------------------------------------------------------------------------
    */
    'discount_range' => [
        (int) env('SPIN_DISCOUNT_MIN', 5),
        (int) env('SPIN_DISCOUNT_MAX', 20),
    ],

];
