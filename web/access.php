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
                    <li><a href="access.php">アクセス制御</a></li>
                    <li><a href="settings.php">設定管理</a></li>
                </ul>
            </div>

            <div class="right-box">
                <div class="access-box" id="access-box">

                    <table width="100%">
                        <tbody>
                            <tr>
                        <form id="hostForm" name="hostForm" action="" method="POST">
                            <th>IPアドレス、ホスト名（オプション）追加</th>
                            <td>IPアドレス：<input type="text" id="ip_address" name="ip_address"></td>
                            <td>ホスト名：<input type="text" id="hostname" name="hostname"></td>
                            <input type="hidden" id="need_add" name="need_add" value="need_add">
                            <td><input type="submit" id="add_host" name="add_host" value="追加"></td>
                        </form>
                        </tr>
                        </tbody>
                    </table>

                    <?php
                    if (session_status() !== PHP_SESSION_ACTIVE) {
                        session_start();
                    }
                    $userid = $_SESSION['USERID'];
                    // Using sqlite as DB source, create a new DB 'seeds' if not exists.
                    $db = new SQLite3('./seeds.db');

                    // Triggers add_host action
                    if (isset($_POST["need_add"])) {
                        if (empty($_POST["ip_address"])) {
                            die('IPアドレスは必須項目です。');
                        } elseif (!preg_match("/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/", $_POST["ip_address"])) {
                            die('正しいIPアドレスを入力して下さい。');
                        }

                        // Then insert host info
                        $ip = $_POST["ip_address"];
                        $host = empty($_POST["hostname"]) ? null : $_POST["hostname"];
                        $sql = "INSERT INTO host_access_info (ipaddress, hostname) VALUES ('$ip', '$host')";
                        $db->query($sql);
                    }

                    // Triggers enable/disable host access.
                    if (isset($_POST["access_state"])) {
                        $state = $_POST["access_state"];
                        $ip = $_POST["ip_address"];
                        $host = empty($_POST["hostname"]) ? null : $_POST["hostname"];
                        $sql = "UPDATE host_access_info SET enabled = '$state' WHERE ipaddress = '$ip'";
                        $db->query($sql);

                        if ($state == '0') {
                            // Then enable access by dropping restriction to the host
                            `sudo iptables -A INPUT -m udp -p udp -s $ip --dport 137:138 -j DROP`;
                            `sudo iptables -A INPUT -m tcp -p tcp -s $ip --dport 139 -j DROP`;
                        } else {
                            `sudo iptables -D INPUT -m udp -p udp -s $ip --dport 137:138 -j DROP`;
                            `sudo iptables -D INPUT -m tcp -p tcp -s $ip --dport 139 -j DROP`;
                        }

                        `sudo iptables save`;
                        `sudo iptables restart`;
                    }

                    // Fetch host access info from DB.
                    $sql = "SELECT * FROM host_access_info";
                    $result = $db->query($sql);
                    if (!isset($result)) {
                        $db->close();
                        echo "表示できる情報がありません。";
                    } else {
                        echo <<<EOF
                        <table width="100%">
                        <tbody>
                            <tr>
                                <th>PC名</th>
                                <th>状態</th>
                                <th>アクセス許可</th>
                            </tr>
EOF;

                        while ($row = $result->fetchArray()) {
                            $ip = $row['ipaddress'];
                            $host = $row['hostname'];
                            $enabled = $row['enabled'] == '1' ? '可' : '不可';

                            echo '<tr>';
                            echo "<th>$ip</th>";
                            echo "<th>$enabled</th>";

                            if ($row['enabled'] == '1') {
                                echo '<td><form id="accessForm" name="accessForm" action="" method="POST">';
                                echo "<input type='hidden' id='ip_address' name='ip_address' value='$ip'>";
                                echo "<input type='hidden' id='hostname' name='hostname' value='$host'>";
                                echo '<input type="hidden" id="access_state" name="access_state" value="0">';
                                echo '<input type="submit" value="不可にする">';
                                echo '</form></td>';
                            } else {
                                echo '<td><form id="accessForm" name="accessForm" action="" method="POST">';
                                echo "<input type='hidden' id='ip_address' name='ip_address' value='$ip'>";
                                echo "<input type='hidden' id='hostname' name='hostname' value='$host'>";
                                echo '<input type="hidden" id="access_state" name="access_state" value="1">';
                                echo '<input type="submit" value="可にする">';
                                echo '</form></td>';
                            }

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