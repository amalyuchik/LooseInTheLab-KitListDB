<?php
/**
 * Created by PhpStorm.
 * User: amalyuchik
 * Date: 8/3/2017
 * Time: 9:50 AM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
$notes_css_style = 'peachy';
$warning_notes = "";
if (isset($_POST['book_state']) && $_POST['book_state'] != '')
{
   $book_state_id = $_POST['book_state'];
   $book_grade_id = $_POST['book_grade'];
   $book_name_supplemental = $_POST['book_name_supplemental'];
   $book_notes = $_POST['book_notes'];
   $field_value_array = array("book_state_id_fk"=>$book_state_id, "book_grade_id_fk"=>$book_grade_id, "book_name_supplemental"=>$book_name_supplemental, "book_notes"=>$book_notes, "book_date_created"=>$current_date);
   $field_value_check_array = array("book_state_id_fk"=>$book_state_id, "book_grade_id_fk"=>$book_grade_id, "book_notes"=>$book_notes);

   $table_array = array('books');
   if ($conn == null)
   {
       $conn = new connections();

       $check_existing_book_query = $conn->query_construct('read', $table_array, $field_value_check_array);
       $execute_check = $conn->runconn_sql_execute($connection_array,$check_existing_book_query);

       if (count($execute_check) < 1)
       {
           $create_query = $conn->query_construct('create',$table_array,$field_value_array);
           $create_book = $conn->runconn_sql_execute($connection_array, $create_query);
       }
       else
       {
           $notes_css_style = "class=\"error\"";
           $warning_notes = "<p style='color: #FF0000;size: 10px;font-weight: bold;'>Notes field must be unique.</p>";
       }
   }
}

?>
<!DOCTYPE html>
<html>
<head>
    <META NAME="ROBOTS" CONTENT="NONE">
    <?php echo $bootstrapLink; ?>
<?php echo $fontAwesomeLink; ?>
<link rel="stylesheet" href="css/nav_style.css" />
<style>

</style>
<title>Add a new book</title>

</head>

<body>
<div class="container">
    <div class="row">
        <section>
<h1><?php echo $lab_name;
/*
book_grade_id_fk
book_name -- optional
book_name_supplemental -- optional
book_notes
book_state_id_fk
book_date_created
*/?></h1>
            <form class="form-inline" action="<?php $thispage ?>" method="post">

                <?php
                $select_field_types = new select_input($global_states_list,'State','book_state',$book_state_id);
                echo $select_field_types->create_select_field(false);
                echo "&nbsp;";
                $select_field_categories = new select_input($global_grades_list,'Grade','book_grade',$book_grade_id);
                echo $select_field_categories->create_select_field(false);

                ?>
                <br /><br />
                <label for="book_name_supplemental">Book Name Supplemental: &nbsp;</label><br />
                <input type="text" size="60" id="book_name_supplemental" name="book_name_supplemental" value="<?php echo $book_name_supplemental; ?>"><br /><br />
                <label for="book_notes">Notes (this shows up in the book list when you select state): &nbsp;</label><br />
                <input type="text" size="60" id="book_notes" <?php echo $notes_css_style; ?> name="book_notes" value="<?php echo $book_notes; ?>"><br /><?php echo $warning_notes; ?><br />

                <input type="submit"  value="Save and add labs" >
            </form><br /><br />





        </section>
        <?php $navigation = new site_nav(); ?>

        <footer>
            <p>Copyright 2017 ASM</p>
            <div id="testing"></div>
        </footer>
    </div>
</div>
</body>
</html>