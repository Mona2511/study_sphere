# StudySphere

A simple study management website built as a Web Technology mini project.
Students can register, add their subjects, write notes and keep track of assignments.

**Technologies:** HTML, CSS, JavaScript, PHP, MySQL (XAMPP)

## How to run

1. Install and open XAMPP. Start **Apache** and **MySQL**.
2. Copy the `studysphere` folder to `C:\xampp\htdocs\`.
3. Open `http://localhost/phpmyadmin`, click **Import**, choose `database.sql` and click Go.
4. Open `http://localhost/studysphere/` in the browser.
5. Register a new account and login.

If your MySQL root user has a password, change it in `config.php`.

## Modules

| Module | Files | What it does |
|---|---|---|
| Login system | register.php, login.php, logout.php | Register, login and logout using sessions |
| Dashboard | dashboard.php | Counts of subjects, notes, tasks, upcoming tasks |
| Subjects | subjects.php, edit_subject.php, delete_subject.php | Add, edit, delete subjects |
| Notes | notes.php, add_note.php, edit_note.php, delete_note.php | Subject-wise notes with search and filter |
| Tasks | tasks.php, update_task.php, delete_task.php | Add tasks with due date, mark done, overdue highlight |

## Folder structure

```
studysphere/
├── config.php          database connection + helper functions
├── database.sql        tables
├── index.php           home page
├── includes/           header, footer, login check
├── css/style.css
├── js/script.js        form validation
└── (all the module pages)
```

## Database tables

- **users** (id, name, email, password, created_at)
- **subjects** (id, user_id, subject_name, teacher, created_at)
- **notes** (id, user_id, subject_id, title, content, created_at)
- **tasks** (id, user_id, subject_id, title, due_date, status, created_at)

`user_id` is a foreign key in every table so each student sees only their own data.
Deleting a subject deletes its notes (`ON DELETE CASCADE`). Deleting a subject does not delete
tasks, the task just shows "-" for subject (`ON DELETE SET NULL`).

## Viva preparation

**Q. How does login work?**
After checking the email and password, we store the user's id and name in `$_SESSION`. Every
protected page includes `auth_check.php`, which redirects to login if `$_SESSION['user_id']` is not set.

**Q. How are passwords stored?**
Using `password_hash()` before inserting, so the real password is never saved. At login we use
`password_verify()` to compare.

**Q. How did you prevent SQL injection?**
All queries that use user input are written with prepared statements (`mysqli_prepare`,
`mysqli_stmt_bind_param`), so the input is treated as data and not as SQL.

**Q. How did you prevent XSS?**
Everything printed from the database or the user goes through `e()` which calls `htmlspecialchars()`.

**Q. Why `WHERE user_id = ?` in every query?**
So one student cannot see, edit or delete another student's data, even by changing the id in the URL.

**Q. Why are delete buttons inside a POST form?**
A delete link (GET) can be triggered accidentally or by another site. A POST form is safer, and
JavaScript `confirm()` asks the user before submitting.

**Q. What is the role of JavaScript here?**
Only client-side validation (empty fields, password length, password match). PHP also validates
again on the server because JavaScript can be disabled.

**Q. What is a JOIN and where did you use it?**
JOIN combines rows from two tables. In notes we join `notes` with `subjects` to show the subject
name. We used LEFT JOIN for tasks because a task may have no subject.

**Q. How does search work in notes?**
SQL `LIKE '%word%'` on title and content. The subject filter uses `(? = 0 OR n.subject_id = ?)`
so value 0 means all subjects.

**Q. How does "Mark Done" work?**
`update_task.php` runs `UPDATE tasks SET status = IF(status='Pending','Completed','Pending')`.

**Q. How is the overdue task found?**
In PHP we compare the task's due date with today's date: `$t['due_date'] < date('Y-m-d')` and status is Pending.

## Possible future improvements

- Profile page and change password
- Forgot password with email
- File upload for notes (PDF)
- Study timetable
