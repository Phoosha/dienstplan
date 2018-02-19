<?php

return [

    'fpi' => env('ICS_API', '-//Phoosha//Dienstplan//EN'),
    'organizer' => env('ICS_ORGANIZER', 'mailto:mail@example.com'),
    'summary' => env('ICS_SUMMARY', 'Dienst FRS'),
    'location' => env('ICS_LOCATION', ':slot, Musterheim'),
    'refresh_interval' => env('ICS_REFRESH_INTERVAL', 'T15M'),
    'color' => env('ICS_COLOR', 'firebrick'),

];
