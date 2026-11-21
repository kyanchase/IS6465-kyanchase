<?php
function findStudentByEmail($pdo, $email) {
    $stmt = $pdo->prepare('SELECT * FROM students WHERE student_email = ? COLLATE NOCASE');
    $stmt->execute([$email]); return $stmt->fetch();
}
function findStudentByUserId($pdo, $id) {
    $stmt = $pdo->prepare('SELECT * FROM students WHERE user_id=?');
    $stmt->execute([$id]); return $stmt->fetch();
}
function insertLinkedStudent($pdo, $id, $name, $email) {
    $stmt = $pdo->prepare('INSERT INTO students(user_id,student_name,student_email) VALUES(?,?,?)');
    $stmt->execute([$id, $name, $email]); return (int)$pdo->lastInsertId();
}
function updateLinkedStudent($pdo, $id, $name, $email) {
    $stmt = $pdo->prepare('UPDATE students SET student_name=?,student_email=? WHERE user_id=?');
    $stmt->execute([$name, $email, $id]);
}
function insertStudentIfAbsent($pdo, $name, $email) {
    $stmt = $pdo->prepare('INSERT OR IGNORE INTO students(student_name,student_email) VALUES(?,?)');
    $stmt->execute([$name,$email]);
}
