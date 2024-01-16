<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./cssfiles/InstructorHomepage.css">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <title>Instructor Homepage</title>
  <?php
  include('connect.php');
  session_start();

  if (!isset($_SESSION['instructor_id'])) {
      header("Location: instructor-login.php");
      exit;
  }

  $instructorId = $_SESSION['instructor_id'];
  $instructorInfoQuery = "SELECT first_name, last_name, email_address FROM Instructor WHERE id = $instructorId";
  $instructorInfoResult = $conn->query($instructorInfoQuery);
  if ($instructorInfoResult->num_rows > 0) {
      $instructorInfo = $instructorInfoResult->fetch_assoc();
  }
  ?>
</head>
<body>
  <div id="container">
    <!-- top of the page -->
    <div id="header">
      <img src="logo.png" alt="Logo">
      <div id="sign-out">
        <a href="logout.php">Log Out</a>
      </div>
    </div>
    <div id="welcome">
      Welcome, <span id="first-name"><?php echo $instructorInfo['first_name']; ?></span>!
    </div>

    <!-- Instructor information -->
    <div id="instructor-info">
      <p>Name: <?php echo $instructorInfo['first_name'] . " " . $instructorInfo['last_name']; ?></p>
      <p>Email: <?php echo $instructorInfo['email_address']; ?></p>
    </div>

    <!-- Courses table -->
    <h3>Sections Teaching</h3>
    <a href="AddNweSec1.php">Add New Section</a>
    <table id="courses-table">
      <thead id="texthead">
        <tr>
          <th>Section</th>
          <th>Course</th>
          <th>Type</th>
          <th>Hours</th>
          <th>Attendance Record</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <!-- Retrieve and display sections taught by the instructor from the database -->
        <?php
        $sectionsQuery = "SELECT S.sectionNumber, C.name, S.type, S.hours 
                          FROM Section AS S 
                          JOIN Course AS C ON S.courseID = C.id 
                          WHERE S.instructorID = $instructorId";
        $sectionsResult = $conn->query($sectionsQuery);

        while ($section = $sectionsResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $section['sectionNumber'] . "</td>";
            echo "<td>" . $section['name'] . "</td>";
            echo "<td>" . $section['type'] . "</td>";
            echo "<td>" . $section['hours'] . "</td>";
            echo '<td><a href="InstructorAttendanceRecordPage.php?section=' . $section['sectionNumber'] . '">Attendance</a></td>';
            echo '<td><a href="DeleteSection.php?section=' . $section['sectionNumber'] . '">Delete</a></td>';
            echo "</tr>";
        }
        ?>
      </tbody>
    </table>
    <br>

    <h3>Uploaded Excuses for Absence</h3>
    <table id="courses-table">
      <thead id="texthead">
        <tr>
          <th>Section</th>
          <th>Student Name </th>
          <th>Student ID</th>
          <th>Absence Reason</th>
          <th>Upload excuse
          </th>
          <th>Date</th>

        </tr>
      </thead>
      <tbody>
     
<?php
$excusesQuery = "SELECT UE.id, CA.sectionNumber, S.firstName, S.lastName, SA.KSUID, UE.absenceReason, UE.uploadedExcuseFileName, CA.date
                 FROM UploadedExcuses AS UE
                 JOIN ClassAttendanceRecord AS CA ON UE.attendanceRecordID = CA.id
                 JOIN StudentAccount AS SA ON SA.id = UE.studentAccountID
                 JOIN Student AS S ON SA.KSUID = S.KSUID
                 JOIN Section AS SEC ON SEC.sectionNumber = CA.sectionNumber
                 WHERE SEC.instructorID = $instructorId AND UE.decision IS NULL";
$excusesResult = $conn->query($excusesQuery);

while ($excuse = $excusesResult->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $excuse['sectionNumber'] . "</td>";
    echo "<td>" . $excuse['firstName'] . " " . $excuse['lastName'] . "</td>";
    echo "<td>" . $excuse['KSUID'] . "</td>";
    echo "<td>" . $excuse['absenceReason'] . "</td>";
    echo '<td><a href="' . $excuse['uploadedExcuseFileName'] . '">' . $excuse['uploadedExcuseFileName'] . '</a></td>'; 
    echo "<td>" . $excuse['date'] . '</td>';
    echo '<td><a href="#" class="approve-link" data-excuse-id="' . $excuse['id'] . '">Approve</a></td>';
    echo '<td><a href="#" class="disapprove-link" data-excuse-id="' . $excuse['id'] . '">Disapprove</a></td>';
    echo "</tr>";
}
?>
      </tbody>
    </table>
  </div>


  <script>
  $(document).ready(function () {
    $(".approve-link").on("click", function (e) {
      e.preventDefault();
      var excuseId = $(this).data("excuse-id");

      $.ajax({
        type: "GET",
        url: "HandleApproval.php",
        data: { excuseId: excuseId, decision: "Approve" },
        dataType: 'json',
        success: function (data) {
          if (data.success) {
            $("tr[data-excuse-id='" + excuseId + "']").remove();
           location.reload();
          } else {
            alert("Error approving excuse. Please try again. " + data.error);
          }
        },
      });
    });


    $(".disapprove-link").on("click", function (e) {
      e.preventDefault();
      var excuseId = $(this).data("excuse-id");

      $.ajax({
        type: "GET",
        url: "HandleApproval.php",
        data: { excuseId: excuseId, decision: "Disapprove" },
        dataType: 'json',
        success: function (data) {
          if (data.success) {
            $("tr[data-excuse-id='" + excuseId + "']").remove();
           location.reload();
          } else {
            alert("Error disapproving excuse. Please try again. " + data.error);
          }
        },
      });
    });
  });
</script>

</body>
</html>
