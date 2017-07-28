<?php
/**
 * Created by PhpStorm.
 * User: amalyuchik
 * Date: 7/21/2017
 * Time: 3:04 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/sql_functions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/table_rows.php");

if (isset($_POST['product_category_id']) && $_POST['product_category_id'] !== null)
{
    $conn = new connections();
    $query = "UPDATE kitliastdb.products SET product_category_id_fk = " . $_POST['product_category_id'] . " WHERE product_category = '" . $_POST['product_category_name'] . "'";
    echo $query;
    $query_execute = $conn->runconn_sql_execute($connection_array,$query);
}
?>

<html>

<head>
	<META NAME="ROBOTS" CONTENT="NONE">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?php echo $bootstrapLink;
			echo $jQueryLink;
            echo $fontAwesomeLink;?>
<link rel="stylesheet" href="css/nav_style.css" />
<style>

</style>
<title>Books</title>
</head>
<body>
<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
    <label>ID: <input type="text" name="product_category_id"></input></label>
    <label>Name: <input type="text" name="product_category_name"></input></label>
    <button type="submit">Go</button>
</form>


</form>
</body>
</html>