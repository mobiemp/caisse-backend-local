<?php

function regenerePanier($conn,$sql,$jsonfile){

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {

        while ($row[] = $result->fetch_assoc()) {

            $tem = $row;

            $json = $tem;
        }
        $fp = fopen($jsonfile, 'w');
        fwrite($fp, json_encode($json));
        fclose($fp);

        return $json;
    }else{
        $fp = fopen($jsonfile, 'w');
        fwrite($fp, json_encode([]));
        fclose($fp);
    }

}

function response($json,$res){
    return array('json'=>$json,'result'=>$res);
}

function errorResponse($message,$response){
    echo json_encode(array('message'=>$message,'response'=>$response));
    exit();
}

function successResponse($message,$response){
    echo json_encode(array('message'=>$message,'response'=>$response));
}

function ticketFormatString($str,$limit){
    $row1 = $lastrow = str_repeat("*",$limit);

    if(strlen($str)>$limit){
        $splited = explode(' ',$str);

//        $first = count($splited) % 2 == 0 ? array_slice($splited,0,count($splited)/2) : array_slice($splited,0,((count($splited) + 1) / 2 ));
        $first = array_slice($splited,0,((count($splited) / 2) +1 ));
        $firstRow = implode(" ",$first);
        $secondRow = array_slice($splited,count($first));
        $secondRow = implode(" ", $secondRow);


        $diff1 = strlen($firstRow) < $limit ? $limit  - strlen($firstRow) : strlen($firstRow);
        $diff2 = strlen($secondRow) < $limit ? $limit  - strlen($secondRow) : strlen($secondRow);
        $firstRow = $diff1%2 == 0 ? str_repeat("*",$diff1/2) . $firstRow . str_repeat("*",$diff1/2) : str_repeat("*",($diff1+1)/2) . $firstRow . str_repeat("*",($diff1-1)/2);
        $secondRow = $diff2%2 == 0 ? str_repeat("*",$diff2/2) . $secondRow . str_repeat("*",$diff2/2) : str_repeat("*",($diff2+1)/2) . $secondRow . str_repeat("*",($diff2-1)/2);
        return $row1 ."\n". $firstRow ."\n". $secondRow ."\n". $lastrow ;
    }
    else{
        $diff = strlen($str) < $limit ? $limit  - strlen($str) : strlen($str);
//        $diff = mb_strtoupper($str, 'utf-8') == $str ? $diff / 1.5 : $diff;
        $str = $diff%2 == 0 ? str_repeat("*",$diff/2) . $str . str_repeat("*",$diff/2) : str_repeat("*",($diff+1)/2) . $str . str_repeat("*",($diff-1)/2);
        return $row1 ."\n". $str ."\n". $lastrow ;

    }
}

function setStringLen($str,$limit,$eur = false){
    if($eur){
        return strlen($str) <= $limit ? $str . "€" . str_repeat(" ", $limit - strlen($str)) : substr($str,0,$limit-1)."€" ;
    }
    return strlen($str) <= $limit ? $str . str_repeat(" ", $limit - strlen($str)) : substr($str,0,$limit-1) . str_repeat(" ",1);
}

function validate_EAN13Barcode($barcode)
{
    // check to see if barcode is 13 digits long
    if (!preg_match("/^[0-9]{13}$/", $barcode)) {
        return false;
    }

    $digits = $barcode;

    // 1. Add the values of the digits in the 
    // even-numbered positions: 2, 4, 6, etc.
    $even_sum = $digits[1] + $digits[3] + $digits[5] +
        $digits[7] + $digits[9] + $digits[11];

    // 2. Multiply this result by 3.
    $even_sum_three = $even_sum * 3;

    // 3. Add the values of the digits in the 
    // odd-numbered positions: 1, 3, 5, etc.
    $odd_sum = $digits[0] + $digits[2] + $digits[4] +
        $digits[6] + $digits[8] + $digits[10];

    // 4. Sum the results of steps 2 and 3.
    $total_sum = $even_sum_three + $odd_sum;

    // 5. The check character is the smallest number which,
    // when added to the result in step 4, produces a multiple of 10.
    $next_ten = (ceil($total_sum / 10)) * 10;
    $check_digit = $next_ten - $total_sum;

    // if the check digit and the last digit of the 
    // barcode are OK return true;
    if ($check_digit == $digits[12]) {
        return true;
    }

    return false;
}

function random_strings($length_of_string)
{
 
    // String of all alphanumeric character
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
 
    // Shuffle the $str_result and returns substring
    // of specified length
    return substr(str_shuffle($str_result),
                       0, $length_of_string);
}

function checkIfBarcodeExist($barcode,$conn,$action)
{
    $existGencode = 'false';
    if($action == 'creer'){
        do {
            $sql = "SELECT ref FROM table_client_catalogue WHERE ref = '$barcode' ";
            $result = $conn->query($sql);
            $nbligne = $result->num_rows;
            $existGencode = $nbligne > 0 ? 'true' : 'false';
        } while ($existGencode == 'true');
        return $existGencode;
    }
    else{
        $sql = "SELECT ref FROM table_client_catalogue WHERE ref = '$barcode' ";
        $result = $conn->query($sql);
        $nbligne = $result->num_rows;
        if($nbligne>0){
            return 0;
        } 
        else{
            return $existGencode;
        }
    }
    

    
}

function connexionDb($HostName, $HostUser, $HostPass, $DatabaseName)
{

    return new mysqli($HostName, $HostUser, $HostPass, $DatabaseName);
}

