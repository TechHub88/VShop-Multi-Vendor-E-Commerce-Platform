<?php

if (!function_exists('hasProductPanel')) {

    function hasProductPanel()
    {
        return in_array('product', session('selected_panels', []));
    }
}

if (!function_exists('hasTravelPanel')) {

    function hasTravelPanel()
    {
        return in_array('travel', session('selected_panels', []));
    }
}

if (!function_exists('hasCampusPanel')) {

    function hasCampusPanel()
    {
        return in_array('vcampus', session('selected_panels', []));
    }
}
