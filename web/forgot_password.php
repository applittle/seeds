<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>パスワードを忘れた方</title>
        <link href="css/style.css" rel="stylesheet" type="text/css">
    </head>

    <body>

        <?php
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (isset($_POST["send_password"])) {
            $to = $_POST['email'];

            mb_language("Japanese");
            mb_internal_encoding("UTF-8");

            require_once("./phpmailer/class.phpmailer.php");
            $db = new SQLite3('./seeds.db');
            $sql = "SELECT password from account_info where email = '$to'";
            $result = $db->query($sql);
            if (!isset($result)) {
                $errorMessage = $to . "は存在しないEmailです。";
                $db->close();
                die($errorMessage);
            }

            while ($row = $result->fetchArray()) {
                $pwd = $row['password'];
            }

            $db->close();

            $from = "support@logazer.com";
            $fromname = "シーズクリエート　お問い合わせ";
            $subject = "パスワード通知";
            $body = "お客様のパスワードは'$pwd'です。";

            $mail = new PHPMailer();
            $mail->CharSet = "iso-2022-jp";
            $mail->Encoding = "7bit";
            $mail->SMTPSecure = 'tls';
            $mail->SMTPDebug = 1;
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = 'seeds-create.sakura.ne.jp';
            $mail->Port = 587;
            $mail->Username = 'support@logazer.com';
            $mail->Password = 'R7PA#tW.&2';

            $mail->AddAddress($to);
            $mail->From = $from;
            $mail->FromName = mb_encode_mimeheader($fromname, 'ISO-2022-JP');
            $mail->Subject = mb_encode_mimeheader($subject, 'ISO-2022-JP');

            $mail->Body = mb_convert_encoding($body, "JIS", "UTF-8");
        }
        ?>
        <div class="login-box">
            <img src="img/logazer.png" width="600" height="120" alt="" class="logo"/>

            <div class="form-box">
                <p class="small-font">「ユーザーID」または「メールアドレス」を入力してください。<br>
                    パスワードをご登録頂いているメールアドレスに送らせて頂きます。</p>
                <form action="" id="passwordForm" name="passwordForm" action="" method="POST">
                    <p>ユーザーID または メールアドレス</p>
                    <input type="text" name="email" id="email" >
                    <input type="hidden" id="send_password" name="send_password" value="send_password">
                    <input type="submit" value="パスワードを送信">
                </form>
                <?php
                if (!$mail->Send()) {
                    echo("<strong>'$to'へのメール送信に失敗しました！. Error:" . $mail->ErrorInfo . '</strong>');
                } else {
                    echo("<strong>'$to'へメールを送信しました。</strong>");
                }
                ?>
            </div>
        </div>
    </body>
</html>