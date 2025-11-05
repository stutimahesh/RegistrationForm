<?php
// PHP BACKEND LOGIC: Handles the AJAX POST request and returns HTML results

// Check if the request is a POST submission (i.e., the form was submitted via fetch)
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST)) {
    
    // Set the content type to HTML since we are returning a styled snippet
    header('Content-Type: text/html; charset=utf-8');

    // Simple sanitization function
    function sanitize_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Sanitize and store input data
    $fullName = isset($_POST['fullName']) ? sanitize_input($_POST['fullName']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
    $gender = isset($_POST['gender']) ? sanitize_input($_POST['gender']) : ''; 
    $course = isset($_POST['course']) ? sanitize_input($_POST['course']) : '';

    // Perform basic validation
    $errors = [];
    if (empty($fullName)) { $errors[] = "Full Name is required."; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "A valid Email Address is required."; }
    if (empty($phone) || strlen(preg_replace('/[^0-9]/', '', $phone)) < 7) { $errors[] = "A valid Phone Number is required (min 7 digits)."; }
    if (empty($gender)) { $errors[] = "Gender selection is required."; }
    if (empty($course)) { $errors[] = "Course Selection is required."; }

    // Check for errors
    if (count($errors) > 0) {
        // If validation fails, return a styled error message
        http_response_code(400); // Set response code to Bad Request
        echo '<div class="result-card error">';
        echo '<h2>Application Error</h2>';
        echo '<p>The following issues were found:</p>';
        echo '<ul>';
        foreach ($errors as $error) {
            echo '<li>' . $error . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    } else {
        // Success case: Generate formatted, styled HTML output to be displayed by AJAX
        
        $output_html = "
            <div class='result-card success'>
                <h2>Application Submitted Successfully!</h2>
                <p>Thank you, <strong>$fullName</strong>. Here are your application details:</p>
                <div class='detail-group'>
                    <span class='label'>Email:</span>
                    <span class='value'>$email</span>
                </div>
                <div class='detail-group'>
                    <span class='label'>Phone:</span>
                    <span class='value'>$phone</span>
                </div>
                <div class='detail-group'>
                    <span class='label'>Gender:</span>
                    <span class='value'>$gender</span>
                </div>
                <div class='detail-group'>
                    <span class='label'>Course:</span>
                    <span class='course-tag'>$course</span>
                </div>
            </div>
        ";
        
        echo $output_html;
    }
    
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Application Form</title>
    <style>
        /* Basic Reset and Layout */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
            min-height: 100vh;
            color: #333;
        }
        .container {
            max-width: 500px;
            width: 100%;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }
        h1 {
            font-size: 28px;
            color: #1a1a1a;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
            font-weight: 600;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
            font-size: 0.95em;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box; 
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        input:focus,
        select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
            outline: none;
        }

        /* Radio Group Styling (Gender) */
        .radio-group {
            display: flex;
            gap: 25px;
            padding-top: 5px;
        }
        .radio-group label {
            font-weight: normal;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }
        .radio-group input[type="radio"] {
            width: auto;
            margin-right: 8px;
            cursor: pointer;
        }
        .radio-group .form-group {
            margin-bottom: 0;
        }

        /* Button Styling */
        #submitBtn {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 17px;
            font-weight: bold;
            width: 100%;
            transition: background-color 0.3s, opacity 0.3s;
            margin-top: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        #submitBtn:hover:not(:disabled) {
            background-color: #45a049;
        }
        #submitBtn:disabled {
            background-color: #a5d6a7;
            cursor: not-allowed;
            opacity: 0.8;
        }

        /* Status, Error, and Results */
        #statusMessage {
            text-align: center;
            padding: 10px;
            border-radius: 6px;
            margin-top: 15px;
            font-weight: 500;
        }
        .text-success {
            background-color: #e6ffe6;
            color: #2e7d32;
        }
        .text-error {
            background-color: #ffe6e6;
            color: #d32f2f;
            border: 1px solid #ff0000;
        }
        .error-input {
            border-color: #f44336 !important;
            box-shadow: 0 0 0 1px #f44336 !important;
        }

        /* Results Display Styling */
        #results {
            margin-top: 30px;
        }
        .result-card {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s;
        }
        .result-card.success {
            border-left: 5px solid #4CAF50;
        }
        .result-card.error {
            border-left: 5px solid #f44336;
        }
        .result-card h2 {
            font-size: 22px;
            color: #4CAF50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            font-weight: 600;
        }
        .result-card.error h2 {
            color: #f44336;
        }
        .result-card ul {
            list-style: disc;
            margin-left: 20px;
            padding-top: 5px;
        }
        .detail-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #eee;
        }
        .detail-group:last-of-type {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #666;
            min-width: 100px;
        }
        .value {
            color: #333;
        }
        .course-tag {
            background-color: #e0f2f1;
            color: #00796b;
            padding: 4px 10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.9em;
        }
        /* Loading Spinner */
        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
        .spinner {
          border: 3px solid rgba(255, 255, 255, 0.3);
          border-top: 3px solid white;
          border-radius: 50%;
          width: 15px;
          height: 15px;
          animation: spin 0.8s linear infinite;
          display: inline-block;
          vertical-align: middle;
          margin-right: 8px;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Application Form -->
        <div id="formCard">
            <h1>Course Registration</h1>
            
            <form id="registrationForm">
                
                <!-- Full Name -->
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" required placeholder="Stuti Mahesh">
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="stuti@gmail.com">
                </div>

                <!-- Phone Number -->
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required placeholder="+91 892637712">
                </div>
                
                <!-- Gender Selection (New Field) -->
                <div class="form-group">
                    <label>Gender</label>
                    <div class="radio-group">
                        <label for="genderMale">
                            <input type="radio" id="genderMale" name="gender" value="Male" required> Male
                        </label>
                        <label for="genderFemale">
                            <input type="radio" id="genderFemale" name="gender" value="Female"> Female
                        </label>
                        <label for="genderOther">
                            <input type="radio" id="genderOther" name="gender" value="Other"> Other
                        </label>
                    </div>
                </div>

                <!-- Course Selection -->
                <div class="form-group">
                    <label for="course">Select Course</label>
                    <select id="course" name="course" required>
                        <option value="">-- Choose a Course --</option>
                        <option value="Web Development (Full Stack)">Web Development (Full Stack)</option>
                        <option value="Data Science and Analytics">Data Science and Analytics</option>
                        <option value="UI/UX Design Principles">UI/UX Design Principles</option>
                        <option value="Cloud Computing Basics">Cloud Computing Basics</option>
                    </select>
                </div>

                <!-- Submission Button -->
                <button type="submit" id="submitBtn">
                    Submit Application
                </button>
                
                <!-- Submission Status/Loading Indicator -->
                <div id="statusMessage"></div>
            </form>
        </div>
        
        <!-- Results Display Area -->
        <div id="results">
        </div>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('registrationForm');
            const submitBtn = document.getElementById('submitBtn');
            const statusMessage = document.getElementById('statusMessage');
            const resultsDiv = document.getElementById('results');
            const formCard = document.getElementById('formCard');

            // Function to serialize form data into URLSearchParams object
            function serializeForm(formElement) {
                return new URLSearchParams(new FormData(formElement));
            }

            // Function to handle form submission via fetch
            form.addEventListener('submit', async (e) => {
                e.preventDefault(); 

                // Reset previous states
                resultsDiv.innerHTML = '';
                statusMessage.style.display = 'none';
                statusMessage.className = '';

                // Client-side validation for required fields
                let isValid = true;
                document.querySelectorAll('[required]').forEach(input => {
                    input.classList.remove('error-input');
                    if (input.type !== 'radio' && input.value.trim() === '') {
                        input.classList.add('error-input');
                        isValid = false;
                    }
                });

                // Special check for radio buttons (gender)
                if (!form.querySelector('input[name="gender"]:checked')) {
                    // Visually mark the gender group as needing attention if possible, 
                    // though no single input to mark
                    document.querySelector('.radio-group').style.border = '1px solid #f44336';
                    document.querySelector('.radio-group').style.borderRadius = '5px';
                    document.querySelector('.radio-group').style.padding = '5px';
                    isValid = false;
                } else {
                    document.querySelector('.radio-group').style.border = 'none';
                    document.querySelector('.radio-group').style.padding = '0';
                }

                if (!isValid) {
                    statusMessage.textContent = 'Please fill all required fields.';
                    statusMessage.classList.add('text-error');
                    statusMessage.style.display = 'block';
                    return;
                }

                // Prepare for AJAX call
                const formData = serializeForm(form);
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner"></span> Processing...';

                try {
                    const response = await fetch('index.php', {
                        method: 'POST',
                        body: formData
                    });

                    const responseHtml = await response.text();

                    if (response.ok) {
                        // Success (HTTP 200) - PHP returned success HTML
                        resultsDiv.innerHTML = responseHtml; 
                        statusMessage.textContent = 'Application submitted successfully!';
                        statusMessage.classList.add('text-success');
                        statusMessage.style.display = 'block';
                        
                        // Hide the form on success and scroll
                        formCard.style.display = 'none';
                        window.scrollTo({ top: resultsDiv.offsetTop - 20, behavior: 'smooth' });
                    } else if (response.status === 400) {
                        // Server-side validation failed (HTTP 400) - PHP returned error HTML
                        resultsDiv.innerHTML = responseHtml;
                        statusMessage.textContent = 'Server-side validation failed (see error details below).';
                        statusMessage.classList.add('text-error');
                        statusMessage.style.display = 'block';
                    } else {
                        // Other server error (e.g., 500)
                        resultsDiv.innerHTML = `<div class='result-card error'><h2>Submission Failed</h2><p>Server error occurred (Status: ${response.status}). Please try again later.</p></div>`;
                        statusMessage.textContent = 'A critical error occurred.';
                        statusMessage.classList.add('text-error');
                        statusMessage.style.display = 'block';
                    }
                } catch (error) {
                    // Network failure (e.g., PHP server is down)
                    resultsDiv.innerHTML = `<div class='result-card error'><h2>Submission Failed</h2><p>Network connection failed. Please check your server setup.</p></div>`;
                    statusMessage.textContent = 'Network error.';
                    statusMessage.classList.add('text-error');
                    statusMessage.style.display = 'block';
                    console.error('Fetch Error:', error);
                } finally {
                    // Re-enable button and reset text
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Application';
                }
            });
        });
    </script>
</body>
</html>