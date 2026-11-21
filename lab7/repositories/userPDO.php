<?php
function findUserById($pdo, $id) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
    $stmt->execute([$id]); return $stmt->fetch();
}
function findUserByEmail($pdo, $email) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? COLLATE NOCASE');
    $stmt->execute([$email]); return $stmt->fetch();
}
function insertUser($pdo, $name, $email, $hash, $role) {
    $stmt = $pdo->prepare("INSERT INTO users(full_name,email,password_hash,role,status) VALUES(?,?,?,?,'Active')");
    $stmt->execute([$name, $email, $hash, $role]); return (int)$pdo->lastInsertId();
}
function listUsers($pdo, $search = '', $role = '', $status = '') {
    $sql = 'SELECT user_id,full_name,email,role,status FROM users WHERE 1=1';
    $args = [];
    if ($search !== '') { $sql .= ' AND (full_name LIKE ? OR email LIKE ?)'; $args[] = '%'.$search.'%'; $args[] = '%'.$search.'%'; }
    if ($role !== '') { $sql .= ' AND role = ?'; $args[] = $role; }
    if ($status !== '') { $sql .= ' AND status = ?'; $args[] = $status; }
    $stmt = $pdo->prepare($sql.' ORDER BY user_id'); $stmt->execute($args); return $stmt->fetchAll();
}
function updateUserProfile($pdo, $id, $name, $email) {
    $stmt = $pdo->prepare('UPDATE users SET full_name=?,email=? WHERE user_id=?');
    $stmt->execute([$name, $email, $id]);
}
function updatePasswordHash($pdo, $id, $hash) {
    $stmt = $pdo->prepare('UPDATE users SET password_hash=? WHERE user_id=?');
    $stmt->execute([$hash, $id]);
}
function updateUserAccess($pdo, $id, $role, $status) {
    $stmt = $pdo->prepare('UPDATE users SET role=?,status=? WHERE user_id=?');
    $stmt->execute([$role, $status, $id]);
}
function countActiveAdministrators($pdo) {
    return (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='Administrator' AND status='Active'")->fetchColumn();
}
