<?php
include("connection.php");

if (isset($_GET['folder'])) {
    $folderName = $_GET['folder'];

    // Path to the folder in the filesystem
    $folderPath = 'uploads/' . $folderName;

    // Drop the table associated with the folder
    $dropQuery = "DROP TABLE `$folderName`";

    if (mysqli_query($con, $dropQuery)) {
        // Check if the folder exists in the filesystem
        if (is_dir($folderPath)) {
            // Function to recursively delete a folder and its contents
            function deleteFolder($folder) {
                // Get all the files and directories inside the folder
                $files = array_diff(scandir($folder), ['.', '..']);

                foreach ($files as $file) {
                    $filePath = "$folder/$file";
                    if (is_dir($filePath)) {
                        deleteFolder($filePath); // Recursively delete subfolders
                    } else {
                        unlink($filePath); // Delete files
                    }
                }
                // Remove the folder itself
                return rmdir($folder);
            }

            // Attempt to delete the folder
            if (deleteFolder($folderPath)) {
                echo "<script>alert('Folder and table deleted successfully!');</script>";
            } else {
                echo "<script>alert('Error deleting folder from the file system.');</script>";
            }
        } else {
            echo "<script>alert('Folder does not exist in the file system.');</script>";
        }

        // Redirect back to the home page
        header("Location: home.php");
        exit;
    } else {
        echo "<script>alert('Error deleting folder: " . mysqli_error($con) . "');</script>";
    }
} else {
    echo "<script>alert('No folder specified for deletion.');</script>";
}
?>
