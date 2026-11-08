# Lab 5 Notes

## Part 1: Create the database tables

### Table and schema output

```text
sqlite> .tables
students  tutoring_requests

sqlite> .schema
CREATE TABLE students (student_id INTEGER PRIMARY KEY, student_name TEXT NOT NULL, student_email TEXT NOT NULL UNIQUE);
CREATE TABLE tutoring_requests (request_id INTEGER PRIMARY KEY, student_id INTEGER NOT NULL, course TEXT NOT NULL, topic TEXT NOT NULL, preferred_date TEXT NOT NULL, preferred_time TEXT NOT NULL, status TEXT NOT NULL DEFAULT 'Open', created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (student_id) REFERENCES students(student_id));
```

### Which column is the primary key in each table?

The primary key in students is student_id. The primary key in tutoring_requests is request_id. Each primary key uniquely identifies one row in its table.

### Which column is the foreign key, and what relationship does it represent?

The foreign key is student_id in tutoring_requests. It references students(student_id), connecting each request to an existing student. One student can have many tutoring requests, and each request belongs to one student.

### Why is student_email marked UNIQUE?

student_email is marked UNIQUE to prevent multiple student records from using the same email address. This helps the system identify the correct student without creating duplicate records.

## Part 2: Insert students and tutoring requests

### INSERT statements used

```sql
INSERT INTO students (student_name, student_email)
VALUES ('Jordan Lee', 'jordan.lee@example.edu');

INSERT INTO students (student_name, student_email)
VALUES ('Avery Patel', 'avery.patel@example.edu');

INSERT INTO tutoring_requests
    (student_id, course, topic, preferred_date, preferred_time)
VALUES (1, 'IS 6465', 'Forms', '2026-11-16', '14:00');

INSERT INTO tutoring_requests
    (student_id, course, topic, preferred_date, preferred_time)
VALUES (1, 'IS 6465', 'SQL SELECT', '2026-11-17', '15:00');

INSERT INTO tutoring_requests
    (student_id, course, topic, preferred_date, preferred_time)
VALUES (2, 'IS 6465', 'SQLite CRUD', '2026-11-18', '13:00');
```

### SELECT output

```text
sqlite> SELECT * FROM students;
1|Jordan Lee|jordan.lee@example.edu
2|Avery Patel|avery.patel@example.edu

sqlite> SELECT * FROM tutoring_requests;
1|1|IS 6465|Forms|2026-11-16|14:00|Open|2026-11-07 23:33:53
2|1|IS 6465|SQL SELECT|2026-11-17|15:00|Open|2026-11-07 23:34:05
3|2|IS 6465|SQLite CRUD|2026-11-18|13:00|Open|2026-11-07 23:34:22
```

### Why does Jordan have one student row but two request rows?

Jordan has one student row because he is one student, but he has two request rows because he requested two sessions. Both requests use his student_id of 1, so his name and email only need to be stored once.

## Part 3: Read the stored data

### JOIN query and output

```sql
SELECT r.request_id, s.student_name, s.student_email,
       r.course, r.topic, r.preferred_date, r.preferred_time, r.status
FROM tutoring_requests AS r
JOIN students AS s ON r.student_id = s.student_id
ORDER BY r.preferred_date;
```

```text
1|Jordan Lee|jordan.lee@example.edu|IS 6465|Forms|2026-11-16|14:00|Open
2|Jordan Lee|jordan.lee@example.edu|IS 6465|SQL SELECT|2026-11-17|15:00|Open
3|Avery Patel|avery.patel@example.edu|IS 6465|SQLite CRUD|2026-11-18|13:00|Open
```

### What does the JOIN condition connect?

The JOIN condition connects tutoring_requests.student_id to students.student_id. This lets each request display the name and email of the student who submitted it.

### What is the difference between the purpose of JOIN and WHERE?

JOIN combines related records from different tables using matching values. WHERE filters the results to rows that meet a condition.

### Why should the application retrieve one request by request_id instead of by topic alone?

The application should retrieve a request by request_id because it uniquely identifies one request. Multiple requests can have the same topic, so searching by topic alone could return several records.

## Part 4: Update one request

### UPDATE statements

```sql
UPDATE tutoring_requests
SET topic = 'Forms and validation', preferred_time = '15:00'
WHERE request_id = 1;

UPDATE tutoring_requests
SET status = 'Cancelled'
WHERE request_id = 3;
```

### Jordan's request before the update

```text
1|1|IS 6465|Forms|2026-11-16|14:00|Open|2026-11-07 23:33:53
```

### Jordan's request after the update

```text
1|1|IS 6465|Forms and validation|2026-11-16|15:00|Open|2026-11-07 23:33:53
```

### Avery's request after cancellation

```text
3|2|IS 6465|SQLite CRUD|2026-11-18|13:00|Cancelled|2026-11-07 23:34:22
```

### Why is a specific WHERE clause essential in an UPDATE statement?

A specific WHERE clause limits the UPDATE to the intended request. Without it, every row in the table could be changed.

### Why does the Peer Tutoring Hub use a Cancelled status instead of immediately deleting a real request?

A Cancelled status preserves the request history, including who submitted it and what they requested. The system can exclude it from open requests while keeping a record of the cancellation.

## Part 5: Delete one test request

### Test request INSERT and DELETE

```sql
INSERT INTO tutoring_requests
    (student_id, course, topic, preferred_date, preferred_time)
VALUES (1, 'IS 6465', 'Delete Practice', '2026-11-19', '14:00');

DELETE FROM tutoring_requests WHERE request_id = 4;
```

### Test request before deletion

```text
4|1|IS 6465|Delete Practice|2026-11-19|14:00|Open|2026-11-07 23:58:29
```

### Verification after deletion

```sql
SELECT * FROM tutoring_requests;
```

```text
1|1|IS 6465|Forms and validation|2026-11-16|15:00|Open|2026-11-07 23:33:53
2|1|IS 6465|SQL SELECT|2026-11-17|15:00|Open|2026-11-07 23:34:05
3|2|IS 6465|SQLite CRUD|2026-11-18|13:00|Cancelled|2026-11-07 23:34:22
```

### Why is a WHERE clause essential in a DELETE statement?

A WHERE clause limits DELETE to the intended record. Without it, every row in the table would be deleted.

## Part 6: Reflect on the CRUD workflow

| CRUD action | SQL statement used in this lab | Peer Tutoring Hub example |
|---|---|---|
| Create | INSERT | Add a student or tutoring request. |
| Read | SELECT | Display saved requests with student information. |
| Update | UPDATE | Revise a request or change its status to Cancelled. |
| Delete | DELETE | Remove the Delete Practice test request. |

### How does the database preserve the connection between a student and that student's requests?

Each tutoring request stores a student_id foreign key that references students(student_id). This links each request to its student and allows one student to have multiple requests.

### Which database constraint prevents two student rows from using the same email address?

The UNIQUE constraint on student_email prevents two student rows from using the same email address.

### What could happen if an UPDATE or DELETE statement did not include a WHERE clause?

An UPDATE without WHERE could change every row in the table. A DELETE without WHERE could remove every row from the table.

### When the application later receives values from an HTML form, why should PHP send those values as parameters instead of placing them directly into SQL text?

PHP should use parameters to keep submitted values separate from SQL instructions. This helps prevent SQL injection, where an attacker tries to make input execute as SQL to read or change data without permission.