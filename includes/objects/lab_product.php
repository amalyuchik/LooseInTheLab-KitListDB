<?php
/**
 * Created by PhpStorm.
 * User: amalyuchik
 * Date: 7/21/2017
 * Time: 1:45 PM
 */

class lab_product
{
    var $lab_product_line_id;
    var $lab_product_line_id_fk;
    var $lab_product_line_product_category;
    var $lab_product_line_product_id_fk;
    var $lab_product_line_classroom_qty;
    var $lab_product_line_participant_qty;
    var $lab_product_line_presenter_qty;
    var $lab_product_line_refill_qty;
    var $lab_product_line_refill_qty_detail;
    var $lab_product_line_retail_qty;
    var $lab_product_line_retail_qty_detail;
    var $lab_product_line_is_reusable;
    var $lab_product_line_created_date;
    var $lab_product_line_modified_date;

    //Virtual fields
    var $lab_product_line_name;
    var $lab_product_line_product_price;

    /**
     * lab_product constructor.
     * @param $lab_product_line_id
     * @param $lab_product_line_id_fk
     * @param $lab_product_line_product_category
     * @param $lab_product_line_product_id_fk
     * @param $lab_product_line_classroom_qty
     * @param $lab_product_line_participant_qty
     * @param $lab_product_line_presenter_qty
     * @param $lab_product_line_refill_qty
     * @param $lab_product_line_refill_qty_detail
     * @param $lab_product_line_retail_qty
     * @param $lab_product_line_retail_qty_detail
     * @param $lab_product_line_is_reusable
     * @param $lab_product_line_created_date
     * @param $lab_product_line_modified_date
     * @param $lab_product_line_name
     * @param $lab_product_line_product_price
     */
    public function __construct($lab_product_line_id, $lab_product_line_id_fk, $lab_product_line_product_category, $lab_product_line_product_id_fk, $lab_product_line_classroom_qty, $lab_product_line_participant_qty, $lab_product_line_presenter_qty, $lab_product_line_refill_qty, $lab_product_line_refill_qty_detail, $lab_product_line_retail_qty, $lab_product_line_retail_qty_detail, $lab_product_line_is_reusable, $lab_product_line_created_date, $lab_product_line_modified_date, $lab_product_line_name, $lab_product_line_product_price)
    {
        $this->lab_product_line_id = $lab_product_line_id;
        $this->lab_product_line_id_fk = $lab_product_line_id_fk;
        $this->lab_product_line_product_category = $lab_product_line_product_category;
        $this->lab_product_line_product_id_fk = $lab_product_line_product_id_fk;
        $this->lab_product_line_classroom_qty = $lab_product_line_classroom_qty;
        $this->lab_product_line_participant_qty = $lab_product_line_participant_qty;
        $this->lab_product_line_presenter_qty = $lab_product_line_presenter_qty;
        $this->lab_product_line_refill_qty = $lab_product_line_refill_qty;
        $this->lab_product_line_refill_qty_detail = $lab_product_line_refill_qty_detail;
        $this->lab_product_line_retail_qty = $lab_product_line_retail_qty;
        $this->lab_product_line_retail_qty_detail = $lab_product_line_retail_qty_detail;
        $this->lab_product_line_is_reusable = $lab_product_line_is_reusable;
        $this->lab_product_line_created_date = gmdate($lab_product_line_created_date);
        $this->lab_product_line_modified_date = gmdate($lab_product_line_modified_date);

        //Virtual fields
        $this->lab_product_line_name = $lab_product_line_name;
        $this->lab_product_line_product_price = $lab_product_line_product_price;
    }

}
