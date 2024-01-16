<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./cssfiles/AddNweSec1.css">
  <title>Add new section</title>
</head>
<body>
  <div id="container">
    <!-- top of the page -->
    <div id="header">
      <img src="logo.png" alt="Logo">
    </div>

    <!-- Student information -->
    <h3>Add new Section</h3>
    <h2>Step 1: Add Students to Section</h2>

    <form method="post" action="AddNweSec1.php">
      <h3>Course:
        <select name="course" id="course">
          <?php
          include('connect.php');
          session_start();

          if (!isset($_SESSION['instructor_id'])) {
              header("Location: instructor-login.php");
              exit;
          }

          $instructorId = $_SESSION['instructor_id'];

          $coursesQuery = "SELECT id, name FROM Course";
          $coursesResult = $conn->query($coursesQuery);

          while ($course = $coursesResult->fetch_assoc()) {
              echo "<option value='" . $course['id'] . "'>" . $course['name'] . "</option>";
          }
          ?>
        </select>
      </h3>

      <h3>Section: <input type="text" name="section_number" required></h3>
      <h3>Hours: <input type="text" name="hours" required></h3>

      <h3>Type:
        <input type="radio" id="lap" name="type" value="Lap" reqired>
        <label for="lap">Lap</label>
        <input type="radio" id="lecture" name="type" value="Lecture" reqired>
        <label for="lecture">Lecture</label>
      </h3>

      <input type="submit" value="Add Section">
    </form>

    <?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseId = $_POST['course'];
    $sectionNumber = $_POST['section_number'];
    $hours = $_POST['hours'];
    $type = $_POST['type'];


    $checkSectionQuery = "SELECT sectionNumber FROM Section WHERE sectionNumber = '$sectionNumber'";
    $checkSectionResult = $conn->query($checkSectionQuery);

    if ($checkSectionResult->num_rows > 0) {
        echo "Section with the provided sectionNumber already exists.";
    } else {
        $insertSectionQuery = "INSERT INTO Section (courseID, sectionNumber, type, hours, instructorID)
                              VALUES ('$courseId', '$sectionNumber', '$type', '$hours', '$instructorId')";
        if ($conn->query($insertSectionQuery) === TRUE) {
            echo "Section added successfully.";
            $_SESSION['sectionNumber'] = $sectionNumber;
            header("Location: AddNewSec2.php");
            exit; 
        } else {
            echo "Error: " . $insertSectionQuery . "<br>" . $conn->error;
        }
    }
}
?>
  </div>
</body>
</html>
