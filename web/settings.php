<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>アカウント管理</title>
        <link href="css/style.css" rel="stylesheet" type="text/css">
    </head>

    <?php
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // Using sqlite as DB source, create a new DB 'seeds' if not exists.
    $db = new SQLite3('./seeds.db');
    $settings_updated = False;

    if (isset($_POST['log_settings'])) {
        $log_path = $_POST['log_path'];
        $log_interval = $_POST['log_interval'];

        $sql = "UPDATE settings SET log_path = '$log_path', log_interval = '$log_interval'";
        $db->query($sql);

        $settings_updated = True;
    }

    // Fetch log settings info from DB.
    $sql = "SELECT * FROM log_settings";
    $result = $db->query($sql);
    if (!isset($result)) {
        $db->close();
        die("設定より監視ログを指定して下さい。");
    }

    while ($row = $result->fetchArray()) {
        $log_path = $row['log_path'];
        $log_interval = $row['log_interval'];
    }

    $db->close();
    ?>

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
                    <strong>各種設定</strong>
                    <table width="100%">
                        <tbody>
                            <tr>
                                <th>ログの位置</th>
                                <th>ログのインターバル</th>
                                <th></th>
                            </tr>
                        <form id="logForm" name="logForm" action="" method="POST">
                            <tr>
                            <input type="hidden" id="log_settings" name="log_settings" value="log_settings">
                            <td><input type="text" id="log_path" name="log_path" value="<?php echo $log_path ?>"></td>
                            <td><input type="text" id="log_interval" name="log_interval" value="<?php echo $log_interval ?>"></td>
                            <td><input type="submit" value="反映"></td>
                            </tr>
                        </form>
                        </tbody>
                    </table>
                    <?php
                    if ($settings_updated)
                    {
                        echo '<strong>設定を反映しました。</strong>';
                    }
                    ?>
                </div>

            </div>


        </div>
        <div class="footer">
            <div class="copyright">&copy;Seeds-Create</div>
        </div>

    </body>
</html>