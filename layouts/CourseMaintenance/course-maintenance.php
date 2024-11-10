<?php


include 'db.php';


// Initialize messages
$message = '';

// Handle Save or Update Course
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $course_code = $_POST['course-code'];
        $course_category = $_POST['course-category'];
        $course_title = $_POST['course-title'];
        $course_group = $_POST['course-group'];
        $min_students = $_POST['min-students'];
        $max_students = $_POST['max-students'];
        $employee_id = $_SESSION['employee_id'];
        $name = $_SESSION['name'];
    
        if (isset($_POST['course-id'])) {
            $course_id = $_POST['course-id'];
    
            $sql = "UPDATE courses SET 
                     CourseCode='$course_code', 
                     CourseCategory='$course_category', 
                     CourseTitle='$course_title', 
                     CourseGroup='$course_group', 
                     MinStudents=$min_students, 
                     MaxStudents=$max_students 
                     WHERE CourseID=$course_id";
    
            if ($conn->query($sql) === TRUE) {
                $message = "Course updated successfully!";
                $description = "Updated course: $course_code (Title: $course_title)";
                $history_sql = "INSERT INTO coursehistory (employee_id, name, course_id, description, date) 
                                VALUES ('$employee_id', '$name', '$course_id', '$description', NOW())";
                $conn->query($history_sql);
            } else {
                $message = "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
        // Add new course
        $course_code = $_POST['course-code'];
        $course_category = $_POST['course-category'];
        $course_title = $_POST['course-title'];
        $course_group = $_POST['course-group'];
        $min_students = $_POST['min-students'];
        $max_students = $_POST['max-students'];
        $encoded_by = ''; // Example user

        $sql = "INSERT INTO courses (CourseCode, CourseCategory, CourseTitle, CourseGroup, MinStudents, MaxStudents, EncodedBy)
                VALUES ('$course_code', '$course_category', '$course_title', '$course_group', $min_students, $max_students, '$encoded_by')";

        if ($conn->query($sql) === TRUE) {
            $message = "New course added successfully!";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Store the message in session
    $_SESSION['message'] = $message;

    // Redirect to the same page without the message query parameter
    header("Location: /rescmreg/layouts/CourseMaintenance/course-maintenance.php");
    exit();
}

// Handle Delete Course
if (isset($_GET['id'])) {
    $course_id = $_GET['id'];

    // Prepare the SQL statement
    $sql = "DELETE FROM courses WHERE CourseID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $course_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Course deleted Successfully!";
    } else {
        $_SESSION['message'] = "Error deleting course: " . $conn->error;
    }

    // Redirect to avoid re-submission
    header("Location: /rescmreg/layouts/CourseMaintenance/course-maintenance.php");
    exit();
}

// Fetching Categories for Dropdown
$categories_sql = "SELECT CategoryName FROM category";
$categories_result = $conn->query($categories_sql);

// Fetching Groups for Dropdown
$groups_sql = "SELECT GroupName FROM `group`";
$groups_result = $conn->query($groups_sql);

// Fetching Course for Editing
$edit_course = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_sql = "SELECT * FROM courses WHERE CourseID=$edit_id";
    $edit_result = $conn->query($edit_sql);
    $edit_course = $edit_result->fetch_assoc();
}
// Pagination Logic
$limit = 15; // Entries per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Get filter values
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

// SQL to fetch paginated courses with filtering logic
$sql_paginated = "SELECT * FROM courses WHERE CourseCode LIKE '$filter%' LIMIT $start, $limit";
$result_paginated = $conn->query($sql_paginated);

// Get total number of courses for pagination
$total_courses_sql = "SELECT COUNT(CourseID) AS total FROM courses WHERE CourseCode LIKE '$filter%'";
$total_courses_result = $conn->query($total_courses_sql);
$total_courses_row = $total_courses_result->fetch_assoc();
$total_courses = $total_courses_row['total'];
$total_pages = ceil($total_courses / $limit);


// Function to generate page ranges (e.g., 1-15, 16-30)
function generatePageRanges($total_courses, $limit) {
    $page_ranges = [];
    $page_count = ceil($total_courses / $limit);

    for ($i = 1; $i <= $page_count; $i++) {
        $start_range = ($i - 1) * $limit + 1;
        $end_range = min($i * $limit, $total_courses);
        $page_ranges[] = "$start_range-$end_range";
    }

    return $page_ranges;
}

$page_ranges = generatePageRanges($total_courses, $limit);

// Calculate range of displayed courses
$start_course = $start + 1;
$end_course = min($start + $limit, $total_courses);

// Retrieve the message from session
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear the message from session
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Maintenance</title>
    <link rel="stylesheet" href="/rescmreg/css/course.css">
    <style>
         body, html {
        margin-top: 30px;
        font-family: Arial, sans-serif;
      }
    </style>
