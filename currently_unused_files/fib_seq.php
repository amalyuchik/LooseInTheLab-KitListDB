<?php
/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 4/10/17
 * Time: 9:49 PM
 */

class fib_seq {

   function get_fib_number($fib_number)
   {
       $fib_num_array = array(0,1);

       if($fib_number == 1)
           return 0;
       elseif($fib_number == 2)
           return 1;
       else
       {
           for($i=2;$i<=$fib_number;$i++)
           {
               $fib_num_array[$i] = $fib_num_array[$i-2]+$fib_num_array[$i-1];

           }
           return $fib_num_array[$fib_number];
       }
       //0,1,1,2,3,5,8,13,21
   }

}