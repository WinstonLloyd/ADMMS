<?php
session_start();
include("connection.php");

if (!isset($_SESSION['user_id'])) {
    echo "User not logged in.";
    exit;
}

$user_id = $_SESSION['user_id'];

function getFolderDepth($folder) {
    return substr_count($folder, '_');
}

if (isset($_POST['create_folder']) && isset($_POST['folder_name'])) {
    $newFolderName = $_POST['folder_name'];
    $newFolderName = preg_replace('/[^a-zA-Z0-9 _-]/', '', $newFolderName);
    $newFolderName = substr($newFolderName, 0, 100);

    $folderRow = isset($_POST['row']) ? intval($_POST['row']) : 0;
    $folderColumn = isset($_POST['column']) ? intval($_POST['column']) : 0;

    $parentFolder = isset($_GET['folder']) ? $_GET['folder'] : $user_id;

    // Check the folder depth
    $folderDepth = getFolderDepth($parentFolder);
    if ($folderDepth >= 10) {
        echo "<script>alert('Cannot create more than 10 nested folders.');
        window.location.href = 'home.php';</script>";
        exit;
    }

    $tableName = $parentFolder . "_" . preg_replace('/\s+/', ' ', strtolower($newFolderName));

    $folderColor = $_POST['folder_color'];

    if (strlen($tableName) > 64) {
        $folder = isset($_GET['folder']) ? $_GET['folder'] : $user_id;

        echo "<script>alert('Folder name is too long, please shorten it.');
        window.location.href = 'home.php?folder=$folder';</script>";
        exit;
    }

    if (!empty($newFolderName)) {
        // Construct the new folder table name
        $tableName = $parentFolder . "_" . preg_replace('/\s+/', ' ', strtolower($newFolderName));
    
        // Directory path on the file system (adjust as needed)
        $folderPath = 'uploads/' . $tableName; // Example: folders will be created under 'uploads' directory
    
        // Check if a folder with the same name/path already exists under the parent folder in the DB
        $checkQuery = "SHOW TABLES LIKE '$tableName'";
        $result = mysqli_query($con, $checkQuery);
    
        // Check if a folder with the same name/path exists in the file system
        if (!is_dir($folderPath)) {
            if (mysqli_num_rows($result) == 0) {
                // If no folder exists in the DB, create the new folder table
                $createQuery = "CREATE TABLE `$tableName` (
                    id INT(11) AUTO_INCREMENT PRIMARY KEY,
                    folder_name VARCHAR(255) NOT NULL,
                    folder_row INT(11) NOT NULL,
                    folder_column INT(11) NOT NULL,
                    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    random_number INT NOT NULL DEFAULT 0,
                    document_name VARCHAR(255) NOT NULL,
                    file_content LONGBLOB NOT NULL
                )";
    
                if (mysqli_query($con, $createQuery)) {
                    // Generate random number for folder
                    $randomNumber = rand(1000, 9999);
    
                    // Insert folder data into the database
                    $insertFolderQuery = "INSERT INTO `$tableName` (folder_name, folder_row, folder_column, random_number) 
                                        VALUES ('$newFolderName', $folderRow, $folderColumn, $randomNumber)";
                    
                    if (mysqli_query($con, $insertFolderQuery)) {
                        // Create the physical folder in the file system
                        if (mkdir($folderPath, 0777, true)) {
                            echo "<script>alert('Folder created successfully!');</script>";
    
                            // Save folder color if needed
                            $colorsFile = 'folder_colors.json';
                            $folderColors = [];
    
                            if (file_exists($colorsFile)) {
                                $folderColors = json_decode(file_get_contents($colorsFile), true);
                            }
    
                            $folderColors[$tableName] = $folderColor;
                            file_put_contents($colorsFile, json_encode($folderColors));
                        } else {
                            echo "<script>alert('Error creating folder on the file system.');</script>";
                        }
                    } else {
                        echo "<script>alert('Error inserting folder data: " . mysqli_error($con) . "');</script>";
                    }
                } else {
                    echo "<script>alert('Error creating folder in the database: " . mysqli_error($con) . "');</script>";
                }
            } else {
                echo "<script>alert('Folder already exists in the database! Please choose a different name.');</script>";
            }
        } else {
            echo "<script>alert('Folder already exists in the file system! Please choose a different name.');</script>";
        }
    } else {
        echo "<script>alert('Please enter a valid folder name.');</script>";
    }
    
}

