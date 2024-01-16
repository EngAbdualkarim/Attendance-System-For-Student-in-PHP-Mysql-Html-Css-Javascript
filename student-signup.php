<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./cssfiles/student-signup.css">
	<title>student-signup</title>
	<meta name="generator" content="BBEdit 14.6" />
</head>
<body>
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
  <div id="containerS-SignUp">
    <div id="header">
      <img src="logo.png" alt="Logo">
      <div id="Back">
        <a href="Homepage.php">Back to Home Page</a>
        <br><br><br>
      </div>
    </div>
  
    <div id="signupInfo">
      <h1>Student Sign Up</h1>

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
          $query = "SELECT KSUID FROM Student WHERE KSUID = $ksuId";
          $result = $conn->query($query);
          if ($result->num_rows === 0) {
              echo "<p>KSUID not found in the students database. Please check your KSUID.</p>";
          } else {
              $query = "SELECT KSUID FROM StudentAccount WHERE KSUID = $ksuId";
              $result = $conn->query($query);
              if ($result->num_rows > 0) {
                echo "<p>KSUID is already registered. Please use a different KSUID.</p>";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $insertQuery = "INSERT INTO StudentAccount (KSUID, password) VALUES ($ksuId, '$hashedPassword')";
                if ($conn->query($insertQuery) === TRUE) {
                    $_SESSION['student_id'] = $ksuId;
                    header("Location: student.php");
                    exit;

                  } else {
                    echo "<p>Error: " . $conn->error . "</p>";
                }
            }
        }
    }
    $conn->close();
    ?>

<form action="student-signup.php" method="post" onsubmit="return validateForm()">      
      <label for="ksuId">KSU ID:</label>
        <input type="ksuId" id="ksuId" name="ksuId" required><br>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <br>
        <input type="submit" id="submit" value="Sign Up">
      </form>
    </div>
  </div>
</body>
</html>

