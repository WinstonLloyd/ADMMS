var createFolderModal = document.getElementById("createFolderModal");
var uploadFileModal = document.getElementById("uploadFileModal");
var createFolderBtn = document.getElementById("createFolderBtn");
var uploadFileBtn = document.getElementById("uploadFileBtn");
var closeCreateFolder = document.getElementById("closeCreateFolder");
var closeUploadFile = document.getElementById("closeUploadFile");
createFolderBtn.onclick = function() {
    createFolderModal.style.display = "block";
}
uploadFileBtn.onclick = function() {
    uploadFileModal.style.display = "block";
}
closeCreateFolder.onclick = function() {
    createFolderModal.style.display = "none";
}
closeUploadFile.onclick = function() {
    uploadFileModal.style.display = "none";
}
window.onclick = function(event) {
    if (event.target == createFolderModal) {
        createFolderModal.style.display = "none";
    } else if (event.target == uploadFileModal) {
        uploadFileModal.style.display = "none";
    }
}
// Function to filter folder and file results
function filterResults() {
    var input, filter, folders, files, i, folderName, fileName;
    input = document.getElementById('searchBar');
    filter = input.value.toLowerCase();
    folders = document.getElementsByClassName('folder');
    files = document.querySelectorAll('ul a');
    // Filter folders
    for (i = 0; i < folders.length; i++) {
        folderName = folders[i].getElementsByClassName('folder-name')[0];
        if (folderName.innerHTML.toLowerCase().indexOf(filter) > -1) {
            folders[i].style.display = "";
        } else {
            folders[i].style.display = "none";
        }
    }
    // Filter files
    for (i = 0; i < files.length; i++) {
        fileName = files[i].innerHTML.toLowerCase();
        if (fileName.indexOf(filter) > -1) {
            files[i].style.display = "";
        } else {
            files[i].style.display = "none";
        }
    }
}
document.addEventListener('DOMContentLoaded', function () {
    // Toggle dropdown menu visibility
    window.toggleDropdown = function(button) {
        // Find the nearest dropdown content and toggle its visibility
        var dropdownContent = button.nextElementSibling;
        if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
        } else {
            dropdownContent.style.display = "block";
        }
    };

    // Close the dropdown if the user clicks outside of it
    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.style.display === "block") {
                    openDropdown.style.display = "none";
                }
            }
        }
    };

    // Handle delete folder
    document.querySelectorAll('.delete-folder').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            var folderName = this.getAttribute('data-folder');
            if (confirm('Are you sure you want to delete this folder?')) {
                window.location.href = 'delete_folder.php?folder=' + folderName;
            }
        });
    });
});

// Get the modal
const viewFolderModal = document.getElementById('viewFolderModal');

// Get the <span> element that closes the modal
const closeViewFolder = document.getElementById('closeViewFolder');

// When the user clicks on a button, open the modal and set the content
document.querySelectorAll('.view-folder').forEach(button => {
    button.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default link behavior

        // Get folder name and row number
        const folderName = this.getAttribute('data-folder');
        const folderRow = this.getAttribute('data-row');
        const folderColumn = this.getAttribute('data-column');

        // Set the modal content
        document.getElementById('folderNameDisplay').textContent = 'Folder Name: ' + folderName;
        document.getElementById('folderRowDisplay').textContent = 'Row Number: ' + folderRow;
        document.getElementById('folderColumnDisplay').textContent = 'Column Number: ' + folderColumn;

        // Show the modal
        viewFolderModal.style.display = "block";
    });
});

// When the user clicks on <span> (x), close the modal
closeViewFolder.onclick = function() {
    viewFolderModal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == viewFolderModal) {
        viewFolderModal.style.display = "none";
    }
}