<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* 確認画面からの遷移かどうかをチェック 
  POSTで渡ったmodeの値が正しいかどうか、を確認 */

if ($_POST['mode'] == "registComplete") {

    /*
      入力値により処理を切り分け
      nameがreturnの場合、登録せずに入力画面へ強制遷移
      nameがregistの場合、登録処理を実行 */

    if ($_POST['regist']) {

        // Using sqlite as DB source, create a new DB 'seeds' if not exists.
        $db = new SQLite3('./seeds.db');

        $userid = $_SESSION['userid'];
        $email = $_SESSION['email'];
        $password = md5($_SESSION['password']);

        $sql = "INSERT INTO account_info (userid, password, email) VALUES ('$userid', '$password', '$email')";
        $db->query($sql);
        $db->close();

        /* セッション情報を削除 */
        session_destroy();

        print("情報を登録しました。登録情報でログインできます。<br />");
        header("Location: ./index.php");
    } elseif ($_POST['return']) {

        /* submitボタンが「もとへ戻る」だった場合、登録フォームへ移動 */

        header("Location: ./registForm.php");
    }
} else {

    /* hidden値が不正、もしくはsubmitボタンで渡されたname値が
      不正な場合、エラーメッセージを表示して、セッション情報削除 */

    print("不正なURLから呼び出された可能性があります。");
    session_destroy();
}
?>