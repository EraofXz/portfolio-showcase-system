document.addEventListener("DOMContentLoaded", function () {
    // 1. Client-Side Validation for Register Form
    const registerForm = document.getElementById("registerForm");
    if (registerForm) {
        registerForm.addEventListener("submit", function (e) {
            const fullName = document.getElementById("full_name")?.value.trim();
            const email = document.getElementById("email")?.value.trim();
            const password = document.getElementById("password")?.value;
            const confirmPassword = document.getElementById("confirm_password")?.value;

            if (!fullName || !email || !password || !confirmPassword) {
                alert("Please insert all required fields!");
                e.preventDefault();
                return;
            }

            if (password.length < 6) {
                alert("Password must be at least 6 characters long!");
                e.preventDefault();
                return;
            }

            if (password !== confirmPassword) {
                alert("Password and Confirm Password do not match!");
                e.preventDefault();
                return;
            }
        });
    }

    // 2. Client-Side Validation to Submit Project (File & Size)
    const submitProjectForm = document.getElementById("submitProjectForm");
    if (submitProjectForm) {
        submitProjectForm.addEventListener("submit", function (e) {
            const fileInput = document.getElementById("documentation_file");
            if (fileInput && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const allowedExtensions = ["pdf", "docx", "zip"];
                const fileExtension = file.name.split(".").pop().toLowerCase();
                const maxSize = 5 * 1024 * 1024; // 5MB

                if (!allowedExtensions.includes(fileExtension)) {
                    alert("Invalid file type! Only PDF, DOCX, or ZIP formats are allowed.");
                    e.preventDefault();
                    return;
                }

                if (file.size > maxSize) {
                    alert("File size exceeds the 5MB limit!");
                    e.preventDefault();
                    return;
                }
            }
        });
    }
});