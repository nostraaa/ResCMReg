<?php
include 'db.php';

// Handle Add/Save Group
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = $_POST['group-id']; 
    $group_name = $_POST['group-name'];

    if ($group_id) {
        // Update if ID exists
        $stmt = $conn->prepare("UPDATE `group` SET GroupName = ? WHERE GroupId = ?");
        $stmt->bind_param("si", $group_name, $group_id);
    } else {
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO `group` (GroupName) VALUES (?)");
        $stmt->bind_param("s", $group_name);
    }

    if ($stmt->execute()) {
        header("Location: /rescmreg/layouts/coursemaintenance/update-group.php"); // Refresh after operation
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch Groups
$sql = "SELECT * FROM `group`";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Group</title>
    <link rel="stylesheet" href="/rescmreg/css/updategroup.css">
</head>
<body>

<div class="container">
    <a href="/rescmreg/layouts/coursemaintenance/course-maintenance.php" class="back-button">
        <img src="/rescmreg/images/go-back.png" alt="Go Back">
    </a>

    <h3>Group Maintenance</h3>
    <form method="POST" action="">
        <input type="hidden" id="group-id" name="group-id"> <!-- Hidden input for Group ID -->
        
        <div class="form-group">
            <label for="group-name">Group Name</label>
            <input type="text" id="group-name" name="group-name" required>

            <div class="form-buttons">
                <button type="submit">
                    <img src="/rescmreg/images/add.png" alt="Save" class="button-icon"> Add/Save
                </button>

                <button type="button" id="cancel-clear-btn">
                    <img src="/rescmreg/images/cancel.png" alt="Remove" class="button-icon"> Cancel/Clear
                </button>
            </div>
        </div>
    </form>

    <h4>List of Existing Groups</h4>
    <table>
        <thead>
            <tr>
                <th>Group Name</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['GroupName']) ?></td>
                    <td>
                        <button class="edit-btn" 
                                data-id="<?= $row['GroupId'] ?>" 
                                data-name="<?= htmlspecialchars($row['GroupName']) ?>">
                            Edit
                        </button>
                    </td>
                    <td>
                        <button class="delete-btn" data-id="<?= $row['GroupId'] ?>">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
    document.querySelectorAll('.delete-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            const groupId = this.getAttribute('data-id'); // Get Group ID
            
            if (confirm('Are you sure you want to delete this group?')) {
                // Send AJAX request to delete-group.php
                fetch('/rescmreg/layouts/coursemaintenance/delete-group.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ 'id': groupId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the row from the table
                        this.closest('tr').remove();
                        alert('Group deleted successfully.');
                    } else {
                        alert('Failed to delete group: ' + data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });

    // Clear form when 'Cancel / Clear' button is clicked
    document.getElementById('cancel-clear-btn').addEventListener('click', function () {
        document.getElementById('group-id').value = '';  // Clear the hidden ID field
        document.getElementById('group-name').value = ''; // Clear the input field
    });

    // Populate form with existing data for editing
    document.querySelectorAll('.edit-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            const groupId = this.getAttribute('data-id');
            const groupName = this.getAttribute('data-name');

            // Debugging: Check if the values are correct
            console.log('Editing Group ID:', groupId);
            console.log('Editing Group Name:', groupName);

            document.getElementById('group-id').value = groupId;  // Set hidden ID field
            document.getElementById('group-name').value = groupName;  // Set name field
        });
    });
</script>


<?php $conn->close(); ?>
</body>
</html>
