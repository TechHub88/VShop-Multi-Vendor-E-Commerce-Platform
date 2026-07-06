<?php

if (!function_exists('getCurrencySymbol')) {
    function getCurrencySymbol($currency) {
        return match($currency) {
            'INR' => '₹',
            'EUR' => '€',
            'GBP' => '£',
            default => '$',
        };
    }
}
