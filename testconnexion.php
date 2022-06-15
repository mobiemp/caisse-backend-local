<?php
//$HostName = '192.168.1.19';
//$DatabaseName = "mobipos";
//$HostUser = "root";
//$HostPass = "";
//$conn = new mysqli($HostName, $HostUser, $HostPass, $DatabaseName);
//
//$catalogue = $conn->query('SELECT * from table_client_catalogue');

//var_dump($catalogue->fetch_assoc());

//function ticketFormatString($str,$limit){
//    $row1 = $lastrow = str_repeat("*",$limit);
//
//    if(strlen($str)>$limit){
//        $splited = explode(' ',$str);
//
////        $first = count($splited) % 2 == 0 ? array_slice($splited,0,count($splited)/2) : array_slice($splited,0,((count($splited) + 1) / 2 ));
//        $first = array_slice($splited,0,((count($splited) / 2) +1 ));
//        $firstRow = implode(" ",$first);
//        $secondRow = array_slice($splited,count($first));
//        $secondRow = implode(" ", $secondRow);
//
//
//        $diff1 = strlen($firstRow) < $limit ? $limit  - strlen($firstRow) : strlen($firstRow);
//        $diff2 = strlen($secondRow) < $limit ? $limit  - strlen($secondRow) : strlen($secondRow);
//        $firstRow = $diff1%2 == 0 ? str_repeat("*",$diff1/2) . $firstRow . str_repeat("*",$diff1/2) : str_repeat("*",($diff1+1)/2) . $firstRow . str_repeat("*",($diff1-1)/2);
//        $secondRow = $diff2%2 == 0 ? str_repeat("*",$diff2/2) . $secondRow . str_repeat("*",$diff2/2) : str_repeat("*",($diff2+1)/2) . $secondRow . str_repeat("*",($diff2-1)/2);
//        return $row1 ."\n". $firstRow ."\n". $secondRow ."\n". $lastrow ;
//    }
//    else{
//        $diff = strlen($str) < $limit ? $limit  - strlen($str) : strlen($str);
////        $diff = mb_strtoupper($str, 'utf-8') == $str ? $diff / 1.5 : $diff;
//        $str = $diff%2 == 0 ? str_repeat("*",$diff/2) . $str . str_repeat("*",$diff/2) : str_repeat("*",($diff+1)/2) . $str . str_repeat("*",($diff-1)/2);
//        return $row1 ."\n". $str ."\n". $lastrow ;
//
//    }
//}
//
//$test = ticketFormatString("974 EMBALLAGES",26);
//file_put_contents('test.txt', $test);
//require('functions.php');
//$prix_total_ticket = number_format((float) 6.50, 2, '.','') . "€"  ;
//echo strlen($prix_total_ticket);
//var_dump(setStringLen($prix_total_ticket,8));

exec("git pull origin main",$output);
highlight_string("<?php\n\$data =\n" . var_export($output, true) . ";\n?>");

?>
