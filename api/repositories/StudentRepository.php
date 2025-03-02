<?php

namespace repositories;

use config\Database;
use contract\IBaseRepository;
use PDO;
use services\GradeCalculation;

class StudentRepository implements IBaseRepository {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM student");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM student WHERE stud_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        if (!isset($data['name'], $data['midterm'], $data['final'])) {
            throw new \InvalidArgumentException("Missing required fields: 'name', 'midterm', 'final'.");
        }

        $finalGrade = GradeCalculation::calculateFinalGrade($data['midterm'], $data['final']);
        $status = GradeCalculation::determineStatus($finalGrade);

        $stmt = $this->conn->prepare("
            INSERT INTO student (stud_name, midterm_score, final_score, final_grade, status) 
            VALUES (:name, :midterm, :final, :finalGrade, :status)
        ");
        
        if ($stmt->execute([
            'name'       => $data['name'],
            'midterm'    => $data['midterm'],
            'final'      => $data['final'],
            'finalGrade' => $finalGrade,
            'status'     => $status
        ])) {
            return $this->conn->lastInsertId(); // Return new student ID
        }

        return false;
    }

    public function update($id, $data) {
        if (!isset($data['midterm'], $data['final'])) {
            throw new \InvalidArgumentException("Missing required fields: 'midterm', 'final'.");
        }

        $finalGrade = GradeCalculation::calculateFinalGrade($data['midterm'], $data['final']);
        $status = GradeCalculation::determineStatus($finalGrade);

        $stmt = $this->conn->prepare("
            UPDATE student 
            SET midterm_score = :midterm, final_score = :final, final_grade = :finalGrade, status = :status 
            WHERE stud_id = :id
        ");

        $stmt->execute([
            'midterm'    => $data['midterm'],
            'final'      => $data['final'],
            'finalGrade' => $finalGrade,
            'status'     => $status,
            'id'         => $id
        ]);

        return $stmt->rowCount() > 0; // Return true if a row was updated
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM student WHERE stud_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0; // Return true if a row was deleted
    }
}
