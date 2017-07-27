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
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/book_detail.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/book_names_left_menu.php");
$conn = new connections();
$select_sql = new select_sql();
$book_name = '';
if(isset($_POST['state']) && $_POST['state'] !== '')
{
	$query_select = $select_sql->query("books", $state_submit, $grade_submit);
	$sql_execute = $conn->runconn_sql_execute($connection_array, $query_select);
}
if(isset($_GET['delete']))
{
    $delete_conn = new connections();
    $delete_query = $delete_conn->query_construct('delete',array('book_lab_line'),array('book_lab_line_id'=> $_GET['record_id']));
    $delete_query_execute = $delete_conn->runconn_sql_execute($connection_array,$delete_query);
}
?>
<!DOCTYPE HTML>

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
    <script type="text/javascript">
        var strUser = '';
        var valUser = '';
    </script>
    <script>
                function selected()
                {
                    if(document.getElementById('book_detail_table'))
                    document.getElementById('book_detail_table').innerHTML = '';
                    document.getElementById('button').click();
                }
    </script>
</head>

<body>
<div class="container">
<section>
        <div class="form-group">
            <form class="form-horizontal" action="<?php $thispage ?>" method="post">
                <?php
                $select_field_state = new select_input($global_available_states_list,'State','state',$sql_execute[0]['book_state_id_fk']);
                echo $select_field_state->create_select_field(true);

                echo "<div class=\"col-lg-10\"><input style=\"display:none;\" id=\"button\" type=\"submit\" value=\"Submit\"></div>";
                ?>

            </form>
        </div>
    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-5">
            <header>
                <strong>Available Books</strong>
            </header>
            <div class="list-group table-of-contents">

            <?php
            if($sql_execute !== null)
            {
                $book_menu = new book_names_left_menu($sql_execute);
                echo $book_menu->generate_book_name_menu();
//                $do_once = $do_once - $do_once;
//                foreach ($sql_execute as $k => $v) {
//                    echo "<a class=\"list-group-item\" href=\"" . $thispage . "?book_id=";
//                    echo $sql_execute[$do_once]['book_id'] . "&book_name=".$sql_execute[$do_once]['Book Name']."\">" . $sql_execute[$do_once]['Book Name'];
//                    echo "</a>";
//                    echo "\r\n";
//                    $do_once++;
                //}
            }
            ?>
            </div>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-7">

            <article>
                <div id="book_detail_result"></div> <!--Result of the Book_detail displays here-->
                <?php
//                if ($_GET['book_id'] !== null)
//                {
//                    $book_contents = new book_detail($connection_array, $_GET['Book Name'],$_GET['book_id'],$conn,$select_sql,'','',$global_lab_names_list);
//                }
                ?>
            </article>
        </div>
    </div>
</section>

		<?php $navigation = new site_nav(); ?>

	<footer>
		<p>Copyright 2017 ASM</p>
        <div id="testing"></div>
	</footer>
</div>
<script type="text/javascript">
    //    if(strUser)
    //    document.getElementById('testing').innerHTML = strUser;

    function postBook(book_id)
    {
        $.post('includes/post/post_book_detail_processor.php', {post_book_id:book_id},
            function(data)
            {
                $('#book_detail_result').html(data);
                //alert(data);
            });
    }

    function addLabToBook(book_id)
    {
        if($('#insert_lab_row').html().length > 100) {
            $('#insert_lab_row').html('');
        }
        else {
            $.post('includes/post/post_add_lab_to_book_form.php', {post_book_id: book_id},
                function (data) {
                    $('#insert_lab_row').html(data);
                });
        }
    }
    function closeAddLabRow()
    {
        if($('#insert_lab_row').html().length > 100)
            $('#insert_lab_row').html('');
    }
    function postLabInBook(book_id)
    {
        var lab_id = $('#add_lab_id').val();
        var alert_msg = ''.concat(book_id," ", lab_id);
        $.post('includes/post/post_add_lab_to_book_processor.php', {post_book_id: book_id,post_lab_id: lab_id},
            function (data) {
                $('#testing').html(data);
                //alert(data);
            });
        postBook(book_id);
        addLabToBook(book_id);
    }
    function deleteLabFromBook(record_id,book_id)
    {
        //alert(''.concat(record_id, " deleting ",book_id));
        var r = confirm('Are you sure you want to delete this record?');
        if(r==true) {
            $.post('includes/validate.php', {post_record_id: record_id},
                function (data) {
                    $('#testing').html(data);
                    //alert(data);
                });
            postBook(book_id);
            //addLabToBook(book_id);
        }
    }
</script>
</body>

</html>
