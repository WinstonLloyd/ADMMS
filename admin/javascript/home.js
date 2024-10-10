const createFolderModal = document.getElementById('createFolderModal');
const closeCreateFolderBtn = document.getElementById('closeCreateFolder');
const createFolderBtn = document.getElementById('createFolderBtn');

createFolderBtn.onclick = function() {
    createFolderModal.style.display = "block";
}

closeCreateFolderBtn.onclick = function() {
console.log("Close button clicked"); // For debugging
createFolderModal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target === createFolderModal) {
        createFolderModal.style.display = "none";
    }
}

document.getElementById('togglePassword').addEventListener('click', function (e) {
    const passwordField = document.getElementById('password');
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
    this.classList.toggle('fa-eye-slash');
});

document.getElementById('toggleConfirmPassword').addEventListener('click', function (e) {
    const confirmPasswordField = document.getElementById('confirm_password');
    const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
    confirmPasswordField.setAttribute('type', type);
    this.classList.toggle('fa-eye-slash');
});