<?php
//Checks if submit has been clicked in the form
if(isset($_POST['submit'])){
    echo "Is this working";
    //Get values we want to insert
    $issuetitle = $_POST['title'];
    $issuecategory = $_POST['category'];
    $issuedesc = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO issues (title, category, description, user_id) VALUES (?, ?, ?, ?)");
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $issuetitle, $issuecategory, $issuedesc, $user_id);
    if($stmt->execute()){
        echo "Issue created successfully!";
        //You can add any type of message here for your user.
    } else {
        echo "An error occurred";
    }
}

$conn->close();
?>