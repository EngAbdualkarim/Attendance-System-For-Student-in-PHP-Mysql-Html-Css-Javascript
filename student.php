<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./cssfiles/stuent.css">
  <title>Student Homepage</title>
</head>
<body>
  <div id="container">
    <!-- top of the page--> 
    <div id="header">
      <img src="logo.png" alt="Logo">
      <div id="sign-out">
        <a href="logout.php">Sign Out</a>
      </div>
    </div>
    <div id="welcome">
      Welcome, 
      <?php
      session_start();
      include('connect.php');
      if (!isset($_SESSION['student_id'])) {
          header("Location: student-login.php");
          exit;
      }
      if (isset($_SESSION['student_id'])) {
        $student_id = $_SESSION['student_id'];
        $query = "SELECT firstName, lastName FROM Student WHERE KSUID = $student_id";
        $result = mysqli_query($conn, $query);
        
        if ($row = mysqli_fetch_assoc($result)) {
          $student_name = $row['firstName'] . ' ' . $row['lastName'];
          echo $student_name;
        }
        
        mysqli_close($conn);
      } else {
        echo "Guest!";
      }
      ?>
      
    </div>


    <div id="student-info">
      <p>Name: 
      <?php
      if (isset($student_name)) {
        echo $student_name;
      }
      ?>
      </p>
      <p>KSU ID: 
      <?php
      if (isset($_SESSION['student_id'])) {
        echo $_SESSION['student_id'];
      }
      ?>
      </p>
    </div>


    <table id="courses-table">
      <thead id="texthead">
        <tr>
          <th>Course</th>
          <th>Attendance Record</th>
          <th>Percentage of Absence</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (isset($_SESSION['student_id'])) {
          include('connect.php');
          $student_id = $_SESSION['student_id'];

          $query = "SELECT Course.id, Course.name  AS course_name,Course.symbol, Section.type, Section.hours
          FROM Course
          JOIN Section ON Course.id = Section.courseID
          JOIN SectionStudents ON Section.sectionNumber = SectionStudents.sectionNumber
          WHERE SectionStudents.studentKSUID = $student_id";


          $result = mysqli_query($conn, $query);
          
          while ($row = mysqli_fetch_assoc($result)) {

            $totalHours = 0;
            $totalAbsentHours = 0;
                        
            $course_name = $row['course_name'];
            $section_type = $row['type'];
            $course_symbol = $row['symbol']; 
            $section_hours = $row['hours'];
            $course_id = $row['id']; 

            $query2 = "SELECT ClassAttendanceRecord.date, StudentAttendanceInRecord.attendance
            FROM ClassAttendanceRecord
            LEFT JOIN StudentAttendanceInRecord ON ClassAttendanceRecord.id = StudentAttendanceInRecord.attendanceRecordID
            WHERE ClassAttendanceRecord.sectionNumber IN (
                SELECT sectionNumber FROM SectionStudents
                WHERE studentKSUID = $student_id
            )";
 
            $result2 = mysqli_query($conn, $query2);
            
            while ($row2 = mysqli_fetch_assoc($result2)) {
         


              $attendance = $row2['attendance'];
              $date = $row2['date'];
              
              if ($section_type == 'lecture') {
                $totalHours += 1;
                if ($attendance == 'absent') {
                  $totalAbsentHours += 1;
                }
              } else {
                $totalHours += 0.5;
                if ($attendance == 'absent') {
                  $totalAbsentHours += 0.5;
                }
              }
            }
            
            $percentage = ($totalAbsentHours / $totalHours) * 100;
            
            echo "<tr>";
            echo "<td>$course_symbol <br> $course_name</td>";
            echo "<td><a href='studentsAttendane.php?course_name=$course_name'>View Attendance</a></td>";
            echo "<td>$percentage%</td>";
            echo "</tr>";
          }
          mysqli_close($conn);
        }
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
