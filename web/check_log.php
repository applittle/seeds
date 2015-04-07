<?php

$log_location = '/var/log/samba/';

exec('ls ' . $log_location, $ls);

$logs = array();

foreach ($ls as $val) {
    $parts = explode('.', $val);
    if (count($parts) === 5 or count($parts) === 1) { // Means IP add or non-related log
        continue;
    } else if (strcmp($parts[1], 'smbd') === 0) {
        continue;
    } else {
        array_push($logs, $val);
    }
}

$outArray = array();

foreach ($logs as $v) {
    $l = $log_location . $v;
    exec('tail -n 20 ' . $l, $log);
    $v = str_replace('log.', '', $v);

    for ($i = 0; $i < count($log); $i++) {
        if (preg_match('(\[\d{4}/.*\])', $log[$i], $date)) {
            $d = str_replace('[', '', $date[0]);
            $d = str_replace(']', '', $d);
            $dd = explode(',', $d);
            $i++;
            
            /**
            if (strstr($log[$i], 'ignore NBT'))
            {
                continue;
            }
             **/

            $val = str_replace('[', '', $log[$i]);
            $val = str_replace(']', '', $log[$i]);
            $array = array(
                'host' => $v,
                'date' => date('Y-m-d H:i:s', strtotime($dd[0])),
                'message' => $val,
            );
            array_push($outArray, $array);
        }
    }
}

echo json_encode($outArray);
?>