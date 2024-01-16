<?php
if (isset($_GET['filename'])) {
    $filename = $_GET['filename'];
    if (file_exists($filename)) {
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($filename) . '"');

        readfile($filename);
    } else {
        echo "File not found";
    }
} else {
    echo "Invalid request";
}
?>
