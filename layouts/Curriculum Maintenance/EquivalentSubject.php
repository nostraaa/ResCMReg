<?php
// Define the current page
$current_page = 'Equivalent Subject for Advising'; // Set this to the title of the current page
$breadcrumbs = [
    'Dashboard' => '/rescmreg/index.php', 
    'Equivalent Subject for Advising' => '/rescmreg/layouts/Curriculum Maintenance/EquivalentSubject.php' 
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applicant</title>
    <!-- Load Google Fonts Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Load Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Load Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Breadcrumb styling */
        .breadcrumbs {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            font-size: 14px;
            color: #6c757d;
            padding: 12px 12px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            z-index: 1000;
            border-radius: 7px;
            font-family: Arial, sans-serif;
            gap: 15px;
        }
        .breadcrumbs a {
            margin-left: 10px;
            text-decoration: none;
            color: #174069;
            font-weight: bold;
            transition: color 0.2s ease-in-out;
        }
        .breadcrumbs a:hover {
            color: #0056b3;
        }
        .breadcrumbs span {
            color: #6c757d;
        }
        .breadcrumbs .current-page {
            color: #FFA500;; /* Mustard yellow color */
            font-weight: bold;
        }
        /* Add some top padding to the container to avoid overlap with the fixed breadcrumbs */
        .container {
            padding-top: 50px;
        }
    </style>

</head>

<body>
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <?php 
        $breadcrumbCount = count($breadcrumbs);
        $currentIndex = 0;

        foreach ($breadcrumbs as $title => $link): 
            $currentIndex++;
            if ($title === $current_page): ?>
                <span class="current-page"><?= htmlspecialchars($title) ?></span>
            <?php else: ?>
                <a href="<?= $link ?>"><?= htmlspecialchars($title) ?></a>
            <?php endif; ?>
            <?php if ($currentIndex < $breadcrumbCount): ?>
                <span>&gt;</span>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Navbar placeholder -->
    <div id="navbar-placeholder"></div>

    <!-- Main content -->
    <div class="main-content p-6" id="mainContent">
        


          <!-- Form section -->

    <section class="form-section mb-10 p-6 bg-white shadow-md rounded-lg mt-10">
    <form>
        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="course_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white text-center">Subject RHS (Equivalent Subject)</label>
                <select id="course_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                    <option value="" disabled selected>Select RHS</option>
                    <!-- Add more options as needed -->
                </select>
            </div>


            <div>
                <label for="course_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white text-center">Subject LHS (Will share offering from RHS subject)</label>
                <select id="course_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                    <option value="" disabled selected>Select SUbject LHS</option>
                    <!-- Add more options as needed -->
                </select>
            </div>


    </form>
</section>



<!-- Buttons -->
<section class="flex justify-center items-center">
    <div class="grid mb-6 md:grid-cols-2 gap-10"> <!-- Adjusted gap for better separation -->

        <label for="include-offered" class="inline-flex items-start cursor-pointer text-sm font-medium text-gray-900 dark:text-gray-300">
            <input type="checkbox" id="include-offered" class="hidden" />
            <div class="flex items-center justify-center w-6 h-6 border border-gray-300 rounded bg-gray-100 dark:bg-gray-700 dark:border-gray-600 relative">
                <i class="fa-solid fa-eye text-blue-600" style="display: block;"></i> <!-- Eye icon -->
                <i class="fa-solid fa-eye-slash text-blue-600" style="display: none;"></i> <!-- Eye slash icon -->
            </div>
            <span class="ml-2">Show all subject already created</span>
        </label>

        <label for="include-offered-2" class="inline-flex items-start cursor-pointer text-sm font-medium text-gray-900 dark:text-gray-300">
            <input type="checkbox" id="include-offered-2" class="hidden" />
            <div class="flex items-center justify-center w-6 h-6 border border-gray-300 rounded bg-gray-100 dark:bg-gray-700 dark:border-gray-600 relative">
                <i class="fa-solid fa-eye text-blue-600" style="display: block;"></i> <!-- Eye icon -->
                <i class="fa-solid fa-eye-slash text-blue-600" style="display: none;"></i> <!-- Eye slash icon -->
            </div>
            <span class="ml-2">Show all subject created with Course/Curriculum/unit</span>
        </label>
        
    </div>
</section>



 <!-- Table section -->


 <section class="table-section p-6 bg-white shadow-md rounded-lg">
    <h2 class="text-lg font-semibold text-gray-900 mb-6 text-center">List of Departments</h2>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
             
                <tr style="background-color: #012D5E; color: white;">
                    <th scope="col" class="px-6 py-3 text-center">Course code</th>
                    <th scope="col" class="px-6 py-3 text-center">Course name</th>
                    <th scope="col" class="px-6 py-3 text-center">Classification</th>
                    <th scope="col" class="px-6 py-3 text-center">Major</th>
                    <th scope="col" class="px-6 py-3 text-center">College offering</th>
                    <th scope="col" class="px-6 py-3 text-center">Department </th>
                    <th scope="col" class="px-6 py-3 text-center">Curriculum format </th>

                </tr>
            </thead>
            <tbody>
                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">PRE MBA OU</th> <!--Course code table-->
                    <td class="px-6 py-4 text-center">Master in Business Administration (Pre-MBA)</td> <!--Course name row-->
                    <td class="px-6 py-4 text-center">Masteral</td> <!--Classification rows-->
                    <td class="px-6 py-4 text-center"></td> <!--Major row-->
                    <td class="px-6 py-4 text-center">Online Education</td> <!--College offering row-->
                    <td class="px-6 py-4 text-center">Distance Education</td> <!--Department row-->
                    <td class="px-6 py-4 text-center">Graduate</td> <!--Curriculum format row-->
                </tr>


               <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">AUTO SERV</th> <!--Course code row-->
                    <td class="px-6 py-4 text-center">Automotive Servicing NC I</td> <!--Course name row-->
                    <td class="px-6 py-4 text-center">Technical Education and Skills Development</td> <!--Classification row-->
                    <td class="px-6 py-4 text-center"></td> <!--Major row-->
                    <td class="px-6 py-4 text-center">Technical Education and Skills Development</td> <!--COllege offering row-->
                    <td class="px-6 py-4 text-center"></td> <!--Department row-->
                    <td class="px-6 py-4 text-center">Undergraduate</td> <!--Curriculum format row-->
                </tr>

            </tbody>
        </table>
    </div>
</section>   
<script>
             document.addEventListener('DOMContentLoaded', function() {
        // Select the breadcrumb container
        const breadcrumbContainer = document.querySelector('.breadcrumbs');

        // Add event listener to 'Update' button to add breadcrumb item
        document.querySelector('.change-btn[data-breadcrumb]').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default link behavior

            // Get the breadcrumb name from the data attribute
            const breadcrumbName = this.getAttribute('data-breadcrumb');

            // Generate the new breadcrumb item
            const newBreadcrumb = `<a href="${this.href}">${breadcrumbName}</a>`;

            // Append new breadcrumb to container with separator
            breadcrumbContainer.innerHTML += ` &gt; ${newBreadcrumb}`;

            // Redirect to the actual link after updating the breadcrumb
            setTimeout(() => {
                window.location.href = this.href;
            }, 100); // Small delay to visually update breadcrumbs first
        });
    });
    </script>     
</body>

</html> 

