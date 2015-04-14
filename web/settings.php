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
                                <th>ユーザー名</th>
                                <th>メールアドレス</th>
                                <th>削除</th>
                            </tr>
                            <tr>
                                <td>yoshio</td>
                                <td>aaa@a.com</td>
                                <td><form><input type="submit"  value="削除"></form></td>
                            </tr>
                            <tr>
                                <td>yoshio2</td>
                                <td>bbb@b.com</td>
                                <td><form><input type="submit"  value="削除"></form></td>
                            </tr>
                        </tbody>
                    </table>

                </div>

            </div>


        </div>
        <div class="footer">
            <div class="copyright">&copy;Seeds-Create</div>
        </div>

    </body>
</html>