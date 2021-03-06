<?php
if(!isset($_COOKIE['ID_LooseInTheLab'])){
    header("Location: http://www.seriouslyfunnyscience.com/workshops/login.php");
}
/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 4/14/17
 * Time: 12:44 AM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
//require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/data_processing/sql_functions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/table_rows.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/book_detail.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/book_names_left_menu.php");

if(!$conn)
$conn = new connections();
if (!$select_sql)
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
    <script>
                function selected()
                {
                    var e = document.getElementById("book_name_supplemental");
                    var selectedBookId = e.options[e.selectedIndex].value;
                    var selectedBookName = e.options[e.selectedIndex].text;
                    if(selectedBookId !== '')
                    {
                        if (document.getElementById('book_detail_table'))
                            document.getElementById('book_detail_table').innerHTML = '';
                        $.get("includes/get/get_commercial_book_processor.php?book_id=".concat(selectedBookId, "&book_name_supplemental=", selectedBookName), function (data) {
                            //alert(data);
                            $('#book_detail_result').html(data);
                        });
                    }
                    //if(document.getElementById('book_name_supplemental').
                    var e1 = document.getElementById("state");
                    var selectedStateId = e1.options[e1.selectedIndex].value;
                    var selectedStateName = e1.options[e1.selectedIndex].text;
                    if (selectedStateId != '') //inside this if statement should be re-written as per above and use REST
                    {
                        if (document.getElementById('book_detail_table'))
                            document.getElementById('book_detail_table').innerHTML = '';

                        $.get("includes/get/get_available_state_book_menu.php?state_id=".concat(selectedStateId, "&state_name=", selectedStateName), function (data) {
                            //alert(data);
                            $('#book_menu').html(data);
                        });
//                        $.post('includes/post/post_book_detail_processor.php', {post_book_id:book_id},
//                            function(data)
//                            {
//                                $('#book_detail_result').html(data);
//                                //alert(data);
//                            });
                        //document.getElementById('button').click();
                    }
                }
    </script>
</head>

<body>
<div class="container">
<section>
        <div class="form-group">
            <form class="form-horizontal" action="<?php $thispage ?>" method="post">
                <?php
                $select_field_state = new select_input($global_available_states_list,'State','state','');//$sql_execute[0]['book_state_id_fk']
                echo $select_field_state->create_select_field(true);
                echo "<br />";
                $select_field_stateless = new select_input($global_available_stateless_list,'Commercial Books','book_name_supplemental','');//$sql_execute[0]['book_id']
                echo $select_field_stateless->create_select_field(true);



                echo "<div class=\"col-lg-10\"><input style=\"display:none;\" id=\"button\" type=\"submit\" value=\"Submit\"></div>";
                ?>

            </form>
            <a id="add_link" href="book_add.php">Add New Book</a>
        </div>
    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-5" id="book_menu">


            <?php
//            if($sql_execute !== null)
//            {
//                $book_menu = new book_names_left_menu($sql_execute);
//                echo $book_menu->generate_book_name_menu();
//            }
            ?>

        </div>
        <div class="col-lg-9 col-md-8 col-sm-7">

            <article>
                <div id="book_detail_result"></div> <!--Result of the Book_detail displays here-->
            </article>
        </div>
    </div>
</section>

    <?php /** @var Site Navigation $navigation */
        echo $navigation = new site_nav(); ?>

	<footer>
		<p><?php        echo $_SESSION['user']->user_first_name . " " . $_SESSION['user']->user_last_name . " is logged in as " . $_SESSION['user']->username . ".";    ?></p>    <p>Copyright 2017 Loose in the Lab</p>
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
            $.post('includes/post/post_delete_lab_from_book_processor.php', {post_record_id: record_id},
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
