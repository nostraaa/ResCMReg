<?php
function renderMenu() {
    $menu = [
        "Dashboard" => [
            "icon" => "bi-house", 
            "link" => "dashboard.html"
        ],
        "Student Main Record" => [
            "icon" => "bi-file-earmark-text",
            "sub-menu" => [
                "Student Schedule" => "student-schedule.php",
                "Modification of Data" => "modification.php",
                "Residency Status" => "/rescmreg/layouts/coursemaintenance/residency-status.php"
            ],
        ],
        "Course Maintenance" => [
            "icon" => "bi-journal", 
            "link" => "/rescmreg/layouts/coursemaintenance/course-maintenance.php"
        ],
        "Curriculum Maintenance" => [
            "icon" => "bi-book",  
            "sub-menu" => [
                "Program" => "program.php"
            ]
        ],
        "Grades Management" => [
            "icon" => "bi-pencil",  
            "sub-menu" => [
                "Rectification of Grades" => "/rescmreg/layouts/gradesmanagement/RectificationOfGrades.php",
                "Unlocking of Grades" => "unlocking-of-grades.php"
            ]
        ],
        "Reports" => [
            "icon" => "bi-file-earmark",  
            "sub-menu" => [
                "List of Graduating" => "list-of-graduating.php"
            ]
        ]
    ];

    foreach ($menu as $key => $value) {
        if (isset($value['link'])) {
            echo "<li><a href='#' onclick='loadContent(\"{$value['link']}\", this, [\"$key\"])'><i class='bi {$value['icon']}'></i> <span class='menu-text'>$key</span></a></li>";
        } elseif (isset($value['sub-menu'])) {
            echo "<li class='has-sub-menu'><a href='#' class='menu-item' onclick='toggleSubMenu(this.parentNode)'><i class='bi {$value['icon']}'></i> <span class='menu-text'>$key</span> <i class='bx bx-chevron-down'></i></a>";
            echo "<ul class='sub-menu' style='display: none;'>";
            foreach ($value['sub-menu'] as $subKey => $subValue) {
                echo "<li><a href='#' onclick='loadContent(\"{$subValue}\", this, handleMenuClick(\"$key\", \"$subKey\"))'>$subKey</a></li>";
            }
            echo "</ul></li>";
        }
    }

    echo "<li class='profile-item'><a href='#' onclick='showProfile()'><img src='/rescmreg/images/profile.jpg' class='profile-image' alt='Profile Image'> <span class='menu-text'>Profile</span> <i class='bi bi-bell' style='float:right;'></i></a></li>";
    echo "<li class='logout-item' onclick='logout()'><a href='#'><i class='bi bi-box-arrow-right'></i> <span class='menu-text'>Log Out</span></a></li>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collapsible Sidebar</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.1/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="navbar.css">

    <style>
        .sidebar {
            position: fixed;
            width: 250px;
            height: 100vh;
            background-color: #ffffff;
            transition: all 0.3s ease;
            overflow-y: auto; /* Enable vertical scrolling */
            z-index: 1000; /* Ensure it stays above other content */
        }
        .sidebar.collapsed {
            width: 80px;
        }
        .main-content {
            margin-left: 250px;
            transition: all 0.3s ease;
            padding: 20px;
            overflow-y: auto; /* Enable vertical scrolling */
            height: 100vh; /* Make sure main content takes full height */
        }
        .main-content.collapsed {
            margin-left: 80px;
        }
        #contentFrame {
            width: 100%;
            height: calc(100vh - 80px); /* Adjust based on your layout */
            border: none; /* Remove default iframe border */
            overflow: hidden; /* Prevent scrollbars in iframe */
            background-color: #fff; /* Optional background color */
        }

        /* Hide scrollbar for WebKit browsers (Chrome, Safari) */
        #contentFrame::-webkit-scrollbar {
            display: none; /* Hides the vertical and horizontal scrollbar */
        }

        /* Hide scrollbar for Firefox */
        #contentFrame {
            scrollbar-width: none; /* Hides the scrollbar */
        }

        /* Profile Section */
        .profile-item a {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
            font-family: 'Poppins', sans-serif;
            transition: background-color 0.3s ease;
        }

        .profile-item a:hover {
            background-color: #e0e0e0;
            border-radius: 5px;
        }

        .profile-image {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        /* Notification Icon */
        .notification-icon {
            margin-left: auto;
            font-size: 18px;
            color: #666;
        }

        /* Sub-menu styling */
        .sub-menu {
            list-style-type: none;
            padding-left: 20px;
            display: none;
        }

        .sub-menu li a {
            padding: 8px 15px;
            font-size: 14px;
        }

        /* Toggle icon rotation */
        .rotate {
            transform: rotate(180deg);
        }

        /* Logout button */
        .logout-item a {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
            font-family: 'Poppins', sans-serif;
            transition: background-color 0.3s ease;
        }

        .logout-item a:hover {
            background-color: #e0e0e0;
            border-radius: 5px;
        }
        .breadcrumbs {
            position: fixed; /* Keeps the breadcrumbs fixed on the top */
            width: 92%; /* Full width */
            font-size: 14px;
            margin: 0; /* Removes margin */
            padding: 10px; /* Adds padding */
            background-color: #f8f9fa; /* Background color */
            border: 1px solid #dee2e6; /* Border to match existing style */
            border-radius: 7px; /* Remove border radius for a more streamlined look */
        }

        .breadcrumbs a {
            text-decoration: none;
            color: #17416A; 
        }

        .breadcrumbs a:hover {
            color: white;
            background-color: #17416A;
            border-radius: 5px;
            padding: 5px;
        }

        .breadcrumb .active {
            font-weight: bold; 
            color: #17416A; 
        }
        

    </style>
</head>
<body>
    <nav class="navbar">
        <div id="hamburgerIcon" class="hamburger-icon">
            <i class="bx bx-menu" onclick="toggleSidebar()"></i>
        </div>
        <div class="logo_item">
            <img src="/rescmreg/images/NEU Logo.png" alt="NEU Logo">
            <span class="neu-university">NEW ERA UNIVERSITY</span> NEU SYSTEM
        </div>
    </nav>

    <div id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <div id="collapseToggle" class="collapse-icon" onclick="toggleSidebar()">
                <i class="bx bx-arrow-from-right" id="collapseIcon"></i>
            </div>
        </div>
        <ul class="nav_list">
            <?php renderMenu(); ?>
        </ul>
    </div>

    <div id="mainContent" class="main-content">
        <div id="breadcrumbs" class="breadcrumbs"></div>
        <iframe id="contentFrame" src=""></iframe> <!-- Frame for dynamic content -->
    </div>

    <script>
        const breadcrumbContainer = document.getElementById('breadcrumbs');

        function updateBreadcrumbs(path) {
            breadcrumbContainer.innerHTML = path.map((item, index) => {
                // Create a breadcrumb link for all but the last item
                return index < path.length - 1 ? `<a href="#" onclick="goToBreadcrumb(${index})">${item}</a>` : `<span class="active">${item}</span>`;
            }).join(' > ');
        }

        function goToBreadcrumb(index) {
            // Logic to navigate back to a specific breadcrumb level
            const path = getBreadcrumbPath(index);
            updateBreadcrumbs(path);
            loadContent(path[path.length - 1]); // Load the content corresponding to the clicked breadcrumb
        }

        function getBreadcrumbPath(index) {
            // Get the current breadcrumb trail as an array
            return breadcrumbTrail.slice(0, index + 1);
        }

        let breadcrumbTrail = [];

        function loadContent(page, menuItem, breadcrumbPath) {
            const contentFrame = document.getElementById('contentFrame');
            contentFrame.src = page;

            // Update active menu item
            const menuItems = document.querySelectorAll('.nav_list li a');
            menuItems.forEach(item => item.classList.remove('active'));
            menuItem.classList.add('active');

            // Update breadcrumbs
            breadcrumbTrail = breadcrumbPath; // Store the current breadcrumb path
            updateBreadcrumbs(breadcrumbTrail);
        }

        function handleMenuClick(menuName, subMenuName = '') {
            const breadcrumbPath = [menuName];

            if (subMenuName) {
                breadcrumbPath.push(subMenuName);
            }

            return breadcrumbPath;
        }

        function toggleSubMenu(menuItem) {
            const subMenu = menuItem.querySelector('.sub-menu');
            const icon = menuItem.querySelector('.bx-chevron-down');
            if (subMenu) {
                subMenu.style.display = (subMenu.style.display === "none" || subMenu.style.display === "") ? "block" : "none";
                icon.classList.toggle('rotate');
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        }
    </script>
</body>
</html>