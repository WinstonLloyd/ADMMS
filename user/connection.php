<?php
    $con = mysqli_connect("localhost","root","","admms") or die ("Error Connect");
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>