<?php

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/model/Student.php';
require_once __DIR__ . '/contract/IBaseRepository.php';
require_once __DIR__ . '/repositories/StudentRepository.php';
require_once __DIR__ . '/services/GradeCalculation.php';
require_once __DIR__ . '/controller/StudentManagementController.php';
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/../vendor/autoload.php';



use Controller\StudentManagementController;
use Repositories\StudentRepository;
use Core\Router;

$repository = new StudentRepository();

header('Content-Type: application/json');

Router::handleRequest();

$controller = new StudentManagementController();

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

switch ($method) {
    case 'GET':
        if ($id) {
            $controller->getStudentById($id);
        } else {
            $controller->getAllStudents();
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $controller->createStudent($data);
        break;

    case 'PUT':
        if ($id) {
            $data = json_decode(file_get_contents('php://input'), true);
            $controller->updateStudent($id, $data);
        } else {
            echo json_encode(["error" => "ID is required for updating"]);
        }
        break;

    case 'DELETE':
        if ($id) {
            $controller->deleteStudent($id);
        } else {
            echo json_encode(["error" => "ID is required for deleting"]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}
