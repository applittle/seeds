<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$userid = $_POST["userid"];
$email = $_POST["email"];
$password = $_POST["password"];
$password2 = $_POST["password2"];

// Using sqlite as DB source, create a new DB 'seeds' if not exists.
$db = new SQLite3('./seeds.db');
$sql = "SELECT userid FROM account_info WHERE userid = '$userid'";
$result = $db->query($sql);

$user_exist = false;

while ($result->fetchArray()) {
    $user_exist = true;
    break;
}

unset($regist_error);

if ($_POST["mode"] == "check") {
    if (empty($userid)) {
        $regist_error = "ユーザIDは必須です。<br />";
    } elseif (!empty($user_exist)) {
        $regist_error .= "入力した " . $userid . "はすでに使用されています。<br />";
    } elseif (!preg_match("/^[a-zA-Z0-9]+$/", $userid)) {
        $regist_error .= "ユーザIDには半角英数字のみ使用してください。<br />";
    } elseif (mb_strlen($userid) < 4) {
        $regist_error .= "ユーザIDは4文字以上で設定してください<br />";
    } elseif (mb_strlen($userid) > 20) {
        $regist_error .= "ユーザIDが長すぎます。20文字以下で設定してください<br />";
    }
    if (empty($email)) {
        $regist_error .= "emailは必須です。<br />";
    } elseif (!preg_match("/[0-9a-z!#\$%\&'\*\+\/\=\?\^\|\-\{\}\.]+@[0-9a-z!#\$%\&'\*\+\/\=\?\^\|\-\{\}\.]+/", $email)) {
        $regist_error .= "正しいemailアドレスを入力してください。<br />";
    }
    if (empty($password) || empty($password2)) {
        $regist_error .= "パスワードが正しく入力されていません。<br />";
    } elseif (!preg_match("/[\@-\~]/", $password)) {
        $regist_error .= "パスワードは半角英数字及び記号のみ入力してください。<br />";
    } elseif (mb_strlen($password) < 6) {
        $regist_error .= "パスワードは6文字以上で設定してください<br />";
    } elseif (mb_strlen($password) > 32) {
        $regist_error .= "パスワードが長すぎます。32文字以下で設定してください。<br />";
    } elseif ($password !== $password2) {
        $regist_error .= "入力されたパスワードが一致しません。<br />";
    }

    $_SESSION['userid'] = $userid;
    $_SESSION['email'] = $email;
    $_SESSION['password'] = $password;
}
?>