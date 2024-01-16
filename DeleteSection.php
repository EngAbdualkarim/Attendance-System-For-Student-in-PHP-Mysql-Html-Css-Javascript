<?php
include('connect.php'); 

if (isset($_GET['section']) && is_numeric($_GET['section'])) {
    $sectionNumber = $_GET['section'];
    $deleteSectionQuery = "DELETE FROM Section WHERE sectionNumber = ?";
    $stmt = $conn->prepare($deleteSectionQuery);

    if ($stmt) {
        $stmt->bind_param("i", $sectionNumber);

        if ($stmt->execute()) {
            echo "Section with section number $sectionNumber has been deleted successfully.";
            header("Location: InstructorHomepage.php");
            exit;
        } else {
            echo "Error deleting section: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error in preparing the delete statement: " . $conn->error;
    }
} else {
    echo "Invalid section number provided.";
}

$conn->close(); 
?>
