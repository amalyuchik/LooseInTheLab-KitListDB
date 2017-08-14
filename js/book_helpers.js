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