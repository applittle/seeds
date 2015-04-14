<?php

function grepAndFormatLog($message) {
    $array = null;

    // 1. Connect to share pattern
    preg_match("/connect to service (.*) initially as user/", $message, $array);
    if (isset($array[1])) {
        $retArray = array('location' => $array[1], 'message' => '共有フォルダに接続しました。');
        return $retArray;
    }

    // 2. Open some file
    preg_match("/opened file (.*) read/", $message, $array);
    if (isset($array[1])) {
        $retArray = array('location' => $array[1], 'message' => 'ファイルを生成、または開きました。');
        return $retArray;
    }

    // 3. Close some file
    preg_match("/closed file (.*) (numopen/", $message, $array);
    if (isset($array[1])) {
        $retArray = array('location' => $array[1], 'message' => 'ファイルを閉じました。');
        return $retArray;
    }

    // 4. Close the connection to share
    preg_match("/closed connection to service (.*)/", $message, $array);
    if (isset($array[1])) {
        $retArray = array('location' => $array[1], 'message' => '共有フォルダへの接続を切断しました。');
        return $retArray;
    }

    return null;
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$userid = $_SESSION['USERID'];
// Using sqlite as DB source, create a new DB 'seeds' if not exists.
$db = new SQLite3('./seeds.db');

// Fetch log settings info from DB.
$sql = "SELECT * FROM log_settings";
$result = $db->query($sql);
if (!isset($result)) {
    $db->close();
    die("設定より監視ログを指定して下さい。");
}

while ($row = $result->fetchArray()) {
    $log_location = $row['log_path'];
}

$db->close();

exec('ls ' . $log_location, $ls);

$logs = array();

foreach ($ls as $val) {
    $parts = explode('.', $val);
    if (count($parts) === 5 or count($parts) === 1) { // Means IP add or non-related log
        continue;
    } else if (strcmp($parts[1], 'smbd') === 0 or strcmp($parts[1], 'nmbd') === 0 or strcmp($parts[1], '%m') === 0) {
        continue;
    } else {
        array_push($logs, $val);
    }
}

$outArray = array();

foreach ($logs as $v) {
    $l = $log_location . $v;
    exec('tail -n 10000 ' . $l, $log);
    $v = str_replace('log.', '', $v);

    for ($i = 0; $i < count($log); $i++) {
        if (preg_match('(\[\d{4}/.*\])', $log[$i], $date)) {
            $d = str_replace('[', '', $date[0]);
            $d = str_replace(']', '', $d);
            $dd = explode(',', $d);
            $i++;

            $val = str_replace('[', '', $log[$i]);
            $val = str_replace(']', '', $log[$i]);
            $retArray = grepAndFormatLog($val);

            if (isset($retArray)) {
                $array = array(
                    'host' => $v,
                    'date' => date('Y-m-d H:i:s', strtotime($dd[0])),
                    'location' => $retArray['location'],
                    'message' => $retArray['message'],
                );
                array_push($outArray, $array);
            }
        }
    }
}

echo json_encode($outArray);
?>