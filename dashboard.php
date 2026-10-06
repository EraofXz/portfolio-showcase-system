<?php
require_once 'includes/header.php';

// Ensure only logged-in users can access this page
if (!$is_logged_in) {
    header("Location: login.php");
    exit();
}
?>

<div class="container py-4">
    <!-- Welcome Message (Figure 3) -->
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div>
            Welcome back, <strong><?= htmlspecialchars($user_name) ?></strong>!
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Title Header & Button Submit -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1">Project Showcase Directory</h2>
            <p class="text-muted mb-0">Explore student portfolio submissions and FYP projects.</p>
        </div>
        <div>
            <a href="submit_project.php" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Submit New Project
            </a>
        </div>
    </div>

    <!-- Search Bar AJAX Live Search (Figure 3, 9, 10) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-2">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="liveSearchInput" class="form-control border-0 shadow-none ps-2" 
                       placeholder="Live search by project title, tech stack, or student name...">
            </div>
        </div>
    </div>

    <!-- Project Container (Loaded asynchronously by AJAX) -->
    <div class="row" id="projectContainer">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>

<!-- AJAX Script using Fetch API -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("liveSearchInput");
    const container = document.getElementById("projectContainer");

    // Function to fetch data from fetch_projects.php
    function loadProjects(query = "") {
        fetch(`fetch_projects.php?search=${encodeURIComponent(query)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error("Network error while loading project.");
                }
                return response.text();
            })
            .then(html => {
                container.innerHTML = html;
            })
            .catch(error => {
                console.error("AJAX Error:", error);
                container.innerHTML = `<div class="col-12 text-center text-danger py-4">Failed to load data. Please try again.</div>`;
            });
    }

    // Load data on initial page load
    loadProjects();

    // Event listener direct (input without refresh)
    searchInput.addEventListener("input", function () {
        loadProjects(this.value.trim());
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>