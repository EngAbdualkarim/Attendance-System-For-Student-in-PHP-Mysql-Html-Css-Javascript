
<?php
include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['sectionNumber']) && isset($_POST['selectedDate'])) {
        $sectionNumber = $_POST['sectionNumber'];
        $selectedDate = $_POST['selectedDate'];
        $getAttendanceQuery = "SELECT sair.attendance, s.KSUID, s.firstName, s.lastName
          FROM StudentAttendanceInRecord sair
          INNER JOIN Student s ON sair.studentKSUID = s.KSUID
          INNER JOIN ClassAttendanceRecord car ON sair.attendanceRecordID = car.id
          WHERE car.date = '$selectedDate' AND car.sectionNumber = $sectionNumber";

        $getAttendanceResult = $conn->query($getAttendanceQuery);

        if ($getAttendanceResult->num_rows > 0) {
            $attendanceData = array();

            while ($attendanceRow = $getAttendanceResult->fetch_assoc()) {
                $attendanceData[] = array(
                    'KSUID' => $attendanceRow['KSUID'],
                    'firstName' => $attendanceRow['firstName'],
                    'lastName' => $attendanceRow['lastName'],
                    'attendance' => $attendanceRow['attendance'],
                );
            }
            echo json_encode(array('success' => true, 'attendance' => $attendanceData));
        } else {
            echo json_encode(array('success' => false, 'error' => 'No attendance data found.'));
        }
    } else {
        echo json_encode(array('success' => false, 'error' => 'Invalid or missing parameters.'));
    }
} else {
    echo json_encode(array('success' => false, 'error' => 'Invalid request method.'));
}
?>
