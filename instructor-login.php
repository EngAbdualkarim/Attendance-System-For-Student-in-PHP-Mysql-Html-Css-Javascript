<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./cssfiles/instructor-login.css">
  <title>Instructor LogIn</title>

  <script>
    function validateForm() {
      var emailInput = document.getElementById("email");
      var passwordInput = document.getElementById("password");
      var email = emailInput.value;
      var password = passwordInput.value;

      if (email.indexOf("@") === -1) {
        alert("Please enter your Email");
        return false; 
      }

      if (password === "") {
        alert("Enter Password");
        return false; 
      }
      return true; 
    }

    
  </script>
</head>
<body>
  <div id="containerI-Login">
    <div id="header">
      <img src="logo.png" alt="Logo">
      <div id="Back">
        <a href="Homepage.php">Back to Home Page</a>
        <br><br><br>
      </div>
    </div>
  
    <div id="loginInfo">
      <h1>Instructor LogIn</h1>

      <?php
include('connect.php');
session_start();
if (isset($_SESSION['instructor_id'])) {
    header("Location: InstructorHomepage.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $query = "SELECT id, password FROM Instructor WHERE email_address = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['instructor_id'] = $row['id'];
            header("Location: InstructorHomepage.php");
            exit;
        } else {
            echo "<p>Incorrect email or password. Please try again.</p>";
        }
    } else {
      
        echo "<p>Incorrect email or password. Please try again.</p>";
    }
}

$conn->close();
?>
      <form action="instructor-login.php" method="post">
        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Login">
      </form>
    </div>
  </div>
</body>
</html>