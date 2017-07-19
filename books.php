<?php
/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 4/14/17
 * Time: 12:44 AM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/sql_functions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/table_rows.php");
$conn = new connections();
$select_sql = new select_sql();
$book_name = '';
if(isset($_POST['state']) && $_POST['state'] !== '')
{
	$query_select = $select_sql->query("books", $state_submit, $grade_submit);
	$sql_execute = $conn->runconn_sql_execute($connection_array, $query_select);
}
?>
<!DOCTYPE HTML>

<html>

<head>
	<META NAME="ROBOTS" CONTENT="NONE">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?php echo $bootstrapLink;
			echo $jQueryLink; ?>
	<link rel="stylesheet" href="css/nav_style.css" />
	<style>

	</style>
	<title>Books</title>
    <script type="text/javascript">
        var strUser = '';
        var valUser = '';
    </script>
    <script>function alertMessage(containerName)
        {
            valUser = containerName.options[containerName.selectedIndex].value;
            strUser = containerName.options[containerName.selectedIndex].text;
            var str1 = 'For ';

            document.getElementById('testing').innerHTML = str1.concat(strUser);
            document.getElementById('button').click();
        }
    </script>
</head>

<body>
<div class="container">
<div class="row">
<section>
    <form class="form-horizontal" action="<?php $thispage ?>" method="post">
        <div class="form-group">
        <?php
        $select_field_state = new select_input($global_available_states_list,'State','state',$sql_execute[0]['book_state_id_fk']);

        echo "<div class=\"col-lg-10\"><input style=\"display:none;\" id=\"button\" type=\"submit\" value=\"Submit\"></div>";
        ?>

        </div>
    </form>
    <div class="row">
    <div class="col-lg-3 col-md-4 col-sm-5">
        <header>
            <strong>Available Books <span id="testing"></span></strong>
        </header>
            <div class="list-group table-of-contents">

            <?php
            if($sql_execute !== null)
            {
                //echo "<table style='border: solid 0px black;margin-bottom: 20px;'>";
    //                    echo "<tr> ";
    //                    foreach ($sql_execute[0] as $k => $v) {
    //                        if ($do_once < count($sql_execute[0]))
    //                            echo "<th class=\"table_header\">" . $k . "</th>";
    //                        else
    //                            continue;
    //
    //                        $do_once++;
    //                    }
    //                    echo "</tr>";
                $do_once = $do_once - $do_once;
                foreach ($sql_execute as $k => $v) {
                    echo "<a class=\"list-group-item\" href=\"" . $thispage . "?book_id=";
                    echo $sql_execute[$do_once]['book_id'] . "\">" . $sql_execute[$do_once]['Book Name'];
                    echo "</a>";
                    echo "\n\r";
                    $do_once++;
                }
            }
            ?>
            </div>
        </div>
    <div class="col-lg-9 col-md-8 col-sm-7">

        <article>
            <header>
                <h1>Testing</h1>
            </header>
            <?php
            if ($_GET['book_id'] !== null)
                $book_contents = new book_detail($connection_array, '',$_GET['book_id'],$conn,$select_sql);
            ?>
        </article>
    </div>
    </div>
</div>




</section>

		<?php $navigation = new site_nav(); ?>

	<footer>
		<p>Copyright 2017 ASM</p>
	</footer>
</div>
<script type="text/javascript">
    document.getElementById('testing').innerHTML = strUser;
</script>
</body>

</html>
