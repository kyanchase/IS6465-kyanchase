<?php
require_once dirname(__DIR__).'/repositories/userPDO.php';
require_once dirname(__DIR__).'/repositories/studentPDO.php';

function validateAccountFields($input) {
    $name = trim((string)($input['full_name'] ?? ''));
    $email = strtolower(trim((string)($input['email'] ?? '')));
    if ($name === '' || mb_strlen($name) > 120) throw new InvalidArgumentException('Name must contain 1 to 120 characters.');
    if (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Enter a valid email address.');
    return [$name, $email];
}
function validatePassword($password, $confirmation) {
    // 12 characters is the lab minimum; 72 bytes avoids silent bcrypt truncation.
    if (mb_strlen($password) < 12 || strlen($password) > 72 || strpos($password, "\0") !== false) {
        throw new InvalidArgumentException('Password must contain at least 12 characters and no more than 72 bytes.');
    }
    if ($password !== $confirmation) throw new InvalidArgumentException('Passwords must match.');
}
function createAccount($pdo, $input, $allowAdministrator = false) {
    list($name, $email) = validateAccountFields($input);
    $password = (string)($input['password'] ?? '');
    validatePassword($password, (string)($input['confirm_password'] ?? ''));
    $role = (string)($input['role'] ?? 'Student');
    $allowed = $allowAdministrator ? ['Student','Tutor','Administrator'] : ['Student','Tutor'];
    if (!in_array($role, $allowed, true)) throw new InvalidArgumentException('Public registration permits Student or Tutor only.');
    $pdo->beginTransaction();
    try {
        if (findUserByEmail($pdo, $email)) throw new InvalidArgumentException('An account already uses that email address.');
        // Unlinked Lab 6 records are not automatically claimed using an unverified email.
        if ($role === 'Student' && findStudentByEmail($pdo, $email)) {
            throw new InvalidArgumentException('A legacy student record uses that email. Use a different fictional email for this lab.');
        }
        $id = insertUser($pdo, $name, $email, password_hash($password, PASSWORD_DEFAULT), $role);
        if ($role === 'Student') insertLinkedStudent($pdo, $id, $name, $email);
        $pdo->commit(); return $id;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack(); throw $e;
    }
}
function registerUser($pdo, $input) { return createAccount($pdo, $input, false); }
function authenticateUser($pdo, $email, $password) {
    $user = findUserByEmail($pdo, strtolower(trim($email)));
    if (!$user || $user['status'] !== 'Active' || !password_verify($password, $user['password_hash'])) {
        throw new InvalidArgumentException('Invalid email or password.');
    }
    if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
        updatePasswordHash($pdo, $user['user_id'], password_hash($password, PASSWORD_DEFAULT));
    }
    return $user;
}
function saveOwnProfile($pdo, $userId, $input) {
    list($name, $email) = validateAccountFields($input);
    if (isset($input['role']) || isset($input['status'])) throw new InvalidArgumentException('Role and status cannot be edited through a profile.');
    $pdo->beginTransaction();
    try {
        $other = findUserByEmail($pdo, $email);
        $student = findStudentByEmail($pdo, $email);
        if (($other && (int)$other['user_id'] !== (int)$userId)
            || ($student && (int)($student['user_id'] ?? 0) !== (int)$userId)) {
            throw new InvalidArgumentException('That email address is already in use.');
        }
        updateUserProfile($pdo, $userId, $name, $email);
        updateLinkedStudent($pdo, $userId, $name, $email);
        $pdo->commit();
    } catch (Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
}
function changeOwnPassword($pdo, $userId, $input) {
    $user = findUserById($pdo, $userId);
    if (!$user || !password_verify((string)($input['current_password'] ?? ''), $user['password_hash'])) {
        throw new InvalidArgumentException('Current password is incorrect.');
    }
    $password = (string)($input['password'] ?? '');
    validatePassword($password, (string)($input['confirm_password'] ?? ''));
    updatePasswordHash($pdo, $userId, password_hash($password, PASSWORD_DEFAULT));
}
function manageUserAccess($pdo, $actorId, $targetId, $role, $status) {
    if (!in_array($role, ['Student','Tutor','Administrator'], true)
        || !in_array($status, ['Active','Inactive'], true)) throw new InvalidArgumentException('Invalid role or status.');
    $pdo->beginTransaction();
    try {
        $actor = findUserById($pdo, $actorId);
        $target = findUserById($pdo, $targetId);
        if (!$actor || $actor['role'] !== 'Administrator' || $actor['status'] !== 'Active') throw new InvalidArgumentException('Administrator access required.');
        if (!$target) throw new InvalidArgumentException('User not found.');
        if ($target['role'] === 'Administrator' && $target['status'] === 'Active'
            && ($role !== 'Administrator' || $status !== 'Active') && countActiveAdministrators($pdo) <= 1) {
            throw new InvalidArgumentException('The last active administrator must be retained.');
        }
        if ($role === 'Student' && !findStudentByUserId($pdo, $targetId)) {
            if (findStudentByEmail($pdo, $target['email'])) throw new InvalidArgumentException('Student email conflicts with a legacy record.');
            insertLinkedStudent($pdo, $targetId, $target['full_name'], $target['email']);
        }
        updateUserAccess($pdo, $targetId, $role, $status);
        $pdo->commit();
    } catch (Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
}
function deleteUserAccount($pdo, $actorId, $targetId) {
    $actor = findUserById($pdo, $actorId);
    if (!$actor || $actor['role'] !== 'Administrator' || $actor['status'] !== 'Active') throw new InvalidArgumentException('Administrator access required.');
    if (!findUserById($pdo, $targetId)) throw new InvalidArgumentException('User not found.');
    throw new InvalidArgumentException('Deletion is blocked until a retention policy is approved. Deactivate the account instead.');
}
