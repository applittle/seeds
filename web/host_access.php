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
                            <td>ユーザ名（複数ユーザを追加する場合は,で区切る 例）takashi,takeshi,yumi）：<input type="text" id="user_name" name="user_name"></td>
                            <input type="hidden" id="action" name="action" value="need_add">
                            <td><input type="submit" id="add_host" name="add_host" value="追加"></td>
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
                    
                    function addShareInfo($conf_file, $db, $share_name, $path, $users)
                    {
                        $contents = file_get_contents($conf_file);
                    }

                    function updateShareInfo($conf_file, $db, $share_name, $path, $users) {
                        $fp = fopen($conf_file, 'r');
                        $newLines = '';
                        $isShareExists = False;
                        if ($fp) {
                            if (flock($fp, LOCK_SH)) {
                                while (!feof($fp)) {
                                    $buffer = fgets($fp);
                                    if (startsWith($buffer, '[')) {
                                        $share_name_old = substr($buffer, 1, -1);
                                        if ($share_name_old == $share_name) {
                                            $isShareExists = True;
                                        }
                                    }

                                    if (strrpos($buffer, 'path') !== false and $isShareExists) {
                                        $t = explode('=', $buffer);
                                        $p = $t[0] . ' = ' . $path;
                                        $newLines .= $p;
                                        continue;
                                    }

                                    if (strrpos($buffer, 'valid users') !== false and $isShareExists) {
                                        $t = explode('=', $buffer);
                                        $p = $t[0] . ' = ' . $users;
                                        $newLines .= $p;
                                        continue;
                                    }

                                    $sql = "update folder_access_info set share_name='$share_name', path='$path', users='$users' where share_name='$share_name'";
                                    $db->query($sql);
                                }
                                flock($fp, LOCK_UN);
                            } else {
                                print('ファイルロックに失敗しました');
                            }
                        }

                        fclose($fp);
                    }

                    if (session_status() !== PHP_SESSION_ACTIVE) {
                        session_start();
                    }
                    $userid = $_SESSION['USERID'];
                    // Using sqlite as DB source, create a new DB 'seeds' if not exists.
                    $db = new SQLite3('./seeds.db');

                    if ($_POST["action"] == "need_add") {
                        // Then insert host info
                        $share_name = $_POST["share_name"];
                        $path = $_POST['path'];
                        $users = $_POST['users'];
                        $sql = "INSERT INTO folder_access_info (share_name, path, users) VALUES ('$share_name', '$path', '$users')";
                        $db->query($sql);
                    }

                    if ($_POST["action"] == "modify") {
                        // Then insert host info
                        $share_name = $_POST["share_name"];
                        $path = $_POST['path'];
                        $users = $_POST['users'];
                        $sql = "UPDATE folder_access_info SET share_name='$share_name', path='$path', users='$users'";
                        $db->query($sql);
                    }

                    // Fetch host access info from DB.
                    $sql = "SELECT * FROM folder_access_info";
                    $result = $db->query($sql);
                    if (!isset($result)) {
                        $db->close();
                        echo "表示できる情報がありません。";
                    } else {
                        echo <<<EOF
                        <table width="100%">
                        <tbody>
                            <tr>
                                <th>共有フォルダ名</th>
                                <th>パス</th>
                                <th>ユーザ名</th>
                            </tr>
EOF;

                        while ($row = $result->fetchArray()) {
                            $share_name = $row['share_name'];
                            $path = $row['path'];
                            $users = $row['users'];

                            echo '<tr>';
                            echo '<form id="accessForm" name="accessForm" action="" method="POST">';
                            echo "<td>$share_name</td>";
                            echo "<td><input type='text' id='folder_path' name='folder_path' value='$path'></td>";
                            echo "<td><input type='text' id='users' name='users' value='$users'></td>";
                            echo "<input type='hidden' id='action' name='action' value='modify'>";
                            echo '<input type="submit" value="変更反映">';
                            echo '</form>';
                            echo '</tr>';
                        }
                    }
                    echo '</tbody>';
                    echo '</table>';

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