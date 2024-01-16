<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./cssfiles/InstructorAttendanceRecordPage.css">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <title>Attendance Record</title>
</head>
<body>
  <div id="container">
    <div id="header">
      <img src="logo.png" alt="Logo">
      <div id="sign-out">
        <a href="logout.php">log-out</a>
      </div>
    </div>
    
    <h1>Attendance Record</h1>
    <h3>Section Information</h3>
    
    <?php
    include('connect.php');
    session_start();

    if (!isset($_SESSION['instructor_id'])) {
        header("Location: instructor-login.php");
        exit;
    }

    if (isset($_GET['section'])) {
        $sectionNumber = $_GET['section'];
    }

if (isset($_POST['save-btn'])) {
  $classDate = $_POST['class-date'];
  $checkRecordQuery = "SELECT id FROM ClassAttendanceRecord WHERE sectionNumber = $sectionNumber AND date = '$classDate'";
  $checkRecordResult = $conn->query($checkRecordQuery);

  if ($checkRecordResult->num_rows > 0) {
      echo "A record for this date already exists. Please choose a different date.";
  } else {
      $insertClassAttendanceQuery = "INSERT INTO ClassAttendanceRecord (sectionNumber, date) VALUES ($sectionNumber, '$classDate')";
      
      if ($conn->query($insertClassAttendanceQuery) === TRUE) {
          $attendanceRecordID = $conn->insert_id;
          $getStudentsQuery = "SELECT ss.studentKSUID FROM SectionStudents ss WHERE ss.sectionNumber = $sectionNumber";
          $getStudentsResult = $conn->query($getStudentsQuery);

          if ($getStudentsResult->num_rows > 0) {
              while ($studentRow = $getStudentsResult->fetch_assoc()) {
                  $studentKSUID = $studentRow['studentKSUID'];
                  $attendance = $_POST['attendance'][$studentKSUID]; 

                  $insertStudentAttendanceQuery = "INSERT INTO StudentAttendanceInRecord (attendanceRecordID, studentKSUID, attendance) VALUES ($attendanceRecordID, $studentKSUID, '$attendance')";

                  if ($conn->query($insertStudentAttendanceQuery) !== TRUE) {
                      echo "Error: " . $insertStudentAttendanceQuery . "<br>" . $conn->error;
                  }
              }
          }
      } else {
          echo "Error: " . $insertClassAttendanceQuery . "<br>" . $conn->error;
      }
  }
}

?>


    <div id="student-info">
        <h3>Display Attendance for Previous Class</h3>
        <form method="post" action="InstructorAttendanceRecordPage.php?section=<?php echo $sectionNumber; ?>">
        <label for="select-date">Select date:</label>
          <select name="select-date" id="select-date">
            <?php
            $getDatesQuery = "SELECT date FROM ClassAttendanceRecord WHERE sectionNumber = $sectionNumber";
            $getDatesResult = $conn->query($getDatesQuery);

            if ($getDatesResult->num_rows > 0) {
                while ($dateRow = $getDatesResult->fetch_assoc()) {
                    echo "<option value='" . $dateRow['date'] . "'>" . $dateRow['date'] . "</option>";
                }
            }
            ?>
          </select>
          <table id="courses-table">
            <thead id="texthead">
              <tr>
                <th>KSU ID</th>
                <th>Name</th>
                <th>Attendance</th>
              </tr>
            </thead>
            <tbody  id="attendance-table-body">
            </tbody>
          </table>
        </form>
    </div>
    
    <div id="student-info">
        <h3>Add New Class Attendance</h3>
        <form method="post" action="InstructorAttendanceRecordPage.php?section=<?php echo $sectionNumber; ?>">
            <input type="hidden" name="sectionNumber" value="<?php echo $sectionNumber; ?>">
            <label for="class-date">Class Date:</label>
            <input type="date" name="class-date" required>
            <table id="courses-table">
                <thead id="texthead">
                  <tr>
                    <th>KSU ID</th>
                    <th>Name</th>
                    <th>Attendance</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $getStudentsQuery = "SELECT ss.studentKSUID, s.firstName, s.lastName
                    FROM SectionStudents ss
                    INNER JOIN Student s ON ss.studentKSUID = s.KSUID
                    WHERE ss.sectionNumber = $sectionNumber";
                  $getStudentsResult = $conn->query($getStudentsQuery);
                  
                  if ($getStudentsResult->num_rows > 0) {
                    while ($studentRow = $getStudentsResult->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td>" . $studentRow['studentKSUID'] . "</td>";
                      echo "<td>" . $studentRow['firstName'] . " " . $studentRow['lastName'] . "</td>";
                      echo "<td>";
                      echo "<select name='attendance[" . $studentRow['studentKSUID'] . "]'>";
                      echo "<option value='attended'>Attended</option>";
                      echo "<option value='absent'>Absent</option>";
                      echo "</select>";
                      echo "</td>";
                      echo "</tr>";
                    }
                  }
                  ?>
                </tbody>
            </table>
            <button id="save-btn" type="submit" name="save-btn">Save</button>
        </form>
    </div>
  </div>

  <script>
    $(document).ready(function () {
      $("#select-date").on("change", function () {
        var selectedDate = $(this).val();
        $.ajax({
          type: "POST",
          url: "HandleAttendanceUpdate.php", 
          data: {
            sectionNumber: <?php echo $sectionNumber; ?>,
            selectedDate: selectedDate
          },
          dataType: 'json',
          success: function (data) {
            if (data.success) {
              var attendanceTableBody = $("#attendance-table-body");
              attendanceTableBody.empty();
              $.each(data.attendance, function (index, row) {
                var tr = $("<tr>");
                tr.append("<td>" + row.KSUID + "</td>");
                tr.append("<td>" + row.firstName + " " + row.lastName + "</td>");
                tr.append("<td>" + row.attendance + "</td>");
                attendanceTableBody.append(tr);
              });
            } else {
              alert("Error updating attendance. Please try again. " + data.error);
            }
          },
        });
      });
      
      $("#select-date").change();
    });
  </script>
</body>
</html>
