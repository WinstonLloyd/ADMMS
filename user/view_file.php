<?php
session_start();
include("connection.php");

if (!isset($_SESSION['user_id'])) {
    echo "User not logged in.";
    exit;
}

// Check if the file ID and folder are set
if (isset($_GET['id']) && isset($_GET['folder'])) {
    $fileId = intval($_GET['id']);
    $folder = $_GET['folder'];

    // Query to get the file content
    $query = "SELECT document_name, file_content FROM `$folder` WHERE id = $fileId";
    $result = mysqli_query($con, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        // Store file details in variables
        $fileName = $row['document_name'];
        $fileContent = $row['file_content'];

        // Set headers to display the PDF in the browser
        header('Content-Type: application/pdf');
        header("Content-Disposition: inline; filename=\"" . $fileName . "\"");
        echo $fileContent;  // Output the PDF content
    } else {
        echo "File not found.";
    }
} else {
    echo "Invalid request.";
}
?>
