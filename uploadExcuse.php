<?php
session_start();
include('connect.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: student-login.php");
    exit;
}

if (isset($_GET['attendance_id'])) {
    $attendance_id = $_GET['attendance_id'];
    $query = "SELECT c.name, s.type, car.date
              FROM ClassAttendanceRecord car
              INNER JOIN Section s ON car.sectionNumber = s.sectionNumber
              INNER JOIN Course c ON s.courseID = c.id
              WHERE car.id = $attendance_id";

    $result = mysqli_query($conn, $query);

    if ($result) {
        $attendance_info = mysqli_fetch_assoc($result);
    } else {
        die("Query failed: " . mysqli_error($conn));
    }
    $_SESSION['course_name'] = $attendance_info['name'];
}

$student_id = $_SESSION['student_id'];
$checkExcuseQuery = "SELECT id FROM UploadedExcuses WHERE studentAccountID = '$student_id' AND attendanceRecordID = '$attendance_id'";
$checkExcuseResult = mysqli_query($conn, $checkExcuseQuery);

if (mysqli_num_rows($checkExcuseResult) > 0) {
    $excuseUploadedMessage = "An excuse has already been uploaded for this record.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($excuseUploadedMessage)) {
        echo $excuseUploadedMessage;
    } else {
        $absenceReason = $_POST['absenceReason'];
        $uploadedFile = $_FILES['excuseFile'];

        if ($uploadedFile['error'] === 0) {
            $fileExtension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
            $uniqueFileName = $attendance_id . '_' . $_SESSION['student_id'] . '_' . date("YmdHis") . '.' . $fileExtension;
            $fileDestination = $uniqueFileName;

            move_uploaded_file($uploadedFile['tmp_name'], $fileDestination);

            $fetchStudentAccountQuery = "SELECT id FROM studentaccount WHERE KSUID = '$student_id'";
            $fetchStudentAccountResult = mysqli_query($conn, $fetchStudentAccountQuery);

            if ($fetchStudentAccountResult && mysqli_num_rows($fetchStudentAccountResult) > 0) {
                $studentAccountRow = mysqli_fetch_assoc($fetchStudentAccountResult);
                $studentAccountID = $studentAccountRow['id'];

                $insertQuery = "INSERT INTO UploadedExcuses (studentAccountID, attendanceRecordID, absenceReason, uploadedExcuseFileName)"
                            . " VALUES ('$studentAccountID', '$attendance_id', '$absenceReason', '$fileDestination')";
                if (mysqli_query($conn, $insertQuery)) {
                    if (isset($_SESSION['course_name'])) {
                        $courseNameParam = "?course_name=" . urlencode($_SESSION['course_name']);
                        header("Location: studentsAttendane.php" . $courseNameParam);
                        exit();
                    } else {
                        echo "Error: Course name not set in session.";
                    }
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            } else {
                echo "Error: Invalid student account";
            }
        } else {
            echo "Error: File upload failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Upload Excuse</title>
</head>
<body>
    <div>
        <p>Course: <?php echo $attendance_info['name']; ?></p>
        <p>Type: <?php echo $attendance_info['type']; ?></p>
        <p>Date: <?php echo $attendance_info['date']; ?></p>
    </div>
    
    <?php
    if (isset($excuseUploadedMessage)) {
        echo "<p>$excuseUploadedMessage</p>";
    }
    ?>



<form action="uploadExcuse.php?attendance_id=<?php echo $attendance_id; ?>" method="POST" enctype="multipart/form-data">
            <label for="absenceReason">Absence Reason:</label><br>
            <textarea name="absenceReason" rows="3" cols="25" required></textarea><br><br>

            <label for="excuseFile">Excuse Document (PDF):</label><br>
            <input type="file" name="excuseFile" accept="application/pdf" required><br><br>

            <input type="submit" name="submit" value="Send">
        </form>
</body>
</html>
