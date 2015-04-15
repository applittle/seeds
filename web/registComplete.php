<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_POST['mode'] == "registComplete") {

    if ($_POST['regist']) {

        // Using sqlite as DB source, create a new DB 'seeds' if not exists.
        $db = new SQLite3('./seeds.db');

        $userid = $_SESSION['userid'];
        $email = $_SESSION['email'];
        $password = $_SESSION['password'];

        $sql = "INSERT INTO account_info (userid, password, email) VALUES ('$userid', '$password', '$email')";
        $db->query($sql);
        $db->close();

        session_destroy();

        print("情報を登録しました。登録情報でログインできます。<br />");
        header("Location: ./index.php");
    } elseif ($_POST['return']) {
        header("Location: ./registForm.php");
    }
} else {
    print("不正なURLから呼び出された可能性があります。");
    session_destroy();
}
?>