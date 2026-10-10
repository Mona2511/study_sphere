// app.js - small scripts shared by all redesigned pages

// ---------- 1. Dark / light theme ----------
var themeBtn = document.getElementById("themeBtn");
var html = document.documentElement;

// Sun when the theme is dark (click = go light), moon when it is light
function updateThemeButton() {
    if (html.getAttribute("data-theme") === "dark") {
        themeBtn.textContent = "\u2600";
    } else {
        themeBtn.textContent = "\u263E";
    }
}

if (themeBtn) {
    themeBtn.addEventListener("click", function () {
        var newTheme;
        if (html.getAttribute("data-theme") === "dark") {
            newTheme = "light";
            html.removeAttribute("data-theme");
        } else {
            newTheme = "dark";
            html.setAttribute("data-theme", "dark");
        }
        // remember the choice in the browser (works on every page)
        try { localStorage.setItem("theme", newTheme); } catch (err) {}
        updateThemeButton();
    });
    updateThemeButton();
}

// ---------- 2. Mobile sidebar (only on pages that have a sidebar) ----------
var menuBtn = document.getElementById("menuBtn");
var sidebar = document.getElementById("sidebar");
var overlay = document.getElementById("overlay");

if (menuBtn && sidebar && overlay) {
    function closeMenu() {
        sidebar.classList.remove("open");
        overlay.classList.remove("show");
    }

    menuBtn.addEventListener("click", function () {
        if (sidebar.classList.contains("open")) {
            closeMenu();
        } else {
            sidebar.classList.add("open");
            overlay.classList.add("show");
        }
    });

    overlay.addEventListener("click", closeMenu);
}

// ---------- 3. Task checkboxes ----------
// Ticking a checkbox submits its form to update_task.php (marks done / undo).
document.querySelectorAll(".task-check").forEach(function (box) {
    box.addEventListener("change", function () {
        box.form.submit();
    });
});

// ---------- 4. "Read more" on long notes ----------
document.querySelectorAll(".read-more").forEach(function (btn) {
    btn.addEventListener("click", function () {
        var body = btn.parentElement.querySelector(".note-body");
        body.classList.toggle("clamp");
        if (body.classList.contains("clamp")) {
            btn.textContent = "Read more";
        } else {
            btn.textContent = "Show less";
        }
    });
});

// ---------- 5. "Show password" checkbox on login / register ----------
var showPass = document.getElementById("showPass");

if (showPass) {
    showPass.addEventListener("change", function () {
        var type = showPass.checked ? "text" : "password";
        document.querySelectorAll(".pass-field").forEach(function (input) {
            input.type = type;
        });
    });
}
