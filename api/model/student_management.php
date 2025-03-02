<?php
require 'stud_database.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Calculate Final Grade
function calculateGrade($midterm, $final) {
    return (0.4 * $midterm) + (0.6 * $final);
}

// Helper to Send JSON Response
function sendResponse($status, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit();
}

// Handle API Requests
$request = $_SERVER['REQUEST_METHOD'];

// Ping Endpoint (for testing API connection)
if ($request === 'GET' && isset($_GET['ping'])) {
    sendResponse(200, "API is working");
}

// Add Student
elseif ($request === 'POST' && isset($_GET['add'])) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['stud_name'], $data['midterm_score'], $data['final_score'])) {
        sendResponse(400, "Missing required fields");
    }

    $name = $data['stud_name'];
    $midterm = floatval($data['midterm_score']);
    $final = floatval($data['final_score']);

    $stmt = $conn->prepare("INSERT INTO student (stud_name, midterm_score, final_score) VALUES (?, ?, ?)");
    $stmt->bind_param("sdd", $name, $midterm, $final);

    if ($stmt->execute()) {
        sendResponse(201, "Student added successfully!");
    } else {
        sendResponse(500, "Failed to add student");
    }
}

// Get All Students
elseif ($request === 'GET' && isset($_GET['all'])) {
    $result = $conn->query("SELECT * FROM student");
    $students = [];

    while ($row = $result->fetch_assoc()) {
        $row['final_grade'] = calculateGrade($row['midterm_score'], $row['final_score']);
        $row['status'] = $row['final_grade'] >= 75 ? 'Pass' : 'Fail';
        $students[] = $row;
    }

    sendResponse(200, "Students retrieved successfully", $students);
}

// Get Student by ID
elseif ($request === 'GET' && isset($_GET['stud_id'])) {
    $id = intval($_GET['stud_id']);

    $stmt = $conn->prepare("SELECT * FROM student WHERE stud_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if (!$student) sendResponse(404, "Student not found");

    $student['final_grade'] = calculateGrade($student['midterm_score'], $student['final_score']);
    $student['status'] = $student['final_grade'] >= 75 ? 'Pass' : 'Fail';

    sendResponse(200, "Student found", $student);
}

// Update Student
elseif ($request === 'PUT' && isset($_GET['stud_id'])) {
    $id = intval($_GET['stud_id']);
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['midterm_score'], $data['final_score'])) {
        sendResponse(400, "Missing required fields");
    }

    $midterm = floatval($data['midterm_score']);
    $final = floatval($data['final_score']);

    $stmt = $conn->prepare("UPDATE student SET midterm_score = ?, final_score = ? WHERE stud_id = ?");
    $stmt->bind_param("ddi", $midterm, $final, $id);

    if ($stmt->execute()) {
        sendResponse(200, "Student updated successfully");
    } else {
        sendResponse(500, "Failed to update student");
    }
}

// Delete Student
elseif ($request === 'DELETE' && isset($_GET['stud_id'])) {
    $id = intval($_GET['stud_id']);

    $stmt = $conn->prepare("DELETE FROM student WHERE stud_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        sendResponse(200, "Student deleted successfully");
    } else {
        sendResponse(500, "Failed to delete student");
    }
}

// Invalid Request
else {
    sendResponse(400, "Invalid request");
}
?>
