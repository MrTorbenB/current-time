<?php
/*
Plugin Name: Current Time Shortcode
Plugin URI:  https://example.com
Description: Zeigt die aktuelle Uhrzeit in deutscher Schreibweise mit einem Shortcode [current_time] an.
Version:     1.0
Author:      Dein Name
Author URI:  https://example.com
License:     GPLv2 or later
*/

// Shortcode-Funktion, um die aktuelle Uhrzeit anzuzeigen
function current_time_shortcode($atts) {
    // Standard-Attribute (falls keine Attribute angegeben sind)
    $atts = shortcode_atts(array(
        'format' => 'H:i:s', // Standardformat für die Zeit (24-Stunden-Format)
    ), $atts, 'current_time');

    // Hole die aktuelle Uhrzeit basierend auf dem angegebenen Format
    $current_time = current_time($atts['format']);

    // Ausgabe der Uhrzeit in deutscher Schreibweise
    return "Die aktuelle Uhrzeit ist: <strong>" . $current_time . "</strong>";
}

// Registriere den Shortcode
add_shortcode('current_time', 'current_time_shortcode');

// Optional: CSS für die Uhrzeit (kann angepasst werden)
function current_time_styles() {
    echo "
    <style>
    .current-time {
        font-family: Arial, sans-serif;
        font-size: 18px;
        color: #333;
    }
    .current-time strong {
        font-size: 20px;
        color: #FF0000;
    }
    </style>
    ";
}
add_action('wp_head', 'current_time_styles');