<?php

require 'config/database.php';

//!get the signup form submission 
if (isset($_POST['submit'])){
    $firstname = filter_var($_POST ['firstname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastname = filter_var($_POST ['lastname'],  FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $username = filter_var($_POST ['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST ['email'],  FILTER_VALIDATE_EMAIL);
    $createpassword = filter_var($_POST ['createpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirmpassword = filter_var($_POST ['confirmpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $avatar = $_FILES['avatar'];
 
    //! validate input values
    if(!$firstname){
        $_SESSION['signup'] = "Please enter your First name";

    } else if(!$lastname){
        $_SESSION['signup'] = "Please enter your Last name";

    } else if(!$username){
        $_SESSION['signup'] = "Please enter your username";
    } else if(!$email){
        $_SESSION['signup'] = "Please enter your a valid Email";
    } else if(strlen($createpassword) < 8 || strlen($confirmpassword) < 8) {
        $_SESSION['signup'] = "Password must be at least 8 characters";
    }else if(!$avatar['name']){
        $_SESSION['signup'] = "Please select an image ";
    }else{

        //! check if the password does not already exist

        if($createpassword !== $confirmpassword){
            $_SESSION['signup'] = "Passwords do not match";
        }else{

            //! hash the password
            $hashed_password = password_hash($createpassword,
            PASSWORD_DEFAULT);

            //! check if username or email already exists
            $user_check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
            $user_check_result = mysqli_query($connection, $user_check_query);
            if(mysqli_num_rows($user_check_result) > 0){
                $_SESSION['signup'] = "Username or Email already exists";
            }else{
                //! work on avatar
                // rename avatar 
                $time = time(); // make each image name unique using current timestamp
                $avatar_name = $time . $avatar['name'];
                $avatar_tmp_name = $avatar['tmp_name'];
                $avatar_destination_path = 'images/' . $avatar_name;

                //! make sure file is an image
                $allowed_files = ['png', 'jpg', 'jpeg'];
                $extention = explode('.', $avatar_name);
                $extention = end($extention);
                if(in_array($extention, $allowed_files)){
                    // make sure file is  not too large(1mb)
                    if($avatar['size'] < 1000000){
            
                        // updload image
                        move_uploaded_file($avatar_tmp_name, $avatar_destination_path);
        

                    } else{
                        $_SESSION['signup'] = "File too large. should be less than 1mb";
                    }

                    }else{
                        $_SESSION['signup'] = "File type should be png, jpg or jpeg";
                    }

                    }
                }
            }

            // redirect back to signup page if there was an error
            if($_SESSION['signup']){
                // pass form data back to signup page
                $_SESSION['signup-data'] = $_POST;
                header('location: ' . ROOT_URL . 'signup.php');
                die();
            }else{
                //!insert new user into the user table
                $insert_user_query = "INSERT INTO users(firstname, lastname, username, 
                email, password, avatar, is_admin) VALUES('$firstname', '$lastname', 
                '$username', '$email', '$hashed_password', '$avatar_name',0)";
                $insert_user_result = mysqli_query($connection, $insert_user_query);

                if(!mysqli_errno($connection)){
                    $_SESSION['signup-success'] = "Registeration successful. please log in";
                    header('location: '. ROOT_URL. 'signin.php');
                    die();
                }

            }
        
     }else{

    //!get the form signup form submission
    header('location: '. ROOT_URL . 'signup.php');
    die();
}

?>