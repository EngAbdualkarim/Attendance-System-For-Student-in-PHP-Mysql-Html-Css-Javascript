<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./cssfiles/student-login.css">
  <title>Student LogIn</title>

  <script>
    function validateForm() {
  var ksuIdInput = document.getElementById("ksuId");
  var passwordInput = document.getElementById("password");
  var ksuId = ksuIdInput.value;
  var password = passwordInput.value;

  if (ksuId.length !== 9 || isNaN(ksuId)) {
    alert("KSU ID should be an 9-digit number");
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
        <a href="homepage.php">Back to Home Page</a>
        <br><br><br>
      </div>
    </div>
  
    <div id="loginInfo">
      <h1>Student LogIn</h1>

      <?php
      include('connect.php');
      session_start();
      if (isset($_SESSION['student_id'])) {
          header("Location: student.php");
          exit;
      }
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
          $ksuId = $_POST['ksuId'];
          $password = $_POST['password'];
          $query = "SELECT KSUID, password FROM StudentAccount WHERE KSUID = $ksuId";
          $result = $conn->query($query);

          if ($result->num_rows === 1) {
              $row = $result->fetch_assoc();
              $hashedPassword = $row['password'];
              if (password_verify($password, $hashedPassword)) {
                  $_SESSION['student_id'] = $ksuId;
                  header("Location: student.php");
                  exit;
              } else {
                  echo "<p>Invalid KSU ID or password. Please try again.</p>";
              }
            } else {
              echo "<p>Invalid KSU ID or password. Please try again.</p>";
          }
      }
      $conn->close();
      ?>

      <form id="loginForm" action="student-login.php" method="POST" onsubmit="return validateForm()">
        
        <label for="ksuId">KSU ID:</label>
        <input type="ksuId" id="ksuId" name="ksuId" required><br>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <br>
        <button id="logins">LogIn</button>
      </form>
    </div>
  </div>
</body>
</html>