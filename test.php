<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/api/config/Database.php';
require_once __DIR__ . '/api/model/Student.php';
require_once __DIR__ . '/api/contract/IBaseRepository.php';
require_once __DIR__ . '/api/repositories/StudentRepository.php';
require_once __DIR__ . '/api/services/GradeCalculation.php';
require_once __DIR__ . '/api/controller/StudentManagementController.php';

use Repositories\StudentRepository;

$repo = new StudentRepository();

$newStudent = [
    'name'    => 'Sam Ale',
    'midterm' => 85,
    'final'   => 90
];
echo "Creating Student...\n";
$studentId = $repo->create($newStudent);
var_dump($studentId);

echo "Fetching All Students...\n";
$students = $repo->getAll();
var_dump($students);

if ($studentId) {
    echo "Fetching Student with ID: $studentId\n";
    $student = $repo->findById($studentId);
    var_dump($student);
}

$updateData = [
    'midterm' => 75,
    'final'   => 80
];
echo "Updating Student with ID: $studentId\n";
$updated = $repo->update($studentId, $updateData);
var_dump($updated);

echo "Deleting Student with ID: $studentId\n";
$deleted = $repo->delete($studentId);
var_dump($deleted);

?>
