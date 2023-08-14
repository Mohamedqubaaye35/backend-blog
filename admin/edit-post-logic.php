<?php
require 'config/database.php';

if (isset($_POST['submit'])) {
    // get  update form data
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $previous_thumbnail_name = filter_var($_POST['previous_thumbnail_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = filter_var($_POST['body'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_id = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = filter_var($_POST['is_featured'], FILTER_SANITIZE_NUMBER_INT);
    $thumbnail = $_FILES['thumbnail'];

    // set is_featured to 0
    $is_featured = $is_featured == 1 ?: 0 ;

    // check for valid input
    if(!$title){
        $_SESSION['edit-post'] = "Could not Update Post. Invalid form data on edit page.";
    }elseif(!$category_id){
        $_SESSION['edit-post'] = "Could not Update Post. Invalid form data on edit page.";
    }elseif(!$body){
        $_SESSION['edit-post'] = "Could not Update Post. Invalid form data on edit page.";
    }else{
        // delete existing thumbnail if new thumbnail is available
        if($thumbnail['name']){
            $previous_thumbnail_path = '../images/' . $previous_thumbnail_name;
            if($previous_thumbnail_path){
                unlink($previous_thumbnail_path);
            }
            // work on new thumbnial
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
            if($thumbnail['size'] < 2000000){
                // updload image
                move_uploaded_file($thumbnail_tmp_name, $thumbnail_destination_path);
            }else{
                $_SESSION['edit-post'] = "Couldn't update post. Thumbnail size is too large.
                should be less than 2mb";
            }
        }else{
            $_SESSION['edit-post'] = "Couldn't update post. Thumbnail should  be png, jpeg or jpg";

        }
        }
    }

       // !redirect back to add-post page
       if ($_SESSION['edit-post']){
        //! pass form data back to signup page
        header('location: ' . ROOT_URL . 'admin/');
        die();
    }else{
        //! set is_featured of all posts to 0 if is_featured for this post is 1
        if($is_featured == 1){
            $zero_all_is_fearured_query = "UPDATE posts SET is_featured=0" ;
            $zero_all_is_fearured_result = mysqli_query($connection, $zero_all_is_fearured_query);

        }  

        //set thumbnail name if a new one was uploaded
        $thumbnail_to_insert = $thumbnail_name ?? $previous_thumbnail_name;

          //! Update post into database
          $query = "UPDATE posts SET title = '$title', body = '$body', thumbnail = '$thumbnail_to_insert',
          category_id = $category_id, is_featured = $is_featured WHERE id = $id LIMIT 1";
          $result = mysqli_query($connection, $query);
  
    }
    if(!mysqli_errno($connection)){
        $_SESSION['edit-post-success'] = "Post updated successfully. ";
       
}
    }
    header('location: '. ROOT_URL. 'admin/');
    die();

?>