<?php
/**
 * @file timetool.php
 * @date 2018-09-26
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Time tools
 */

/* Query a time server (C) 1999-09-29, Ralf D. Kloth (QRQ.software) <ralf at qrq.de> */
if(!check_function_exists("query_time_server")) {
  function query_time_server($timeserver, $socket) {
      // parameters: server, socket, error code, error text, timeout
      $fp = fsockopen($timeserver,$socket,$err,$errstr,5); 
      if($fp) {
          fputs($fp, "\n");
          $timevalue = fread($fp, 49);
          fclose($fp); // close the connection
      } else {
          $timevalue = " ";
      }

      $ret = array();
      $ret[] = $timevalue;
      $ret[] = $err;     // error code
      $ret[] = $errstr;  // error text

      return $ret;
  }
}

if(!check_function_exists("get_server_time")) {
  function get_server_time($timeserver="time.bora.net") {
    $timestamp = false;
    $timercvd = query_time_server($timeserver, 37);

    //if no error from query_time_server
    if(!$timercvd[1]) {
        $timevalue = bin2hex($timercvd[0]);
        $timevalue = abs(HexDec('7fffffff') - HexDec($timevalue) - HexDec('7fffffff'));
        $timestamp = $timevalue - 2208988800; // convert to UNIX epoch time stamp
        //$datum = date("Y-m-d (D) H:i:s", $tmestamp - date("Z", $timestamp)); // incl time zone offset
        //$doy = (date("z", $tmestamp) + 1);

        //echo "Time check from time server ", $timeserver, " : [<font color=\"red\">",$timevalue,"</font>]";
        //echo " (seconds since 1900-01-01 00:00.00).<br>\n";
        //echo "The current date and universal time is ",$datum," UTC. ";
        //echo "It is day ",$doy," of this year.<br>\n";
        //echo "The unix epoch time stamp is $timestamp.<br>\n";
        //echo date("d/m/Y H:i:s", $timestamp);
    } else {
        //echo "Unfortunately, the time server $timeserver could not be reached at this time. ";
        //echo "$timercvd[1] $timercvd[2].<br>\n";
        set_error($timercvd[1] . " " . $timercvd[2]);
        show_errors();
    }

    return $timestamp;
  }
}
