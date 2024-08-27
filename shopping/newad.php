<?php
include 'db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Advertisement</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {

        // Handle "mail" button click
        $(".sendMail").click(function() {
            var textboxValue = $("#textbox").val(); 
            var textareaValue = $("#textarea").val();
            $.ajax({
                url: 'email.php',
                method: 'POST',
                data: {
                    textbox: textboxValue,
                    textarea: textareaValue
                },
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function() {
                    alert("An error occurred while sending the mail.");
                }
            });
        });

    });
    </script>
    <style>
        body {
            background-image: url('shop img.jpg');
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            position: relative;
            width: 100%;
            max-width: 400px;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .sendMail {
            margin-bottom: 10px;
        }

        .backButtonContainer {
            position: absolute;
            bottom: 0;
            left: 0;
        }

        .backButton {
            background-color: #6c757d;
            width: auto;
            padding: 10px 20px;
            border-radius: 4px;
            color: #fff;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            font-size: 16px;
        }

        .backButton:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <h1><center>Advertisement Mail</hi>
    <div class="container">
        <form method="POST" enctype="multipart/form-data">
            <input type="text" id="textbox" placeholder="Subject" required>
            <textarea id="textarea" placeholder="Description" maxlength="250" required></textarea>
            <button type="button" class="sendMail">Send Advertisement</button>
        </form>
        
    </div>
    <div class="backButtonContainer">
            <a href="dashboard.php" class="backButton">Back to Dashboard</a>
        </div>
</body>
</html>
