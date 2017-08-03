<?php

/**
 * Plugin - CSV : 'csv_json_{metakey}'
 * Uses CsvImporter class to grab and parse csv file.
 *
 */
function pluginCSV($data)
{
     // No Meta Key.
    if (empty($data['metakey'])) {
        return;
    }

    // Check for Value.
    if (!empty($_POST[$data['metakey']])) {
        // Init Class.
        $csv = new \CsvImporter();

        // Determine if remote or local.
        if (filter_var($_POST[$data['metakey']], FILTER_VALIDATE_URL)) {
            $csv->Remote = $_POST[$data['metakey']];
        } else {
            $csv->Local = WP_CONTENT_DIR . str_replace('/wp-content', '', $_POST[$data['metakey']]);
        }

         // Get CSV
         $csvImport = $csv->get();
    }

    // # Delete meta if $metavalue or CSV Import is empty.
    if (empty($_POST[$data['metakey']]) || empty($csvImport)) {
        delete_post_meta($_POST['post_ID'], 'csv_json_' . str_replace('csv_url_', '', $data['metakey']));

        return;
    }

    // Save CSV JSON
    update_post_meta(
        $_POST['post_ID'],
        'csv_json_' . str_replace('csv_url_', '', $data['metakey']),
        wp_slash($csvImport)
    );
}
