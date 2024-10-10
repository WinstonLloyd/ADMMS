<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <img src="assets/Buena Logo.png" alt="Logo 1" class="buenalogo">
            <p>Municipal Social Welfare and Development</p>
            <img src="assets/MSWD Logo.png" alt="Logo 2" class="mswdlogo">
        </div>
        <?php
            session_start();
            include("connection.php");

            if(isset($_POST['submit'])){
                $username = mysqli_real_escape_string($con, $_POST['username']);
                $password = mysqli_real_escape_string($con, $_POST['password']);
            
                $result = mysqli_query($con, "SELECT * FROM useradmin WHERE username = '$username' AND password = '$password'") or die ("Select Error");

                $row = mysqli_fetch_assoc($result);

                if(is_array($row) && !empty($row)){
                    $_SESSION['valid'] = $row['email'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['id'] = $row['id'];
                
                    header("Location: home.php");
                } else {
                    $message = "Invalid Username or Password. <br> Please try again!";
                }
            }
        ?>
        <form action="" method="POST">
            <h2>ADMIN</h2>
            <?php if (!empty($message)) { echo '<p style="color:red;">'.$message.'</p>'; } ?>
            <div class="input-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <i class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></i>
            </div>
            <input type="submit" id="btn" name="submit" value="Sign In">
        </form>
    </div>

    <script src="javascript/index.js"></script>
</body>
</html>
