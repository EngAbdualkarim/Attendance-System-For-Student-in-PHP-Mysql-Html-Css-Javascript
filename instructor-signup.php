<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./cssfiles/instructor-signup.css">
  <title>Instructor Sign Up</title>
</head>
<body>
  <div id="containerI-SignUp">
    <div id="header">
      <img src="logo.png" alt="Logo">
      <div id="Back">
        <a href="Homepage.php">Back to Home Page</a>
        <br><br><br>
      </div>
    </div>

    <div id="signupInfo">
      <h1>Instructor Sign Up</h1>

      <?php
include('connect.php'); 
session_start();

if (isset($_SESSION['instructor_id'])) {
    header("Location: InstructorHomepage.php");
    exit; 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Fname = $_POST['Fname'];
    $Lname = $_POST['Lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT id FROM Instructor WHERE email_address = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "<p>Email address already exists. Please use a different email.</p>";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $insertQuery = "INSERT INTO Instructor (first_name, last_name, email_address, password) 
                        VALUES ('$Fname', '$Lname', '$email', '$hashedPassword')";
        if ($conn->query($insertQuery) === TRUE) {
            $_SESSION['instructor_id'] = $conn->insert_id;
            header("Location: InstructorHomepage.php");
        } else {
            echo "<p>Error: " . $conn->error . "</p>";
        }
    }
}
$conn->close();
?>


      <form action="instructor-signup.php" method="post">
        <label for="Fname">First Name:</label>
        <input type="text" id="Fname" name="Fname" required><br><br>

        <label for="Lname">Last Name:</label>
        <input type="text" id="Lname" name="Lname" required><br><br>

        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" id="submit" value="Sign Up">
      </form>
    </div>
  </div>
</body>
</html>
