<?

define('PREFIJO', '');

function start_trackhack() {
    global $ses_usr_id, $REMOTE_ADDR, $SERVER_NAME, $HTTP_REFERER, $REMOTE_HOST, $PATH_INFO, $QUERY_STRING, $REQUEST_URI;

	$tracktime   = date("Y-m-d H:i:s", time());         # time now                    # WM010805
    $remote_addr = $REMOTE_ADDR;   # get remote IP
    $server      = $SERVER_NAME;   # website
    $referer     = $HTTP_REFERER;  # where are you coming from?
    $remote_host = $REMOTE_HOST;
    $uid         = session_id();

    if ($PATH_INFO!='') {
        if ($QUERY_STRING!='') {
            $requri = $PATH_INFO."?".$QUERY_STRING;
        }
        else {
            $requri = $PATH_INFO;
        }
    } 
    else {
        $requri = $REQUEST_URI;       # old way if no PATH_INFO 
    }

    $query_in = "insert into ".PREFIJO."tracking values ( '$ses_usr_id', '$uid', '$tracktime', '$remote_addr', '$remote_host', '$referer', '$requri')";
    tep_db_query( $query_in );

    $query =  "delete from tracking where tra_tracktime <= DATE_SUB( now(), INTERVAL 5 DAY)";
    tep_db_query( $query );

}

start_trackhack();          # Start the trackhack 

?>