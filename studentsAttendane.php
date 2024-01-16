<?php
session_start();
include('connect.php'); 
if (!isset($_SESSION['student_id'])) {
    header("Location: student-login.php");
    exit;
}
$student_id = $_SESSION['student_id'];
if (isset($_GET['course_name'])) {
    $course_name = $_GET['course_name'];
   
}


function getAttendanceRecords($student_id, $course_name, $type) {
  global $conn; 
  $query = "SELECT car.date, sar.attendance, car.id
            FROM StudentAttendanceInRecord sar
            INNER JOIN ClassAttendanceRecord car ON sar.attendanceRecordID = car.id
            INNER JOIN Section s ON car.sectionNumber = s.sectionNumber
            INNER JOIN Course c ON s.courseID = c.id
            WHERE sar.studentKSUID = $student_id
            AND c.name = '$course_name'
            AND s.type = '$type'
            ORDER BY car.date";

  $result = mysqli_query($conn, $query);

  if (!$result) {
      die("Query failed: " . mysqli_error($conn));
  }

  return $result;
}


$lecture_records = getAttendanceRecords($student_id, $course_name, 'Lecture');

$lab_records = getAttendanceRecords($student_id, $course_name, 'Lab');

$queryExcuses = "SELECT ue.studentAccountID, ue.attendanceRecordID, ue.absenceReason, ue.uploadedExcuseFileName, ue.decision,
                        s.type AS section_type, car.date
                FROM UploadedExcuses AS ue
                INNER JOIN ClassAttendanceRecord AS car ON ue.attendanceRecordID = car.id
                INNER JOIN Section AS s ON car.sectionNumber = s.sectionNumber
                INNER JOIN StudentAccount ON ue.studentAccountID = studentaccount.id
                INNER JOIN Course AS c ON s.courseID = c.id
                WHERE studentaccount.KSUID = '$student_id'
                AND c.name = '$course_name'  -- Add this condition
                ORDER BY car.date";


$resultExcuses = mysqli_query($conn, $queryExcuses);

if (!$resultExcuses) {
    die("Query failed: " . mysqli_error($conn));
}
?>




<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./cssfiles/stusentsAttendane.css">
</head>
<body>
    <div id="container-a">
        <!-- top of the page--> 
       <div id="header-a">
         <img src="logo.png" alt="Logo-a">

         <div id="Log-out">
           <a href="logout.php">Sign Out</a>
         </div>
       </div>
       <div id="Student-Attendance">
    <p>Course: <?php echo $course_name; ?></p>
    <p>Lecture attendance</p> <br>
</div>
        <div id="title">
            Lecture Attendance
        </div>
        <table id="lecture-attendance">
            <thead id="top-attendance">
                <tr>
                    <th>Date</th>
                    <th>Attendance</th>
                    <th>Upload Excuse for Absence</th>
                </tr>
            </thead>
             <tbody>
                <?php while ($record = mysqli_fetch_assoc($lecture_records)) { ?>
                <tr>
                    <td><?php echo $record['date']; ?></td>
                    <td><?php echo ($record['attendance'] == 'attended') ? 'Attended' : 'Absent'; ?></td>
                    <td>
                        <?php if ($record['attendance'] == 'absent') { ?>
                            <a href="uploadExcuse.php?attendance_id=<?php echo $record['id']; ?>">Upload Excuse</a>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <br>
        <div id="title">
            Lab Attendance
        </div>
        <table id="lab-attendence">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Attendance</th>
                    <th>Excuse</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($record = mysqli_fetch_assoc($lab_records)) { ?>
                <tr>
                    <td><?php echo $record['date']; ?></td>
                    <td><?php echo ($record['attendance'] == 'attended') ? 'Attended' : 'Absent'; ?></td>
                    <td>
                        <?php if ($record['attendance'] == 'absent') { ?>
                            <a href="uploadExcuse.php?attendance_id=<?php echo $record['id']; ?>">Upload Excuse</a>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <br>
        <div id="title">
        Previous Absence Excuses
        </div>
        <table  id="excuse-table">
    <tr>
        <th>Class Type</th>
        <th>Date of Absence</th>
        <th>Reason of Absence</th>
        <th>Uploaded Excuse File</th>
        <th>Instructor's Decision</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($resultExcuses)) { ?>
        <tr>
            <td><?php echo $row['section_type']; ?></td>
            <td><?php echo $row['date']; ?></td>
            <td><?php echo $row['absenceReason']; ?></td>
            <td>
            <?php if (!empty($row['uploadedExcuseFileName'])) { ?>
              <a href="view_excuse.php?filename=<?php echo $row['uploadedExcuseFileName']; ?>" target="_blank"><?php echo $row['uploadedExcuseFileName']; ?></a>
              <?php } ?>
            </td>
            <td><?php echo $row['decision']; ?></td>
        </tr>
    <?php } ?>
</table>


    </div>
</body>
</html>