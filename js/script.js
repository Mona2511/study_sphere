// script.js - simple client side validation (server side validation is also done in PHP)

// Register form
var registerForm = document.getElementById("registerForm");
if (registerForm) {
    registerForm.onsubmit = function () {
        var name = document.getElementById("name").value.trim();
        var email = document.getElementById("email").value.trim();
        var password = document.getElementById("password").value;
        var confirm = document.getElementById("confirm_password").value;

        if (name == "" || email == "" || password == "") {
            alert("Please fill all the fields.");
            return false;
        }
        if (password.length < 6) {
            alert("Password must be at least 6 characters long.");
            return false;
        }
        if (password != confirm) {
            alert("Passwords do not match.");
            return false;
        }
        return true;
    };
}

// Login form
var loginForm = document.getElementById("loginForm");
if (loginForm) {
    loginForm.onsubmit = function () {
        var email = document.getElementById("email").value.trim();
        var password = document.getElementById("password").value;

        if (email == "" || password == "") {
            alert("Please enter email and password.");
            return false;
        }
        return true;
    };
}

// Add subject form
var subjectForm = document.getElementById("subjectForm");
if (subjectForm) {
    subjectForm.onsubmit = function () {
        if (subjectForm.subject_name.value.trim() == "") {
            alert("Please enter the subject name.");
            return false;
        }
        return true;
    };
}

// Add task form
var taskForm = document.getElementById("taskForm");
if (taskForm) {
    taskForm.onsubmit = function () {
        if (taskForm.title.value.trim() == "" || taskForm.due_date.value == "") {
            alert("Please enter task title and due date.");
            return false;
        }
        return true;
    };
}
