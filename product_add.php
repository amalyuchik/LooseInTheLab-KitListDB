<?php
/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 4/19/17
 * Time: 11:28 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
$lab_name = $_GET['lab_name'];

?>
<!DOCTYPE html>
<html>
<head>
    <META NAME="ROBOTS" CONTENT="NONE">
    <?php echo $bootstrapLink; ?>
    <?php echo $fontAwesomeLink; ?>
    <link rel="stylesheet" href="css/nav_style.css" />
    <style>
        td{
            padding-left: 10px;
        }th{
             padding-left: 10px;
         }
    </style>
    <title><?php echo $lab_name; ?> | Add Product</title>

</head>

<body>
<h1><?php echo $lab_name; ?></h1>
</body>
</html>