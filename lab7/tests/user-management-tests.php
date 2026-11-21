<?php
// Isolated requirements tests: never connects to database/tutoring.db.
require_once dirname(__DIR__).'/services/userService.php';
require_once dirname(__DIR__).'/services/requestService.php';
require_once dirname(__DIR__).'/includes/auth.php';
$results = []; $failed = 0;
function check($condition, $label) {
    global $results, $failed;
    $results[] = ($condition ? 'PASS ' : 'FAIL ').$label;
    if (!$condition) $failed++;
}
function rejects($action, $label) {
    try { $action(); check(false, $label); }
    catch (InvalidArgumentException $e) { check(true, $label); }
}
try {
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec(file_get_contents(dirname(__DIR__).'/database/setup-database.sql'));
    $pdo->exec(file_get_contents(dirname(__DIR__).'/database/upgrade-to-user-management.sql'));
    $input = ['full_name'=>'Test Student', 'email'=>'student@example.com',
        'password'=>'ClassroomPass123!', 'confirm_password'=>'ClassroomPass123!', 'role'=>'Student'];
    $id = registerUser($pdo, $input); $student = findUserById($pdo, $id);
    check($student['status'] === 'Active' && $student['role'] === 'Student', 'UM-01 registration creates an Active student');
    check($student['password_hash'] !== $input['password'] && password_verify($input['password'], $student['password_hash']), 'Password stored as verifiable hash');
    check((int)findStudentByUserId($pdo, $id)['user_id'] === $id, 'Student profile links to users.user_id');
    rejects(function() use ($pdo,$input) { registerUser($pdo,$input); }, 'Duplicate email rejected');
    $case = $input; $case['email'] = 'STUDENT@example.com';
    rejects(function() use ($pdo,$case) { registerUser($pdo,$case); }, 'Email uniqueness is case insensitive');
    $bad = $input; $bad['email']='bad-role@example.com'; $bad['role']='Administrator';
    rejects(function() use ($pdo,$bad) { registerUser($pdo,$bad); }, 'Public administrator registration denied');
    $bad['role']='Student'; $bad['password']='short'; $bad['confirm_password']='short';
    rejects(function() use ($pdo,$bad) { registerUser($pdo,$bad); }, 'Password shorter than 12 characters denied');
    $bad=$input; $bad['email']='mismatch@example.com'; $bad['confirm_password']='DifferentPass123';
    rejects(function() use ($pdo,$bad) { registerUser($pdo,$bad); }, 'Password confirmation mismatch denied');
    $bad=$input; $bad['email']='maria.lopez@example.com';
    rejects(function() use ($pdo,$bad) { registerUser($pdo,$bad); }, 'Legacy student record cannot be claimed through registration');
    check((int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() === 1, 'Invalid registrations write no user records');
    $tutorInput=$input; $tutorInput['email']='tutor@example.com'; $tutorInput['role']='Tutor';
    $tutorId=registerUser($pdo,$tutorInput); $tutor=findUserById($pdo,$tutorId);
    check(!findStudentByUserId($pdo,$tutorId), 'Tutor registration does not create student profile');
    rejects(function() use ($tutor) { requestOwnerScope($tutor); }, 'Tutor request workflow denied');
    check((int)authenticateUser($pdo,'STUDENT@example.com',$input['password'])['user_id'] === $id, 'UM-09 valid login verifies credentials');
    rejects(function() use ($pdo) { authenticateUser($pdo,'student@example.com','incorrect'); }, 'Incorrect login password denied');
    $adminInput=$input; $adminInput['email']='admin@example.com'; $adminInput['role']='Administrator';
    $adminId=createAccount($pdo,$adminInput,true); $admin=findUserById($pdo,$adminId);
    check(count(listUsers($pdo,'student@example.com','Student','Active')) === 1, 'UM-03 administrator search repository filters accounts');
    saveOwnProfile($pdo,$id,['full_name'=>'Revised Test Student','email'=>'revised@example.com']);
    check(findUserById($pdo,$id)['full_name'] === 'Revised Test Student'
        && findStudentByUserId($pdo,$id)['student_email'] === 'revised@example.com', 'UM-04 account and student profiles update together');
    rejects(function() use ($pdo,$id) { saveOwnProfile($pdo,$id,['full_name'=>'Test','email'=>'revised@example.com','role'=>'Administrator']); }, 'Profile cannot change role');
    rejects(function() use ($pdo,$id) { saveOwnProfile($pdo,$id,['full_name'=>'Test','email'=>'admin@example.com']); }, 'Profile cannot take another account email');
    rejects(function() use ($pdo,$id) { changeOwnPassword($pdo,$id,['current_password'=>'wrong','password'=>'NewClassPass123!','confirm_password'=>'NewClassPass123!']); }, 'UM-05 incorrect current password denied');
    changeOwnPassword($pdo,$id,['current_password'=>$input['password'],'password'=>'NewClassPass123!','confirm_password'=>'NewClassPass123!']);
    check((int)authenticateUser($pdo,'revised@example.com','NewClassPass123!')['user_id'] === $id, 'UM-05 new password works');
    rejects(function() use ($pdo,$input) { authenticateUser($pdo,'revised@example.com',$input['password']); }, 'UM-05 previous password no longer works');
    rejects(function() use ($pdo,$id,$tutorId) { manageUserAccess($pdo,$id,$tutorId,'Administrator','Active'); }, 'UM-06 nonadministrator role change denied');
    manageUserAccess($pdo,$adminId,$tutorId,'Student','Active');
    check(findUserById($pdo,$tutorId)['role'] === 'Student' && !!findStudentByUserId($pdo,$tutorId), 'UM-06 changing Tutor to Student creates linked profile');
    manageUserAccess($pdo,$adminId,$id,'Student','Inactive');
    rejects(function() use ($pdo) { authenticateUser($pdo,'revised@example.com','NewClassPass123!'); }, 'UM-07 inactive account cannot log in');
    manageUserAccess($pdo,$adminId,$id,'Student','Active');
    rejects(function() use ($pdo,$adminId) { manageUserAccess($pdo,$adminId,$adminId,'Tutor','Active'); }, 'Last active administrator cannot be demoted');
    rejects(function() use ($pdo,$adminId,$id) { deleteUserAccount($pdo,$adminId,$id); }, 'UM-08 deletion blocked by unresolved retention policy');
    check(!!findUserById($pdo,$id), 'Blocked deletion retains account');
    $student=findUserById($pdo,$id); $other=findUserById($pdo,$tutorId);
    $request=['student_name'=>'Forged Name','student_email'=>'forged@example.com', 'course'=>'IS 6465',
        'reason'=>'Please help with validation and authentication.', 'topics'=>['Study plan'],
        'preferreddate'=>(new DateTimeImmutable('+10 days'))->format('Y-m-d'), 'preferredtime'=>'Morning'];
    $requestId=submitAuthenticatedRequest($pdo,$student,$request);
    check(findRequest($pdo,$requestId,$id)['student_email'] === 'revised@example.com', 'Request identity comes from authenticated account');
    check(!findRequest($pdo,$requestId,$tutorId), 'Student cannot read another student request');
    check(count(listRequests($pdo,'','',$tutorId)) === 0, 'Student request list excludes other owners');
    check(!reviseAuthenticatedRequest($pdo,$other,$requestId,$request), 'Student cannot update another student request');
    check(!cancelOpenRequest($pdo,$requestId,$tutorId), 'Student cannot cancel another student request');
    check(!!findRequest($pdo,$requestId,requestOwnerScope($admin)), 'Administrator can view another student request');
    check(reviseAuthenticatedRequest($pdo,$student,$requestId,$request), 'Owner can edit own Open request');
    check(cancelOpenRequest($pdo,$requestId,$id), 'Owner can cancel own Open request');
    check(!reviseAuthenticatedRequest($pdo,$student,$requestId,$request), 'Cancelled request cannot be edited');
    // Buffer results until all session actions finish, avoiding headers-already-sent.
    startSecureSession(); $before=session_id(); establishLogin($student);
    check(session_id() !== $before && (int)$_SESSION['user_id'] === $id, 'Login regenerates session ID and stores identity');
    check(!validCsrfToken([]) && !validCsrfToken(['csrf_token'=>'forged']), 'Missing and invalid CSRF tokens rejected');
    check(validCsrfToken(['csrf_token'=>csrfToken()]), 'Valid CSRF token accepted');
    logoutSession();
    check($_SESSION === [] && session_status() !== PHP_SESSION_ACTIVE, 'UM-10 logout clears server session');
} catch (Throwable $e) {
    $results[]='FAIL setup or unexpected exception: '.$e->getMessage(); $failed++;
}
foreach ($results as $line) echo $line.PHP_EOL;
echo 'Results: '.(count($results)-$failed).' passed, '.$failed.' failed'.PHP_EOL;
exit($failed ? 1 : 0);
