<?php
/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 4/19/17
 * Time: 1:07 AM
 */
class lab_edit_data_object
{
    var $lab_edit_lab_id = '';
    var $lab_edit_product_id = '';
    var $lab_edit_classroom_qty = '';
    var $lab_edit_refill_qty = '';
    var $lab_edit_refill_qty_detail = '';
    var $lab_edit_participant_qty = '';
    var $lab_edit_presenter_qty = '';
    var $lab_edit_retail_qty = '';
    var $lab_edit_retail_qty_detail = '';

    function __construct($lab_edit_classroom_qty, $lab_edit_lab_id, $lab_edit_participant_qty, $lab_edit_presenter_qty, $lab_edit_product_id, $lab_edit_refill_qty, $lab_edit_refill_qty_detail, $lab_edit_retail_qty, $lab_edit_retail_qty_detail)
    {
        $this->lab_edit_classroom_qty = $lab_edit_classroom_qty;
        $this->lab_edit_lab_id = $lab_edit_lab_id;
        $this->lab_edit_participant_qty = $lab_edit_participant_qty;
        $this->lab_edit_presenter_qty = $lab_edit_presenter_qty;
        $this->lab_edit_product_id = $lab_edit_product_id;
        $this->lab_edit_refill_qty = $lab_edit_refill_qty;
        $this->lab_edit_refill_qty_detail = $lab_edit_refill_qty_detail;
        $this->lab_edit_retail_qty = $lab_edit_retail_qty;
        $this->lab_edit_retail_qty_detail = $lab_edit_retail_qty_detail;
    }

}