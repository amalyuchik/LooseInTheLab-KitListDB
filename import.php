<?php
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
//connect to the database 
$connect = mysql_connect($db_h,$db_u,$db_p);
mysql_select_db($db_n,$connect); //select the table
// 

if ($_FILES[csv][size] > 0) {

    //get the csv file 
    $file = $_FILES[csv][tmp_name];
    $handle = fopen($file,"r");

    //loop through the csv file and insert into database 
    do {
        if ($data[0]) {
            mysql_query("UPDATE lab_product_line SET lab_product_line_classroom_qty = $data[2] WHERE lab_product_line_lab_id_fk = $data[0] AND lab_product_line_product_id_fk = $data[1]");
            //echo "UPDATE lab_product_line SET lab_product_line_classroom_qty = addslashes($data[2]) WHERE lab_product_line_lab_id_fk = addslashes($data[0]) AND lab_product_line_product_id_fk = addslashes($data[1])<br />";
        }
    } while ($data = fgetcsv($handle,2741,";","\""));
    // 

    //redirect 
    header('Location: import.php?success=1'); die;

}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Import a CSV File with PHP & MySQL</title>
</head>

<body>

<?php if (!empty($_GET[success])) { echo "<b>Your file has been imported.</b><br><br>"; } //generic success notice ?>

<form action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
    Choose your file: <br />
    <input name="csv" type="file" id="csv" />
    <input type="submit" name="Submit" value="Submit" />
</form>

</body>
</html> 