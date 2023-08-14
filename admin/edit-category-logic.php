<?php
require 'config/database.php';

if (isset($_POST['submit'])) {
    // get  update form data
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // check for valid input
    if(!$title || !$description){
        $_SESSION['edit-category'] = "Invalid Form Input on category page" ;
       
    }else{
        // update the user
        $query = "UPDATE categories SET title='$title', 
        description='$description' WHERE id=$id LIMIT 1" ;
        $result = mysqli_query($connection, $query);

        
        if(mysqli_errno($connection)) {
            $_SESSION['edit-category'] = "Failed to update category.";
        }else{
            $_SESSION['edit-category-success'] = "$title Category Updated Successfully.";
        
        }

    }

}
header('location: ' .ROOT_URL . 'admin/manage-categories.php');
die();

?>