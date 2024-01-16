<?php
include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ksuid'])) {
    $ksuid = $_POST['ksuid'];

    $checkStudentQuery = "SELECT firstName, lastName FROM Student WHERE KSUID = '$ksuid'";
    $checkStudentResult = $conn->query($checkStudentQuery);

    if ($checkStudentResult->num_rows > 0) {
        $student = $checkStudentResult->fetch_assoc();
        echo $student['firstName'] . ' ' . $student['lastName'];
    } else {
        echo "No student with this ID";
    }
} else {

    echo "Invalid request";
}
?>
ss