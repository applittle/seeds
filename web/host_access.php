<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>アクセス制御</title>
        <link href="css/style.css" rel="stylesheet" type="text/css">
    </head>

    <body>   
        <div class="admin-box">

            <div class="header">
                <img src="img/logo.png" width="200" height="40" alt="" class="logo"/>　　　<a href="index.php">ログアウト</a> </div>

            <div class="left-box">
                <ul>
                    <li><a href="log.php">ログ監視</a></li>
                    <li><a href="host_access.php">アクセス制御</a></li>
                    <li><a href="settings.php">設定管理</a></li>
                </ul>
            </div>

            <div class="right-box">
                <div class="access-box" id="access-box">

                    <table width="100%">
                        <tbody>
                            <tr>
                        <form id="hostForm" name="hostForm" action="" method="POST">
                            <th>共有フォルダ追加</th>
                            <td>共有フォルダ名：<input type="text" id="share_name" name="share_name"></td>
                            <td>パス名：<input type="text" id="path" name="path"></td>
                            <td>ユーザ名（複数ユーザを追加する場合は,で区切る 例）takashi,takeshi,yumi）：<input type="text" id="users" name="users"></td>
                            <input type="hidden" id="action" name="action" value="add">
                            <td><input type="submit" id="add_folder" name="add_folder" value="追加"></td>
                        </form>
                        </tr>
                        </tbody>
                    </table>

                    <?php

                    function startsWith($haystack, $needle) {
                        // search backwards starting from haystack length characters from the end
                        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
                    }

                    function isShareFolder($str) {
                        $flag = False;
                        // Drills down and find [share] folders.
                        if (strpos($str, 'print') === false and strpos($str, 'global') === false) {
                            $flag = True;
                        }

                        return $flag;
                    }

                    function addShareInfoToFile($conf_file, $share_name, $path, $users) {
                        if (!file_exists($conf_file)) {
                            die('コンフィグファイルが見つかりません。設定を確認してください。');
                        }

                        $contents = file_get_contents($conf_file);
                        $contents .= "\n";
                        $contents .= "[" . $share_name . "]" . "\n";
                        $contents .= "path = " . $path . "\n";
                        $contents .= "writeable = yes" . "\n";
                        $contents .= "force create mode = 0666" . "\n";
                        $contents .= "force directory mode = 0777" . "\n";
                        $contents .= "valid users = " . $users . "\n";

                        file_put_contents($conf_file, $contents);
                        `/etc/init.d/samba restart`;
                    }

                    function extractShareInfoAndShow($conf_file) {
                        if (!file_exists($conf_file)) {
                            die('コンフィグファイルが見つかりません。設定を確認してください。');
                        }

                        $fp = fopen($conf_file, 'r');

                        $flag = False;
                        $share_name = '';
                        $path = '';
                        $users = '';

                        echo <<<EOF
                        <table width="100%">
                        <tbody>
                            <tr>
                                <th>共有フォルダ名</th>
                                <th>パス</th>
                                <th>ユーザ名</th>
                            </tr>
EOF;

                        if ($fp) {
                            if (flock($fp, LOCK_SH)) {
                                while (!feof($fp)) {
                                    $buffer = fgets($fp);

                                    if (!$flag) {
                                        // Drills down and find [share] folders for the first time.
                                        if (startsWith($buffer, '[')) {
                                            $flag = isShareFolder($buffer);
                                        }
                                    }

                                    if ($flag) {
                                        if (startsWith($buffer, '[')) {
                                            $flag = isShareFolder($buffer); // Checks flag state
                                            if (!$flag) {
                                                echo '<td><input type="submit" value="変更反映"></td>';
                                                echo '</form>';
                                                echo '</tr>';
                                                continue;
                                            } else {
                                                if (!empty($share_name) and ! empty($path)) {
                                                    echo '<td><input type="submit" value="変更反映"></td>';
                                                    echo '</form>';
                                                    echo '</tr>';
                                                }

                                                $share_name = substr(trim($buffer), 1, -1);
                                                echo "<tr><form id='accessForm' name='accessForm' action='' method='POST'>";
                                                echo "<td><input type='text' id='share_name' name='share_name' value='$share_name'></td>";
                                                echo "<input type='hidden' id='action' name='action' value='modify'>";
                                            }
                                        }

                                        if (strrpos($buffer, 'path') !== false) {
                                            $t = explode('=', $buffer);
                                            $path = $t[1];
                                            echo "<td><input type='text' id='path' name='path' value='$path'></td>";
                                        }

                                        if (strrpos($buffer, 'writeable') !== false) {
                                            $t = explode('=', $buffer);
                                            $writeable = $t[1];
                                        }

                                        if (strrpos($buffer, 'force create mode') !== false) {
                                            $t = explode('=', $buffer);
                                            $force_create_mode .= $t[1];
                                        }

                                        if (strrpos($buffer, 'valid users') !== false) {
                                            $t = explode('=', $buffer);
                                            $users = $t[1];
                                            echo "<td><input type='text' id='users' name='users' value='$users'></td>";
                                        }

                                        if (strrpos($buffer, 'guest ok') !== false) {
                                            $t = explode('=', $buffer);
                                            $guest_ok .= $t[1];
                                        }
                                    }
                                }

                                flock($fp, LOCK_UN);
                            } else {
                                print('ファイルロックに失敗しました');
                            }
                        }

                        if ($flag) {
                            echo '<td><input type="submit" value="変更反映"></td>';
                            echo '</form>';
                            echo '</tr>';
                        }

                        fclose($fp);

                        echo '</tbody>';
                        echo '</table>';
                    }

                    if (session_status() !== PHP_SESSION_ACTIVE) {
                        session_start();
                    }
                    $userid = $_SESSION['USERID'];
                    // Using sqlite as DB source, create a new DB 'seeds' if not exists.
                    $db = new SQLite3('./seeds.db');
                    // Fetch host access info from DB.
                    $sql = "SELECT config_path FROM settings";
                    $result = $db->query($sql);
                    while ($row = $result->fetchArray()) {
                        $config_path = $row['config_path'];
                    }

                    if ($_POST["action"] == "add") {
                        // Then insert host info
                        $share_name = $_POST["share_name"];
                        $path = $_POST['path'];
                        $users = $_POST['users'];
                        addShareInfoToFile($config_path, $share_name, $path, $users);
                    }

                    if ($_POST["action"] == "modify") {
                        // Then insert host info
                        $share_name = $_POST["share_name"];
                        $path = $_POST['path'];
                        $users = $_POST['users'];
                    }

                    // Retrieves share info from config file.
                    extractShareInfoAndShow($config_path);

                    $db->close();
                    ?> 

                </div>

            </div>


        </div>
        <div class="footer">
            <div class="copyright">&copy;Seeds-Create</div>
        </div>

    </body>
</html>