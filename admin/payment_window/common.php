<?php
function is_leap_year($year) {
  return date('L', strtotime("$year-01-01"));
}

function validate_date($year,$month,$day){
    if (($month == 1) || ($month == 6) || ($month == 9) || ($month == 11)):
        if ($day == 31):
            $l_new_day = '30';
        else:
            $l_new_day = $day;
        endif;
    elseif ($month == 2):
        if ($day > 28):
            $l_leap = is_leap_year($year);

            if ($l_leap):
                $l_new_day = '28';
            else:
                $l_new_day = '29';
            endif;

        else:
            $l_new_day = $day;
        endif;

    else:
        $l_new_day = $day;


    endif;


    return $l_new_day;

}


function auto_date($cnx2,$id,$last_day, $date) {
  
    $date_arr = date_parse($date);
    $year = $date_arr['year'];
    $month = $date_arr['month'];
    $day = $date_arr['day'];

    // Prepare and execute the query
    $l_sql = "SELECT c_retention, c_change_date, c_restructured, c_date_of_sale 
    FROM t_buyers_account
    WHERE c_account_no = '$id'";
    $l_qry = odbc_exec($cnx2, $l_sql);

    if (!$l_qry) {
    die('Error in SQL query: ' . odbc_errormsg($cnx2));
    }

    // Fetch results
    if ($row = odbc_fetch_array($l_qry)) {
    $change_date = $row['c_change_date'];
    $date_of_sale = $row['c_date_of_sale'];
    $day_of_Sale = substr($date_of_sale,8,2);
    }

    $l_leap = ($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0);

    if ($day_of_Sale == 31 && $last_day >= 28 && $change_date != 1) {
        $last_day = 31;
       
    }

    
    if ($month == 1):
        if ($last_day < 28):
            $dt = new DateTime($date);
            $dt->modify('+31 days');
            $l_result = $dt->format('Y-m-d');     
        else:
            if ($l_leap):
                  $l_result = $year .'-02-29';
            else:
                  $l_result = $year .'-02-28';
               
            endif;

        endif;
    elseif($month == 2):
        if ($last_day > 28):
            if($last_day == 29):
                $l_result = $year .'-03-29'; 
            elseif($last_day == 30):
                $l_result = $year .'-03-30'; 
            elseif($last_day == 31):
                $l_result = $year .'-03-31';
            endif;
        else:

            if($l_leap):
                $dt = new DateTime($date);
                $dt->modify('+29 days');
                $l_result = $dt->format('Y-m-d');
            else:
                $dt = new DateTime($date);
                $dt->modify('+28 days');
                $l_result = $dt->format('Y-m-d');
            endif;
        endif;
    
    elseif($month == 3 or $month == 5 or $month == 7 or $month == 8 or $month == 10 or $month == 12):
        if($month ==7 or $month == 12):
              if($last_day >= 30):
                  $l_date1 = $year .'-' .$month . '-'. $last_day ; 
                  $dt = new DateTime($l_date1);
                  $dt->modify('+31 days');
              else:
                  $l_date1 = $date ; 
                  $dt = new DateTime($l_date1);
                  $dt->modify('+31 days');
              endif;
        else:
           
              if ($last_day <= 30):
                  $l_date1 = $date ; 
                 
                  $month = intval($month) + 1;
                  $l_date1 = $year .'-' . $month . '-'. $last_day ; 
            
                  $dt = new DateTime($l_date1);
                  
               
              else:
                  
                  $l_date1 = $year .'-'.$month . '-'. '30'; 
                  $dt = new DateTime($l_date1);
                  $dt->modify('+31 days');   
              endif;     
              
        endif;
      
        $l_result = $dt->format('Y-m-d');

    elseif($month == 4 or $month == 6 or $month == 9 or $month == 11):
          if ($last_day == 31):
                $month = intval($month) + 1;
                $l_date1 = $year .'-' . $month . '-'. $last_day ; 
                $dt = new DateTime($l_date1);
                $l_result = $dt->format('Y-m-d');
          else:
                $dt = new DateTime($date);
                $dt->modify('+30 days');
                $l_result = $dt->format('Y-m-d');
          endif;
    else:
          
    endif;

    return $l_result;

}

function auto_date2($cnx2,$id, $last_day,$date) {
    $date_arr = date_parse($date);
    $year = $date_arr['year'];
    $month = $date_arr['month'];
    $day = $date_arr['day'];

    // Prepare and execute the query
    $l_sql = "SELECT c_retention, c_change_date, c_restructured, c_date_of_sale 
              FROM t_buyers_account
              WHERE c_account_no = '$id'";
    $l_qry = odbc_exec($cnx2, $l_sql);

    if (!$l_qry) {
        die('Error in SQL query: ' . odbc_errormsg($cnx2));
    }

    // Fetch results
    if ($row = odbc_fetch_array($l_qry)) {
        $change_date = $row['c_change_date'];
        $date_of_sale = $row['c_date_of_sale'];
        $day_of_sale = (int) substr($date_of_sale, 8, 2);
    }
    if ($day_of_sale == 31 && $last_day >= 28 && $change_date != 1) {
        $last_day = 31;
       
    }
    $dt = new DateTime($date);

    // Add one month
    $dt->modify('+1 month');

    // If the day is greater than the last day of the new month, adjust to the last day
    $last_day_of_new_month = $dt->format('t'); // 't' gives the last day of the month
    if ($last_day > $last_day_of_new_month) {
        $dt->setDate($dt->format('Y'), $dt->format('m'), $last_day_of_new_month);
    } else {
        $dt->setDate($dt->format('Y'), $dt->format('m'), $last_day);
    }

    return $dt->format('Y-m-d');
}



?>
