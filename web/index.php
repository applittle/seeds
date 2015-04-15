<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>ログイン</title>
        <link href="css/style.css" rel="stylesheet" type="text/css">
    </head>

    <?php
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // Using sqlite as DB source, create a new DB 'seeds' if not exists.
    $db = new SQLite3('./seeds.db');

    $sql = <<<EOF
CREATE TABLE IF NOT EXISTS `account_info` (
  userid varchar(20) UNIQUE NOT NULL,
  password varchar(100) NOT NULL,
  email varchar(100) DEFAULT NULL
)
EOF;
    $db->query($sql);

    /** For storing host access info
     * enabled 0 for disabled, 1 for enabled.
     * As default is is enabled.
     * */
    $sql = <<<EOF
CREATE TABLE IF NOT EXISTS `host_access_info` (
  ipaddress varchar(30) UNIQUE NOT NULL,
  hostname varchar(20) DEFAULT NULL,
  enabled varchar(2) DEFAULT '1'
)
EOF;
    $db->query($sql);

    /** For storing settings for Logazer.
     * log_path to specify the log should be traced
     * log interval to specify the log interval
     * */
    $sql = <<<EOF
CREATE TABLE IF NOT EXISTS `log_settings` (
  log_path varchar(50) NOT NULL,
  log_interval varchar(5) NOT NULL
)
EOF;
    $db->query($sql);

    $sql = "SELECT log_path FROM log_settings";
    $result = $db->query($sql);
    $flag = $result->fetchArray();

    if (!$flag) {
        $sql = "INSERT INTO log_settings (log_path, log_interval) VALUES ('/var/log/samba/', '60')";
        $db->query($sql);
    }

// エラーメッセージの初期化
    $errorMessage = "";

// ログインボタンが押された場合
    if (isset($_POST["login"])) {
        // １．ユーザIDの入力チェック
        if (empty($_POST["userid"])) {
            $errorMessage = "ユーザIDが未入力です。";
        } else if (empty($_POST["password"])) {
            $errorMessage = "パスワードが未入力です。";
        }

        // ユーザIDとパスワードが入力されていたら認証する
        if (!empty($_POST["userid"]) && !empty($_POST["password"])) {
            $userid = $_POST["userid"];
            $sql = "SELECT * FROM account_info WHERE userid = '$userid'";
            $result = $db->query($sql);
            if (!isset($result)) {
                $errorMessage = $userid . "は存在しないユーザです。";
                $db->close();
                die($errorMessage);
            }

            while ($row = $result->fetchArray()) {
                $db_hashed_pwd = $row['password'];
            }

            if ($db_hashed_pwd === $_POST["password"]) {
                session_regenerate_id(true);
                $_SESSION["USERID"] = $_POST["userid"];
                header("Location: log.php");
                exit;
            } else {
                // 認証失敗
                $errorMessage = "ユーザID、またはパスワードが違います。";
                $db->close();
                die($errorMessage);
            }
        } else {
            // 未入力なら何もしない
        }
    }

    $db->close();
    ?>

    <body>
        <div class="login-box">
            <img src="img/logazer.png" width="600" height="120" alt="" class="logo"/>

            <div class="form-box">
                <form id="loginForm" name="loginForm" action="" method="POST">
                    <p>ユーザーID</p>
                    <input type="text" name="userid" id="userid" >
                    <p>パスワード</p>
                    <input type="password" name="password" id="password" >
                    <input type="submit" id="login" name="login" value="ログイン">
                </form>
                <p class="small-font">&gt; <a href="forgot_password.php">パスワードを忘れた方</a></p>
                <p class="small-font">&gt; <a href="create_account.php">新規アカウント登録</a></p>
            </div>
        </div>
    </body>
</html>