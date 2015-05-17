<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>ログ監視</title>
        <link href="css/style.css" rel="stylesheet" type="text/css">
        <script src="//code.jquery.com/jquery-2.1.0.min.js"></script>
    </head>
    <?php
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $userid = $_SESSION['USERID'];

    // Using sqlite as DB source, create a new DB 'seeds' if not exists.
    $db = new SQLite3('./seeds.db');

    // Fetch log settings info from DB.
    $sql = "SELECT * FROM settings";
    $result = $db->query($sql);
    if (!isset($result)) {
        $db->close();
        die("設定より監視ログを指定して下さい。");
    }

    while ($row = $result->fetchArray()) {
        $log_interval = $row['log_interval'];
        $log_path = $row['log_path'];
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
                    <li><a href="host_access.php">アクセス制御</a></li>
                    <li><a href="settings.php">設定管理</a></li>
                </ul>
            </div>

            <div class="right-box">
                <div class="log-box" id="log-box">
                    <div id="select-box"></div>
                    <div id="report-box" class="report-box">
                    </div>
                    <div id="counter"></div>
                </div>


            </div>


        </div>
        <div class="footer">
            <div class="copyright">&copy;Seeds-Create</div>
        </div>



        <script type="text/javascript">
            // <![CDATA[

            var log_array = [];
            var dup_array = [];
            var host_array = [];
            var year_month_map = new Object();
            var host_selected = null;
            var year_selected = null;
            var month_selected = null;

            function funcCompare(a, b) {
                if (a.date < b.date)
                    return -1;
                if (a.date > b.date)
                    return 1;
                if (a.host < b.host)
                    return -1;
                if (a.host > b.host)
                    return 1;
                return 0;
            }

            // Rebuild month selector
            function year_changed()
            {
                year_selected = $("select[name='year_selector']").val();
                // Based on selected year, generating month selector
                $("#select-box").append("<select id='month_selector' name='month_selector'>");
                for (var i = 0; i < year_month_map[year_selected].length; i++)
                {
                    if (month_selected == year_month_map[year_selected][i])
                    {
                        $('#month_selector').append($('<option selected>').html(year_month_map[year_selected][i]));
                    }
                    else
                    {
                        $('#month_selector').append($('<option>').html(year_month_map[year_selected][i]));
                    }
                }
                $("#select-box").append('</select>');
                show_log();
            }

            function show_log()
            {
                $('#report-box').html('&nbsp');
                host_selected = $("select[name='host_selector']").val();
                year_selected = $("select[name='year_selector']").val();
                month_selected = $("select[name='month_selector']").val();

                // Generating show log table.
                $('#report-box').append('<table id="log_table" width="100%">');
                $('#log_table').append('<tbody id="log_tableb">');
                $('#log_tableb').append('<tr>');
                $('#log_tableb').append('<th>PC名</th>');
                $('#log_tableb').append('<th>日時</th>');
                $('#log_tableb').append('<th>場所</th>');
                $('#log_tableb').append('<th>行動</th>');
                $('#log_tableb').append('</tr>');

                for (var i = 0; i < log_array.length; i++) {
                    var host = log_array[i].host;
                    var d = new Date(log_array[i].date);
                    var year = (d.getYear() < 2000) ? d.getYear() + 1900 : d.getYear();
                    var month = d.getMonth() + 1;

                    if ((host == host_selected || host_selected == '全て')
                            && year == year_selected
                            && month == month_selected)
                    {
                        $('#log_tableb').append('<tr>');
                        $('#log_tableb').append('<td>' + log_array[i].host + '</td>');
                        $('#log_tableb').append('<td>' + log_array[i].date + '</td>');
                        $('#log_tableb').append('<td>' + log_array[i].location + '</td>');
                        $('#log_tableb').append('<td>' + log_array[i].message + '</td>');
                        $('#log_tableb').append('</tr>');
                    }
                }

                $('#log_table').append('</tbody>');
                $('#report-box').append('</table>');
            }

            $(function () {
                var update = function () {
                    //var data = {'log': <?php echo $log_path; ?>};
                    host_selected = $("select[name='host_selector']").val();
                    year_selected = $("select[name='year_selector']").val();
                    month_selected = $("select[name='month_selector']").val();

                    $.ajax('./check_log.php', {
                        type: "POST",
                        async: true,
                        cache: false,
                        //data: data,
                        success: function (res) {
                            var arr = JSON.parse(res);
                            console.log('Returned array length: ' + arr.length);

                            // Put parsed values into array
                            for (var i = 0; i < arr.length; i++)
                            {
                                // Parse date to javascript date
                                arr[i]['p_date'] = Date.parse(arr[i].date);
                                // id consists of host name___Date
                                var id = arr[i].host + '___' + arr[i].date;

                                if (dup_array.indexOf(id) < 0)
                                {
                                    dup_array.push(id);
                                    log_array.push(arr[i]);

                                    // For host selector
                                    if (host_array.indexOf(arr[i].host) < 0)
                                    {
                                        console.log('host name put in host array: ' + arr[i].host);
                                        host_array.push(arr[i].host);
                                    }

                                    var d = new Date(arr[i].date);
                                    console.log('Original date: ' + d);
                                    var year = (d.getYear() < 2000) ? d.getYear() + 1900 : d.getYear();
                                    // For year month selector
                                    if (year in year_month_map)
                                    {

                                        var month_array = year_month_map[year];
                                        if (month_array.indexOf(d.getMonth() + 1) < 0)
                                        {
                                            console.log('month put: ' + d.getMonth() + 1);
                                            month_array.push(d.getMonth() + 1);
                                            year_month_map[year] = month_array;
                                        }
                                    }
                                    else
                                    {
                                        console.log('year put: ' + year);
                                        var month_array = [];
                                        console.log('month put: ' + d.getMonth() + 1);
                                        month_array.push(d.getMonth() + 1);
                                        year_month_map[year] = month_array;
                                    }
                                }
                            }

                            // Sort array by date
                            log_array.sort(funcCompare);

                            // Generating select box //
                            $('#select-box').html('&nbsp');
                            $("#select-box").append("<select id='host_selector' name='host_selector'>");
                            $('#host_selector').append($('<option>').html("全て"));

                            // Generating host selector
                            for (var i = 0; i < host_array.length; i++)
                            {
                                if (host_array[i] == host_selected)
                                {
                                    $('#host_selector').append($('<option selected>').html(host_array[i]));
                                }
                                else
                                {
                                    $('#host_selector').append($('<option>').html(host_array[i]));
                                }
                            }
                            $('#select-box').append($('</select>'));
                            $('#host_selector').change(function () {
                                show_log();
                            });

                            // Generating year/month selector
                            $("#select-box").append("<select id='year_selector' name='year_selector'>");
                            for (var year in year_month_map)
                            {
                                if (year_selected == year)
                                {
                                    $('#year_selector').append($('<option selected>').html(year));
                                }
                                else
                                {
                                    $('#year_selector').append($('<option>').html(year));
                                }
                            }
                            $('#select-box').append($('</select>'));
                            $('#year_selector').change(function () {
                                year_changed();
                            });
                            year_changed();

                            $('#month_selector').change(function () {
                                show_log();
                            });

                            if ($('input').prop('checked')) {
                                window.scrollTo(0, $('html').height());
                            }
                        },
                        error: function () {
                            console.log('error');
                        }
                    });
                };

                var counter = Number(<?php echo $log_interval; ?>);
                setInterval(function () {
                    if (log_array.length > 0)
                    {
                        counter = counter - 1;
                        $('#counter').html('ログの更新まで ' + counter + '...');

                        if (counter === 0) {
                            counter = Number(<?php echo $log_interval; ?>);
                            update();
                        }
                    }
                    else if (log_array.length === 0)
                    {
                        $('#counter').html('<strong>表示できるログ情報がありません。設定画面より、ログファイルへのパスを確認してください。</strong>');
                    }
                }, 1000);

                update();
            });

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