</style>
</style>
</head>
<body>
    <div class="container">
        <h3>COURSE MAINTENANCE</h3>

        <div id="notification" class="notification"></div>

        <form method="POST" action="">
            <?php if ($edit_course): ?>
                <input type="hidden" name="course-id" value="<?= htmlspecialchars($edit_course['CourseID']) ?>">
            <?php endif; ?>

            <div class="form-row">
                <label for="course-code">Course Code</label>
                <input type="text" id="course-code" name="course-code" value="<?= htmlspecialchars($edit_course['CourseCode'] ?? '') ?>" required>
            </div>

            <div class="form-row">
                <label for="course-category">Course Category</label>
                <div class="input-btn">
                    <select id="course-category" name="course-category" required>
                        <option value="">Select...</option>
                        <?php while($row = $categories_result->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($row['CategoryName']) ?>" <?= ($edit_course && $row['CategoryName'] === $edit_course['CourseCategory']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['CategoryName']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <a href="/rescmreg/layouts/coursemaintenance/update-category.php" class="change-btn">Update</a>
                </div>
            </div>

            <div class="form-row">
                <label for="course-title">Course Title</label>
                <input type="text" id="course-title" name="course-title" value="<?= htmlspecialchars($edit_course['CourseTitle'] ?? '') ?>" required>
            </div>

            <div class="form-row">
                <label for="course-group">Course Group</label>
                <div class="input-btn">
                    <select id="course-group" name="course-group" required>
                        <option value="">Select...</option>
                        <?php while($row = $groups_result->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($row['GroupName']) ?>" <?= ($edit_course && $row['GroupName'] === $edit_course['CourseGroup']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['GroupName']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <a href="/rescmreg/layouts/coursemaintenance/update-group.php" class="change-btn">Update</a>
                </div>
            </div>

            <div class="form-row">
                <label for="min-students">Min Students Per Class</label>
                <input type="number" id="min-students" name="min-students" value="<?= htmlspecialchars($edit_course['MinStudents'] ?? '') ?>" required>
            </div>

            <div class="form-row">
                <label for="max-students">Max Students Per Class</label>
                <input type="number" id="max-students" name="max-students" value="<?= htmlspecialchars($edit_course['MaxStudents'] ?? '') ?>" required>
            </div>  
            <div class="main-content">
                <div class="left-buttons">
                    <div class="button-container">
                    <button id="save-button" type="submit" class="save-btn" disabled><?= $edit_course ? 'Save Entry' : 'Add Entry' ?></button>
                    <span>Click to save entry</span>
                    </div>
                    <div class="button-container">
                    <button id="clear-button" class="clear-button" type="button" onclick="resetForm()">Clear / Cancel</button>
                    <span>Click to clear/cancel entry</span>
                    </div>
                    <div class="button-container">
                        <a id="pre-requisite-btn" href="#" class="update-btn prerequisite-btn">Pre-requisite</a>
                        <span>Click to update pre-requisite subject list</span>
                    </div>
                    <div class="button-container">
                    <a id="update-btn" href="/rescmreg/layouts/coursemaintenance/elective-course.php" class="update-btn elective-btn">Update</a>
                    <span>Click to update list of elective subject options</span>
                </div>
                </div>
            </div>
        </form>

        <script>
           function validateForm() {
                const courseCode = document.getElementById('course-code').value.trim();
                const courseCategory = document.getElementById('course-category').value.trim();
                const courseTitle = document.getElementById('course-title').value.trim();
                const minStudents = document.getElementById('min-students').value.trim();
                const maxStudents = document.getElementById('max-students').value.trim();

                const saveButton = document.getElementById('save-button');
                const updateBtn = document.getElementById('update-btn');
                const prerequisiteBtn = document.getElementById('pre-requisite-btn');

                // Toggle button state and class
                if (courseCode && courseCategory && courseTitle && minStudents && maxStudents) {
                    saveButton.disabled = false;
                    saveButton.classList.add('enabled');
                    updateBtn.classList.remove('disabled');
                    updateBtn.href = '/rescmreg/layouts/coursemaintenance/elective-course.php'; // Ensure href is enabled
                    prerequisiteBtn.classList.remove('disabled');
                    prerequisiteBtn.href = `/rescmreg/layouts/CourseMaintenance/pre-requisite.php?subjectCode=${encodeURIComponent(courseCode)}&subjectCategory=${encodeURIComponent(courseCategory)}&subjectTitle=${encodeURIComponent(courseTitle)}`; // Update href based on input
                } else {
                    saveButton.disabled = true;
                    saveButton.classList.remove('enabled');
                    updateBtn.classList.add('disabled');
                    updateBtn.href = '#'; // Prevent href action
                    prerequisiteBtn.classList.add('disabled');
                    prerequisiteBtn.href = '#'; // Prevent href action
                }
           }

            function resetForm() {
                // Clear all input fields
                document.getElementById('course-code').value = '';
                document.getElementById('course-category').selectedIndex = 0;
                document.getElementById('course-title').value = '';
                document.getElementById('min-students').value = '';
                document.getElementById('max-students').value = '';

                // Disable the save button and remove the enabled class
                const saveButton = document.getElementById('save-button');
                const updateBtn = document.getElementById('update-btn');
                const prerequisiteBtn = document.getElementById('pre-requisite-btn');
                saveButton.disabled = true;
                saveButton.classList.remove('enabled');
                updateBtn.classList.add('disabled');
                updateBtn.href = '#'; // Prevent href action
                prerequisiteBtn.classList.add('disabled');
                prerequisiteBtn.href = '#'; // Prevent href action
            }

            // Add event listeners to form fields to validate on input
            document.querySelectorAll('input, select').forEach(element => {
                element.addEventListener('input', validateForm);
            });

            // Add event listener to the clear button
            document.getElementById('clear-button').addEventListener('click', resetForm);

            // Initial call to disable save button if form is empty
            validateForm();
            //---------------------------------------------------

            function confirmDelete() {
                return confirm("Are you sure you want to delete this course?");
            }

            function showNotification(message) {
                const notification = document.getElementById('notification');
                notification.textContent = message;
                notification.classList.add('show');

                // Hide the notification after 5 seconds
                setTimeout(() => {
                    notification.classList.remove('show');
                }, 3000); // Display for 3 seconds
            }

            // Get the notification message from PHP
            <?php if ($message): ?>
                showNotification("<?= htmlspecialchars($message) ?>");
            <?php endif; ?>



            //---------------------------------------

            
            document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('.elective-btn').addEventListener('click', function () {
            const courseDetails = {
                code: document.getElementById('course-code').value,
                title: document.getElementById('course-title').value,
                category: document.getElementById('course-category').value
            };
            localStorage.setItem('courseDetails', JSON.stringify(courseDetails));
        });
    });
    
        </script>

        <!-- Course List with Edit/Delete -->
        <h3>LIST OF EXISTING COURSES</h3>
        <form method="GET" action="">
    <div class="controls">
        <div class="button-container">
            <button id="refresh-button" class="round-button refresh" type="submit">↻ </button>
            <div class="control-text">
                <span>To filter COURSE display, enter COURSE code/name starts with</span>
                <input type="text" id="filter-input" name="filter" value="<?= htmlspecialchars($filter) ?>">
                <span>and click REFRESH to reload the page.</span>
            </div>
        </div>
