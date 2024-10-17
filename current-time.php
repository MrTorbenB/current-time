<?php
/*
Plugin Name: Current Time Shortcode with Timezone Selection
Plugin URI:  https://torbenb.info/download/
Description: Zeigt die aktuelle Uhrzeit in deutscher Schreibweise mit einem Shortcode [current_time] an und ermöglicht es, zwischen Serverzeit und GMT+2 im Admin-Bereich zu wechseln.
Version:     1.1
Author:      TorbenB
Author URI:  https://torbenb.info
*/

// Shortcode-Funktion, um die aktuelle Uhrzeit anzuzeigen
function current_time_shortcode($atts) {
    // Standard-Attribute (falls keine Attribute angegeben sind)
    $atts = shortcode_atts(array(
        'format' => 'H:i:s', // Standardformat für die Zeit (24-Stunden-Format)
    ), $atts, 'current_time');

    // Hole die aktuelle Uhrzeit basierend auf dem angegebenen Format und der gewählten Zeitzone
    $current_time = get_admin_time($atts['format']);

    // Ausgabe der Uhrzeit in deutscher Schreibweise
    return "<div class='current-time'><strong>" . $current_time . "</strong></div>";
}

// Registriere den Shortcode
add_shortcode('current_time', 'current_time_shortcode');

// Optional: CSS für die Uhrzeit (kann angepasst werden)
function current_time_styles() {
    echo "
    <style>
    .current-time {
        font-family: 'Roboto', sans-serif;
        font-size: 1.5rem;
        color: #4a4a4a;
        background: #f9f9f9;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        max-width: 300px;
        margin: 20px auto;
    }
    .current-time strong {
        font-size: 2rem;
        color: #000000;
    }
    </style>
    ";
}
add_action('wp_head', 'current_time_styles');

// Admin-Menü für die Zeitzonen-Einstellungen hinzufügen
add_action('admin_menu', 'time_settings_menu');

function time_settings_menu() {
    add_menu_page(
        'Zeit Einstellungen',
        'Zeit Einstellungen',
        'manage_options',
        'zeit-einstellungen',
        'time_settings_page',
        'dashicons-clock',
        20
    );
}

function time_settings_page() {
    // Wenn das Formular abgeschickt wird, speichern wir die ausgewählte Zeitzone in der Option
    if (isset($_POST['timezone'])) {
        update_option('admin_selected_timezone', sanitize_text_field($_POST['timezone']));
        echo "<div class='updated'><p>Zeitzone gespeichert.</p></div>";
    }

    // Aktuelle Auswahl abrufen
    $current_timezone = get_option('admin_selected_timezone', 'server');

    ?>
    <div class="wrap">
        <h1>Zeit Einstellungen</h1>
        <form method="post" action="">
            <label for="timezone">Wähle die Zeitzone:</label><br><br>
            <select id="timezone" name="timezone">
                <option value="server" <?php selected($current_timezone, 'server'); ?>>Serverzeit</option>
                <option value="gmt+2" <?php selected($current_timezone, 'gmt+2'); ?>>GMT +2</option>
            </select>
            <br><br>
            <input type="submit" value="Speichern" class="button button-primary">
        </form>
    </div>
    <?php
}

// Funktion, um die aktuelle Uhrzeit basierend auf der Admin-Auswahl zu erhalten
function get_admin_time($format = 'Y-m-d H:i:s') {
    // Hole die ausgewählte Zeitzone aus der gespeicherten Option
    $selected_timezone = get_option('admin_selected_timezone', 'server');

    // Erstelle ein DateTime-Objekt mit der aktuellen Serverzeit
    $date = new DateTime('now', new DateTimeZone('UTC')); // Verwende UTC als Standard für die Serverzeit

    // Passe die Zeitzone basierend auf der Auswahl an
    if ($selected_timezone === 'gmt+2') {
        $date->setTimezone(new DateTimeZone('Etc/GMT-2')); // GMT+2 einstellen
    } else {
        $date->setTimezone(new DateTimeZone(date_default_timezone_get())); // Server-Zeitzone
    }

    // Rückgabe der formatierten Uhrzeit
    return $date->format($format);
}
