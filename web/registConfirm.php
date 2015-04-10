<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
/**
if (!isset($_SESSION["USERID"])) {
    header("Location: ./logout.php");
}
**/
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja"> 


    <head> 
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
        <title>登録情報の確認</title>
    </head>


    <body>


        <h1>マイページ</h1>

        <h2>プロフィールの入力確認</h2>



        <form action="./registComplete.php" method="post">

            <h3>入力内容はこれでよろしいですか？OKの場合は、「登録する」ボタンを押してください。</h3>

            <table>
                <tr>
                    <th>ユーザID</th>
                    <td><?php print(htmlspecialchars($_SESSION["userid"])); ?></td>
                </tr>

                <tr>
                    <th>Email</th>
                    <td><?php print(htmlspecialchars($_SESSION["email"])); ?></td>
                </tr>

                <tr>
                    <th>パスワード：</th>
                    <td>セキュリティ対策のため表示しません</td>
                </tr>

            </table>

            <input type="submit" name="return" value="元に戻る">
                <input type="submit" name="regist" value="登録する">
                    <input type="hidden" name="mode" value="registComplete">
                        </form>


                        </body>
                        </html>

                        </body>
                        </html>