</div>

    <div class="right-buttons">
    <button class="print-btn" onclick="window.print()">
        <img src="/rescmreg/images/printers.png" alt="Print">
        <span class="button-text">Print Data</span>
    </button>
    <a href="/rescmreg/layouts/coursemaintenance/history.php" class="history-btn">
        <img src="/rescmreg/images/viewhistory.png" alt="View History">
        <span class="button-text">View History</span>
    </a>
    <form method="GET" action="">
    <div id="jump-page-wrapper">
    <label for="jump-to-page">Jump to Page: </label>
    <select id="jump-to-page" name="page" onchange="this.form.submit()">
        <?php foreach ($page_ranges as $index => $range): ?>
            <option value="<?= $index + 1 ?>" <?= ($page == $index + 1) ? 'selected' : '' ?>>
                <?= $range ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>
</div>
    </div>


        <p><b>Total Courses: <?= $total_courses ?> - Showing (<?= $start_course ?> - <?= $end_course ?>)</p></b>

        <table>
            <thead>
            <tr>
                <th>Course Code</th>
                <th>Course Title</th>
                <th>Course Category</th>
                <th>Course Group</th>
                <th>Encoded By</th>
                <th>Min Students</th>
                <th>Max tudents</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
        
            <?php while ($row = $result_paginated->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['CourseCode']) ?></td>
                    <td><?= htmlspecialchars($row['CourseTitle']) ?></td>
                    <td><?= htmlspecialchars($row['CourseCategory']) ?></td>
                    <td><?= htmlspecialchars($row['CourseGroup']) ?></td>
                    <td><?= htmlspecialchars($row['EncodedBy']) ?></td>
                    <td><?= htmlspecialchars($row['MinStudents']) ?></td>
                    <td><?= htmlspecialchars($row['MaxStudents']) ?></td>
                    <td>
                        <a href="?edit=<?= $row['CourseID'] ?>">
                            <img src="/rescmreg/images/edit.png" alt="Edit" style="width: 20px; height: 20px;">
                        </a>
                    </td>
                    <td>
                    <a href="/rescmreg/layouts/coursemaintenance/delete-course.php?id=<?= $row['CourseID'] ?>" onclick="return confirmDelete()">
                        <img src="/rescmreg/images/delete.png" alt="Delete" style="width: 20px; height: 20px;">
                    </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>
