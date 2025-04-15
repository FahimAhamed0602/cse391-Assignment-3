<!DOCTYPE html>
<html>
<head>
    <title>Car Workshop Appointment - User Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        .error {
            color: red;
            font-size: 14px;
        }
        .success {
            color: green;
            font-size: 14px;
        }
        button {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .nav-button {
            margin-bottom: 20px;
            background: #28a745;
        }
        .nav-button:hover {
            background: #218838;
        }
        #mechanic-availability {
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
    <button class="nav-button" onclick="window.location.href='admin.php'">Go to Admin Panel</button>
    <h2>User Panel - Book Appointment</h2>
    <form id="appointmentForm" method="POST" action="process_appointment.php">
        <div class="form-group">
            <label for="client_name">Name:</label>
            <input type="text" id="client_name" name="client_name" required>
        </div>
        
        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="tel" id="phone" name="phone" pattern="[0-9]{10,}" required>
        </div>
        
        <div class="form-group">
            <label for="car_license">Car License Number:</label>
            <input type="text" id="car_license" name="car_license" required>
        </div>
        
        <div class="form-group">
            <label for="car_engine">Car Engine Number:</label>
            <input type="text" id="car_engine" name="car_engine" pattern="[0-9]+" required>
        </div>
        
        <div class="form-group">
            <label for="appointment_date">Appointment Date:</label>
            <input type="date" id="appointment_date" name="appointment_date" min="<?php echo date('Y-m-d'); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="mechanic">Select Mechanic:</label>
            <select id="mechanic" name="mechanic" required>
                <option value="">Select a mechanic</option>
                <?php
                include 'config.php';
                $stmt = $conn->query("SELECT id, name FROM mechanics");
                while ($row = $stmt->fetch()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>
            <div id="mechanic-availability">Select a date to see available mechanics.</div>
        </div>
        
        <button type="submit">Book Appointment</button>
    </form>

    <script>
        // Client-side form validation
        document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            const phone = document.getElementById('phone').value;
            const carEngine = document.getElementById('car_engine').value;
            const appointmentDate = document.getElementById('appointment_date').value;
            
            if (!/^\d{10,}$/.test(phone)) {
                e.preventDefault();
                alert('Phone number must be at least 10 digits');
            }
            
            if (!/^\d+$/.test(carEngine)) {
                e.preventDefault();
                alert('Car engine number must contain only numbers');
            }
            
            const today = new Date().toISOString().split('T')[0];
            if (appointmentDate < today) {
                e.preventDefault();
                alert('Cannot select past dates');
            }
        });

        // Fetch mechanic availability when date changes
        document.getElementById('appointment_date').addEventListener('change', function() {
            const date = this.value;
            if (!date) return;

            fetch(`fetch_mechanic_availability.php?date=${encodeURIComponent(date)}`)
                .then(response => response.json())
                .then(data => {
                    const availabilityDiv = document.getElementById('mechanic-availability');
                    if (data.error) {
                        availabilityDiv.innerHTML = `<span class="error">${data.error}</span>`;
                        return;
                    }
                    if (data.available.length === 0) {
                        availabilityDiv.innerHTML = '<span class="error">No mechanics available for this date.</span>';
                    } else {
                        availabilityDiv.innerHTML = 'Available mechanics: ' + data.available.map(m => `${m.name} (${m.slots} slots)`).join(', ');
                    }
                })
                .catch(error => {
                    document.getElementById('mechanic-availability').innerHTML = `<span class="error">Error checking availability: ${error.message}</span>`;
                });
        });
    </script>
</body>
</html>