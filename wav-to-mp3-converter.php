<?php
/*
Plugin Name: WAV to MP3 Converter
Description: Converts uploaded WAV files to MP3 format using native WordPress functionality.
Version: 1.0
Author: Your Name
*/

// Enqueue scripts and styles
function wav_to_mp3_converter_enqueue_scripts() {
    wp_enqueue_script('wav-to-mp3-converter-script', plugins_url('js/script.js', __FILE__), array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'wav_to_mp3_converter_enqueue_scripts');

// Add shortcode for the file upload form and conversion interface
function wav_to_mp3_converter_shortcode() {
    ob_start();
    ?>

    <div id="wav-to-mp3-container">
        <form id="wav-to-mp3-form" method="post" enctype="multipart/form-data">
            <input type="file" id="wav-file-input" name="wav_file" accept=".wav">
            <input type="button" id="convert-btn" value="Convert">
            <div id="conversion-status"></div>
            <div id="download-link-container"></div>
        </form>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('wav_to_mp3_converter', 'wav_to_mp3_converter_shortcode');

// AJAX callback to handle file upload and conversion
function wav_to_mp3_converter_ajax_handler() {
    if (!is_user_logged_in()) {
        wp_send_json_error('Error: Unauthorized access');
    }

    if (!isset($_FILES['wav_file'])) {
        wp_send_json_error('Error: No file uploaded');
    }

    $file = $_FILES['wav_file'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error('Error: File upload failed');
    }

    // Check file type
    $file_type = wp_check_filetype($file['name']);
    if ($file_type['ext'] !== 'wav') {
        wp_send_json_error('Error: Invalid file type. Only WAV files are allowed');
    }

    // Create a unique file name
    $file_name = sanitize_file_name($file['name']);
    $file_path = wp_upload_dir()['path'] . '/' . $file_name;

    // Move the uploaded file to the uploads directory
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        wp_send_json_error('Error: File could not be saved');
    }

    // Convert WAV to MP3 using wp_audio_conversion
    $mp3_file_path = wp_audio_conversion($file_path, 'mp3');

    if ($mp3_file_path) {
        // Delete the original WAV file
        unlink($file_path);

        // Prepare the response
        $file_name = basename($mp3_file_path);

        $response = array(
            'success' => true,
            'file_name' => $file_name,
            'download_url' => wp_get_attachment_url($mp3_file_path)
        );

        wp_send_json_success($response);
    } else {
        wp_send_json_error('Error: File conversion failed');
    }
}
add_action('wp_ajax_wav_to_mp3_converter', 'wav_to_mp3_converter_ajax_handler');
add_action('wp_ajax_nopriv_wav_to_mp3_converter', 'wav_to_mp3_converter_ajax_handler');
