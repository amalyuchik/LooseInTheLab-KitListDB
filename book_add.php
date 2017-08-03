<?php
/**
 * Created by PhpStorm.
 * User: amalyuchik
 * Date: 8/3/2017
 * Time: 9:50 AM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
if (isset($_POST['book_state']) && $_POST['book_state'] != '')
{
   $book_state_id = $_POST['book_state'];
   $book_grade_id = $_POST['book_grade'];
   $book_name_supplemental = $_POST['book_name_supplemental'];
   $book_notes = $_POST['book_notes'];
   $field_value_array = array("book_state_id_fk"=>$book_state_id, "book_grade_id_fk"=>$book_grade_id, "book_name_supplemental"=>$book_name_supplemental, "book_notes"=>$book_notes, "book_date_created"=>$current_date);
   $table_array = array('books');
   if ($conn == null)
   {
       $conn = new connections();
       $create_query = $conn->query_construct('create',$table_array,$field_value_array);
       echo $create_query;
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
                $select_field_types = new select_input($global_states_list,'State','book_state','');
                echo $select_field_types->create_select_field(false);
                echo "&nbsp;";
                $select_field_categories = new select_input($global_grades_list,'Grade','book_grade','');
                echo $select_field_categories->create_select_field(false);

                ?>
                <br /><br />
                <label for="book_name_supplemental">Book Name Supplemental: &nbsp;</label><br />
                <input type="text" size="60" id="book_name_supplemental" name="book_name_supplemental" value=""><br /><br />
                <label for="book_notes">Notes: &nbsp;</label><br />
                <input type="text" size="60" id="book_notes" name="book_notes" value=""><br /><br />
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