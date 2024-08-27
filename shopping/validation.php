<?php
include 'db.php';
// Validate email
function validateEmail($email){

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }
}

    //validate phone
    function validatePhone($phone) {
        // Define a regex pattern for phone numbers
    
        if (!preg_match('/^(?:\+91|91)?(?:\s|\-)?(?:\(?\d{3}\)?|\d{3})?\s?\d{10}$/',$phone)) {
            die("Invalid phone number format");
        }
    }
    


    
        

    //name validation
    function validateName($name){

        if (empty($name)) {  
            die("enter a name");
            //check the name pattern
        }else if(!preg_match('/^[a-zA-Z][a-zA-Z0-9 ]*(?!\d{3})(?=[^@!#\$%\^&*(),.?":{}|<>]*)$/',$name)){
            die("Name format not matched");

        }else{
            return true;
        }
            
        }
        



   // password validation
    function validatePassword($password){
        if(empty($password)){
            die("password should not be empty");
        }else{
            if(strlen($password)<8){
                die("password should have 8 characters");
            }
            else if(!preg_match('/[A-Z]/', $password)){
                die("password should have atleast one Uppercase");
            }
            else if(!preg_match('/[a-z]/',$password)){
                die("password should have atleast one Lowercase");
            }
            else if(!preg_match('/\d/',$password)){
                die("password should have atleast one digit");
            }
            else if(!preg_match('/[@$!%*?&]/',$password)){
                die("password should have one special character");
            }
            else{
                return true;
            }
        }
    }

      


    ?>