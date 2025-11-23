<?php

// Helper functions
function prettyCondition(?string $cond): string
{
    return match ($cond) {
        'women_only'      => 'Women only',
        'undergrad_only'  => 'Undergraduates only',
        'first_year_only' => 'First years only',
        'none', null, ''  => 'Open to all',
        default           => ucfirst(str_replace('_', ' ', $cond)),
    };
}

?>