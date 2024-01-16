<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./cssfiles/AddNewSec2.css">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <title>Add new sec2</title>
</head>
<body>
  <div id="container">
    <div id="header">
      <img src="logo.png" alt="Logo">
    </div>
  
    <h3>Add new Section</h3>
    <h2>Step2 Add Students to Section</h2>
    <form method="post" action="AddNewSec2.php">
    <h3 id="inputt"> KSU ID: <input type="text" name="KSUID" id="KSUID" required> Name <input type="text" name="studentName" id="studentName" readonly> <button type="submit" name="add">Add</button></h3>
  </form>
    <h3>Student list</h3>
    <table id="courses-table">
      <thead id="texthead">
        <tr>
          <th>KSU ID</th>
          <th>Name</th>
        </tr>
      </thead>
      <tbody>
        <?php
        session_start();
        include('connect.php');

        if (!isset($_SESSION['instructor_id'])) {
            header("Location: instructor-login.php");
            exit;
        }

  

if (isset($_POST['add'])) {
    $sectionNumber = $_SESSION['sectionNumber'];
    $KSUID = $_POST['KSUID'];
    
    $checkStudentInSectionQuery = "SELECT studentKSUID FROM SectionStudents WHERE sectionNumber = '$sectionNumber' AND studentKSUID = '$KSUID'";
    $checkStudentInSectionResult = $conn->query($checkStudentInSectionQuery);

    if ($checkStudentInSectionResult->num_rows > 0) {
        echo "Student with KSUID $KSUID is already in this section.";
    } else {
        $checkStudentQuery = "SELECT firstName, lastName FROM Student WHERE KSUID = '$KSUID'";
        $checkStudentResult = $conn->query($checkStudentQuery);

        if ($checkStudentResult->num_rows > 0) {
            $student = $checkStudentResult->fetch_assoc();
            $studentName = $student['firstName'] . ' ' . $student['lastName'];

            $addStudentQuery = "INSERT INTO SectionStudents (sectionNumber, studentKSUID) VALUES ('$sectionNumber', '$KSUID')";
            if ($conn->query($addStudentQuery) === TRUE) {
                $getStudentsQuery = "SELECT Student.KSUID, Student.firstName, Student.lastName
                                    FROM SectionStudents
                                    INNER JOIN Student ON SectionStudents.studentKSUID = Student.KSUID
                                    WHERE SectionStudents.sectionNumber = '$sectionNumber'";
                $getStudentsResult = $conn->query($getStudentsQuery);

                if ($getStudentsResult->num_rows > 0) {
                    while ($student = $getStudentsResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$student['KSUID']}</td>";
                        echo "<td>{$student['firstName']} {$student['lastName']}</td>";
                        echo "</tr>";
                    }
                }
            } else {
                echo "Error: " . $addStudentQuery . "<br>" . $conn->error;
            }
        } else {
            echo "Student with KSUID $KSUID not found.";
        }
    }
}
?>

      </tbody>
    </table>
    <form action="InstructorHomepage.php" method="post">
      <button id="Done" type="submit" name="done">Done</button>
    </form>
  </div>

  <script>
    $(document).ready(function () {
      $("#KSUID").on("keyup", function () {
        var ksuid = $(this).val();
        if (ksuid.length < 9) {
          $("#studentName").val("Invalid KSUID");
          return;
        }
        $.ajax({
          type: "POST",
          url: "CheckStudent.php", 
          data: { ksuid: ksuid },
          success: function (data) {
            $("#studentName").val(data);
          },
        });
      });
    });
  </script>
</body>
</html>
