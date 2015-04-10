<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>新規アカウント作成</title>
        <link href="css/style.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        include "registCheck.php";

        if ($_POST['mode'] == "check" && empty($regist_error)) {

            header("Location: ./registConfirm.php");
            exit();
        }
        ?>

        <div class="login-box">
            <img src="img/logazer.png" width="600" height="120" alt="" class="logo"/>

            <div class="form-box">
                <p class="small-font">新規アカウントを作成いたします。<br>
                    「ご希望のユーザーID」「ご希望のパスワード」「メールアドレス」をご入力の上『アカウントを作成』ボタンをクリックしてください。</p>

                <?php
                if (isset($regist_error)) {

                    print ("<p><font color=\"red\">" . $regist_error . "</font></p>");
                }
                ?>

                <form action="<?php print($_SERVER['PHP_SELF']) ?>" method="post">
                    <input type="hidden" name="mode" value="check">
                    <p>ユーザID</p>
                    <input type="text" name="userid" id="userid" >
                    <p>メールアドレス</p>
                    <input type="text" name="email" id="email" >
                    <p>ご希望のパスワード</p>
                    <input type="password" name="password" id="password" >
                    <p>ご希望のパスワード</p>
                    <input type="password" name="password2" id="password2" >
                    <input type="submit" value="アカウントを作成">
                </form>
            </div>
        </div>
    </body>
</html>