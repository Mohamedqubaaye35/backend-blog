<?php
require 'config/database.php';

if (isset($_POST['submit'])){
    $author_id = $_SESSION['user-id'];
    $title = filter_var($_POST ['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = filter_var($_POST ['body'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_id = filter_var($_POST ['category'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = filter_var($_POST ['is_featured'], FILTER_SANITIZE_NUMBER_INT);
    $thumbnail = $_FILES['thumbnail'];

    //! set is_featured to 0 if unchecked
    $is_featured = $is_featured == 1 ?: 0;	

    // validate form data
    if(!$title){
        $_SESSION['add-post'] = "Please enter post title";

    } else if(!$category_id){
        $_SESSION['add-post'] = "Please select a category";

    } else if(!$body){
        $_SESSION['add-post'] = "Please enter post body";
    } else if(!$thumbnail['name']){
        $_SESSION['add-post'] = "Choice post thumbnail";
    
    } else{
        // work on thumbnail
        $time = time();
        $thumbnail_name = $time . $thumbnail['name'];
        $thumbnail_tmp_name = $thumbnail['tmp_name'];
        $thumbnail_destination_path = '../images/' . $thumbnail_name;

        // make sure file is an image
        $allowed_files = ['png', 'jpg', 'jpeg'];
        $extention = explode('.', $thumbnail_name);
        $extention = end($extention);
        if(in_array($extention, $allowed_files)){
            // make sure file is  not too large(2mb)
            if($avatar['size'] < 2000000){
                // updload image
                move_uploaded_file($thumbnail_tmp_name, $thumbnail_destination_path);
            }else{
                $_SESSION['add-post'] = "File too large. should be less than 1mb";
            }
        }else{
            $_SESSION['add-post'] = "File type should be png, jpg or jpeg";

        }
    }
    // !redirect back to add-post page
    if($_SESSION['add-post']){
        //! pass form data back to signup page
        $_SESSION['add-post-data'] = $_POST;
        header('location: ' . ROOT_URL . 'admin/add-post.php');
        die();
    }else{
        //! set is_featured of all posts to 0 if is_featured for this post is 1
        if($is_featured == 1){
            $zero_all_is_fearured_query = "UPDATE posts SET is_featured=0" ;
            $zero_all_is_fearured_result = mysqli_query($connection, $zero_all_is_fearured_query);

        }   

        //! Insert post into database
        $query = "INSERT INTO posts (title, body, thumbnail, category_id, author_id, is_featured) 
        VALUES('$title','$body', '$thumbnail_name', $category_id, $author_id, $is_featured)";
        $result = mysqli_query($connection, $query);

        if(!mysqli_errno($connection)){
            $_SESSION['add-post-success'] = "New post added successfully. ";
            header('location: '. ROOT_URL. 'admin/');
            die();
         }

    }
 
}

    //!get the form signup form submission
    header('location: '. ROOT_URL . 'admin/add-post.php');
    die();