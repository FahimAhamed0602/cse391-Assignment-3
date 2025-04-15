<?php
// admin.php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session and include database connection
session_start();
include 'config.php';

// Handle login attempt
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === 'admin123') {
        $_SESSION['admin'] = true;
    } else {
        $login_error = 'Incorrect password. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .action-btn {
            padding: 5px 10px;
            margin: 2px;
        }
        .nav-button {
            padding: 10px 20px;
            margin-bottom: 20px;
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        .nav-button:hover {
            background: #218838;
        }
        .login-form {
            max-width: 300px;
            margin: 0 auto;
        }
        .login-form input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
        .info {
            color: blue;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php if (!isset($_SESSION['admin'])): ?>
        <h2>Admin Login</h2>
        <?php if ($login_error): ?>
            <p class="error"><?php echo $login_error; ?></p>
        <?php endif; ?>
        <form class="login-form" method="POST">
            <label>Password: </label>
            <input type="password" name="password" required>
            <button type="submit">Login</button>
        </form>
    <?php else: ?>
        <button class="nav-button" onclick="window.location.href='appointment.php'">Go to User Panel</button>
        <h2>Admin Panel - Appointment List</h2>
        <div id="appointment-table">
            <p class="info">Loading appointments...</p>
        </div>
        <a href="logout.php">Logout</a>

        <script>
            // Function to fetch appointments via AJAX
            function fetchAppointments() {
                fetch('fetch_appointments.php')
                    .then(response => response.json())
                    .then(data => {
                        const tableDiv = document.getElementById('appointment-table');
                        if (data.error) {
                            tableDiv.innerHTML = `<p class="error">${data.error}</p>`;
                            return;
                        }
                        if (data.appointments.length === 0) {
                            tableDiv.innerHTML = '<p class="info">No appointments found in the database.</p>';
                            return;
                        }

                        let html = '<table>';
                        html += '<tr><th>Client Name</th><th>Phone</th><th>Car Registration</th><th>Appointment Date</th><th>Mechanic</th><th>Actions</th></tr>';
                        data.appointments.forEach(row => {
                            html += `<tr>
                                <td>${row.client_name}</td>
                                <td>${row.phone}</td>
                                <td>${row.car_license}</td>
                                <td>${row.appointment_date}</td>
                                <td>${row.mechanic_name}</td>
                                <td>
                                    <form method="POST" action="update_appointment.php">
                                        <input type="hidden" name="appointment_id" value="${row.id}">
                                        <input type="date" name="new_date" value="${row.appointment_date}">
                                        <select name="new_mechanic">`;
                            data.mechanics.forEach(mech => {
                                const selected = mech.id == row.mechanic_id ? 'selected' : '';
                                html += `<option value="${mech.id}" ${selected}>${mech.name}</option>`;
                            });
                            html += `</select>
                                        <button type="submit" class="action-btn">Update</button>
                                    </form>
                                </td>
                            </tr>`;
                        });
                        html += '</table>';
                        tableDiv.innerHTML = html;
                    })
                    .catch(error => {
                        document.getElementById('appointment-table').innerHTML = `<p class="error">Error fetching appointments: ${error.message}</p>`;
                    });
            }

            // Initial fetch
            fetchAppointments();

            // Poll every 5 seconds for new appointments
            setInterval(fetchAppointments, 5000);
        </script>
    <?php endif; ?>
</body>
</html>