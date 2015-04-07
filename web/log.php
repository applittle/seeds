<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>ログ監視</title>
        <link href="css/style.css" rel="stylesheet" type="text/css">
        <script src="//code.jquery.com/jquery-2.1.0.min.js"></script>
        <script src="js/crawl.js"></script>
    </head>

    <body>
        <div class="admin-box">

            <div class="header">
                <img src="img/logo.png" width="200" height="40" alt="" class="logo"/>　　　<a href="index.php">ログアウト</a> </div>

            <div class="left-box">
                <ul>
                    <li><a href="log.php">ログ監視</a></li>
                    <li><a href="access.php">アクセス制御</a></li>
                    <li><a href="account.php">アカウント管理</a></li>
                </ul>
            </div>

            <div class="right-box">
                <div class="log-box" id="log-box">
                    <form>
                        <input type="submit"  value="CSVダウンロード">
                    </form>
                    <div id="select-box"></div>
                    <div id="report-box" class="report-box">
                    </div>
                </div>


            </div>


        </div>
        <div class="footer">
            <div class="copyright">&copy;Seeds-Create</div>
        </div>



        <script type="text/javascript">
            // <![CDATA[

            tab.setup = {
                tabs: document.getElementById('tab').getElementsByTagName('li'),
                pages: [
                    document.getElementById('log-box'),
                    document.getElementById('access-box')
                ]
            } //オブジェクトをセット
            tab.init(); //起動！

            // ]]>
        </script>
    </body>
</html>