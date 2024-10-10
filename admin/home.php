<?php
session_start();
include("connection.php");

if (!isset($_SESSION['id'])) {
    echo "User not logged in.";
    exit;
}

$id = $_SESSION['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <link rel="stylesheet" href="css/home1.css">
</head>
<body>
    <aside>
        <div class="logo">
            <img src="assets/MSWD Logo2.png" alt="MSWD LOGO">
            <a href="home.php">Admin</a>
        </div>
        <button id="createFolderBtn"><i class='fa fa-user'></i> Add User Account</button>
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
            <p class="title">Archival and Document Mapping Management System</p>
        </div>
        <div>
            <table class="content-table">
                <thead>
                    <h4>Users</h4>
                    <tr>
                        <th>No</th>
                        <th>Fullname</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Password</th>
                        <th>Operation</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $sql = "SELECT * FROM users";
                    $result = mysqli_query($con,$sql);
                    if($result){
                        while($row=mysqli_fetch_assoc($result)){
                            $user_id=$row['user_id'];
                            $fullname=$row['fullname'];
                            $email=$row['email'];
                            $contact=$row['contact'];
                            $address=$row['address'];
                            $password=$row['password'];
                            echo '<tr>
                            <th scope="row">'.$user_id.'</th>
                            <td>'.$fullname.'</td>
                            <td>'.$email.'</td>
                            <td>'.$contact.'</td>
                            <td>'.$address.'</td>
                            <td>'.$password.'</td>
                            <td>
                                <button>Edit</button>
                                <button class="btn2"><a href="delete_user.php? deleteid='.$user_id.'">Delete</a></button>
                            </td>
                            <td>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="createFolderModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeCreateFolder">&times;</span>
            <?php
            include("connection.php");
            $emailError = "";
            $passwordError = "";
            $fullname = "";
            $email = "";
            $contact = "";
            $address = "";
            if(isset($_POST['submit'])){
                $fullname = $_POST['fullname'];
                $email = $_POST['email'];
                $contact = $_POST['contact'];
                $address = $_POST['address'];
                $password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];

                $verify_query = mysqli_query($con, "SELECT email FROM users WHERE email = '$email'");
                if(mysqli_num_rows($verify_query) != 0){
                    $emailError = "This email is used, try another one!";
                } else if ($password !== $confirm_password) {
                    $passwordError = "Passwords do not match!";
                } else {
                    mysqli_query($con, "INSERT INTO users(fullname, email, contact, address, password) 
                    VALUES ('$fullname','$email','$contact','$address','$password')")
                    or die ("Error Occurred");
                    
                    echo '<script>alert("Sign up Successfully!");</script>';
                    echo '<script>window.location.href="home.php";</script>';
                }
            }
        ?>
            <form action="" method="POST">
            <h2>Register</h2>
            <div class="input-group">
                <label for="fullname">Fullname:</label>
                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <span class="error"><?php echo $emailError; ?></span>
            </div>
            <div class="input-group">
                <label for="contact">Contact:</label>
                <input type="number" id="contact" name="contact" value="<?php echo htmlspecialchars($contact); ?>" required>
            </div>
            <div class="input-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" required>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <i class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></i>
            </div>
            <div class="input-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <i class="fas fa-eye" id="toggleConfirmPassword" style="cursor: pointer;"></i>
                <span class="error"><?php echo $passwordError; ?></span>
            </div>
            <input type="submit" id="btn" class="btn" name="submit" value="Register">
        </form>
        </div>
    </div>

    <script src="javascript/home.js"></script>
</body>
</html>