if (isset($_FILES['file_upload'])) {
    // Get folder where the file will be uploaded
    $folder = isset($_GET['folder']) ? $_GET['folder'] : null;

    if ($folder) {
        $fileName = $_FILES['file_upload']['name'];
        $fileTmpName = $_FILES['file_upload']['tmp_name'];

        // Create directory if not exists
        $folderPath = 'uploads/' . $folder;
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Move file to the folder (e.g., uploads/{folder}/file.pdf)
        $filePath = $folderPath . '/' . $fileName;
        if (move_uploaded_file($fileTmpName, $filePath)) {
            // Store the file in the database as a BLOB
            $fileContent = file_get_contents($filePath); // Read the content of the file

            $insertFileQuery = "INSERT INTO `$folder` (document_name, file_content) VALUES (?, ?)";
            $stmt = mysqli_prepare($con, $insertFileQuery);
            mysqli_stmt_bind_param($stmt, "sb", $fileName, $fileContent); // 'sb' - string, binary

            // Send the file to the database
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('File uploaded successfully!');</script>";
            } else {
                echo "<script>alert('Error inserting file data into database: " . mysqli_error($con) . "');</script>";
            }
        } else {
            echo "<script>alert('Error moving file to directory.');</script>";
        }
    } else {
        echo "<script>alert('No folder specified for upload.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <link rel="stylesheet" href="css/home4.css">
</head>
<body>
    <aside>
        <div class="logo">
            <img src="assets/MSWD Logo2.png" alt="MSWD LOGO">
            <a href="home.php">Archival and Document Mapping Management System</a>
        </div>
        <button id="createFolderBtn"><i class='fa fa-folder'></i> Create New Folder</button>
        <button id="uploadFileBtn"><i class='fa fa-file'></i> Upload File</button>
        <div class="logout-container">
            <a href="logout.php" class="logout-button"><i class='fa fa-arrow-right'></i> LOG OUT</a>
        </div>
    </aside>

    <div id="main-content">
        <div class="header--wrapper">
            <div class="logo--title">
                <img src="assets/Buena Logo.png" alt="Buena Logo" class="buenalogo">
                <p>Municipal Social Welfare and Development</p>
                <img src="assets/MSWD Logo.png" alt="MSWD Logo" class="mswdlogo">
            </div>
            <div class="header--title">

                <input type="text" id="searchBar" placeholder="Search for folders" onkeyup="filterResults()"> <i class='fa fa-search'></i>
            </div>
            <button class="user--infoBtn">
                <div class="user--info">
                <?php
                    $user_id = $_SESSION['user_id'];
                    $query = mysqli_query($con, "SELECT * FROM users WHERE user_id=$user_id");
                    while($row = mysqli_fetch_assoc($query)){
                        $fullname = $row['fullname'];
                        $email = $row['email'];
                        $contact = $row['contact'];
                        $user_id = $row['user_id'];
                    }
                    echo "<li><p><i class='fa fa-user'></i> $fullname</p>   
                    </li>";
                ?>
                </div>
            </button>
        </div>
        <p class="current-folder-name">
        <?php 
                if (isset($_GET['folder']) && $_GET['folder'] !== $user_id) {
                    $currentFolder = $_GET['folder'];
                    $parentFolder = substr($currentFolder, 0, strrpos($currentFolder, '_'));
            ?>
                <a href="home.php?folder=<?php echo $parentFolder; ?>" id="backButton"><i class="fa fa-arrow-left"></i></a>
            <?php } ?>
            <?php
                // Get the current folder from the URL
                $folderPath = isset($_GET['folder']) ? $_GET['folder'] : $user_id;
                $folderNames = [];

                // Get folder names from the database
                $currentFolder = $folderPath;

                while ($currentFolder !== $user_id) { // Traverse until reaching the root
                    $folderDetailsQuery = "SELECT folder_name FROM `$currentFolder` LIMIT 1";
                    $folderDetailsResult = mysqli_query($con, $folderDetailsQuery);
                    $folderDetails = mysqli_fetch_assoc($folderDetailsResult);
                
                    if ($folderDetails) {
                        $folderNames[] = htmlspecialchars($folderDetails['folder_name']); // Add folder name to array
                    }
                
                    // Get parent folder
                    $currentFolder = substr($currentFolder, 0, strrpos($currentFolder, '_'));
                }

                // Reverse the array to show from root to current
                $folderNames = array_reverse($folderNames);

                // Display breadcrumbs
                foreach ($folderNames as $index => $name) {
                    $breadcrumbPath = implode('_', array_slice($folderNames, 0, $index + 1)); // Get the breadcrumb path
                    echo "$name";
                    if ($index < count($folderNames) - 1) {
                        echo " > "; // Separator
                    }
                }
            ?>
        </p>
        <div id="folder-container">
            <?php
                $parentFolder = isset($_GET['folder']) ? $_GET['folder'] : $user_id;
                $query = "SHOW TABLES LIKE '" . $parentFolder . "_%'";
                $result = mysqli_query($con, $query);

                $colorsFile = 'folder_colors.json';
                $folderColors = [];
                if (file_exists($colorsFile)) {
                    $folderColors = json_decode(file_get_contents($colorsFile), true);
                }

                // Display folders
                while ($row = mysqli_fetch_row($result)) {
                    $tableName = $row[0];
                    $subFolder = str_replace($parentFolder . '_', '', $tableName);

                    if (strpos($subFolder, '_') !== false) {
                        continue;
                    }

                    $folderDetailsQuery = "SELECT folder_row, folder_column FROM `$tableName` LIMIT 1";
                    $folderDetailsResult = mysqli_query($con, $folderDetailsQuery);
                    $folderDetails = mysqli_fetch_assoc($folderDetailsResult);

                    $folderRow = $folderDetails['folder_row'];
                    $folderColumn = $folderDetails['folder_column'];


                    $folderColor = isset($folderColors[$tableName]) ? $folderColors[$tableName] : "#ffc107";
                    echo "<div class='folder' style='background-color: $folderColor; position: relative;'>
                        <a href='home.php?folder=$tableName'>
                            <div class='folder-name'>$subFolder</div>
                        </a>
                        <div class='dropdown'>
                            <button class='dropbtn'>...</button>
                            <div class='dropdown-content'>
                                <a href='#' class='rename-folder' data-folder='<?php echo $tableName; ?>'>Rename</a>
                                <a href='#' class='view-folder' data-folder='$subFolder' data-row='$folderRow' data-column='$folderColumn'>View</a>
                                <a href='#' class='delete-folder' data-folder='$tableName'>Delete</a>
                            </div>
                        </div>
                    </div>";

                }

                // Display files in the selected folder
                if (isset($_GET['folder'])) {
                    $selectedFolder = $_GET['folder'];
                
                    // Check if the table exists before querying
                    $checkTableQuery = "SHOW TABLES LIKE '$selectedFolder'";
                    $tableExists = mysqli_query($con, $checkTableQuery);
                
                    if (mysqli_num_rows($tableExists) > 0) {
                        $fileQuery = "SELECT * FROM `$selectedFolder`";
                        $fileResult = mysqli_query($con, $fileQuery);
                    
                        echo "<ul>";
                        while ($fileRow = mysqli_fetch_assoc($fileResult)) {
                            $fileName = htmlspecialchars($fileRow['document_name']);
                            $encodedFileName = urlencode($fileName); // Encode the file name to make it URL-safe
                        
                            // Create the file path for displaying in the browser
                            $filePath = "uploads/$selectedFolder/$encodedFileName"; // Path to the uploaded file
                        
                            // Create a clickable link to display the PDF in an iframe
                            echo "<li class='uploaded_files'><a href='home.php?folder=$selectedFolder&file=$encodedFileName'>$fileName</a></li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<script>window.location.href = 'home.php';</script>";
                    }
                }

                // Check if a file is selected and display it
                if (isset($_GET['file'])) {
                    $selectedFile = urldecode($_GET['file']);
                    $filePath = "uploads/$selectedFolder/$selectedFile"; // Path to the uploaded file
                
                    // Check if the file exists
                    if (file_exists($filePath)) {
                        echo "<iframe src='$filePath' width='100%' height='700px'></iframe>";
                    } else {
                        echo "<p>File not found.</p>";
                    }
                }
            ?>
        </div>
    </div>

    <div id="createFolderModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeCreateFolder">&times;</span>
            <h2>Create a New Folder</h2>
            <form method="POST">
                <label for="">Folder Name</label>
                <input type="text" name="folder_name" placeholder="Enter folder name" required>
                <label for="">Row Number</label>
                <input type="number" name="row" placeholder="Enter Row number">
                <label for="">Column Number</label>
                <input type="number" name="column" placeholder="Enter Column number">
                <label for="folder_color">Select Folder Color:</label>
                <select name="folder_color" required>
                    <option value="#FFFFFF">White</option>
                    <option value="#ffc0cb">Pink</option>
                    <option value="#ff8c00">Orange</option>
                    <option value="#ff0000">Red</option>
                    <option value="#ffff00">Yellow</option>
                    <option value="#008000">Green</option>
                </select>
                <button type="submit" name="create_folder">Create Folder</button>
            </form>
        </div>
    </div>

    <div id="uploadFileModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeUploadFile">&times;</span>
            <h2>Upload File</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="file" name="file_upload" required>
                <input type="submit" class="upload_fill_button" value="Upload">
            </form>
        </div>
    </div>

    <!-- View Folder Modal -->
    <div id="viewFolderModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeViewFolder">&times;</span>
                <h2>Folder Details</h2>
                <p id="folderNameDisplay"></p>
                <p id="folderRowDisplay"></p>
                <p id="folderColumnDisplay"></p>
            </div>
        </div>
    </div>

    <script src="javascript/home.js"></script>
</body>
</html>