var log_array = [];
var dup_array = [];
var host_array = [];
var year_month_map = new Object();

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
    var year_selected = $("select[name='year_selector']").val();
    // Based on selected year, generating month selector
    $("#select_box").append("<select id='month_selector' name='month_selector'>");
    for (var i = 0; i < year_month_map[year_selected].length; i++)
    {
        $('#month_selector').append($('<option>').html(year_month_map[year_selected][i]));
    }
    $("#select_box").append('</select>');
    show_log();
}

function show_log()
{
    $('#log').html('&nbsp');
    var host_selected = $("select[name='host_selector']").val();
    var year_selected = $("select[name='year_selector']").val();
    var month_selected = $("select[name='month_selector']").val();

    for (var i = 0; i < log_array.length; i++) {
        var d = new Date(log_array[i].date);
        var year = (d.getYear() < 2000) ? d.getYear() + 1900 : d.getYear();

        if (log_array[i].host === host_selected
                || host_selected === '全て'
                || year === year_selected
                || d.getMonth() + 1 === month_selected)
        {
            $('#log').append(log_array[i].host + ':' + log_array[i].date + ':' + log_array[i].message);
            $('#log').append('<br>');
        }
    }
}

$(function () {
    var update = function () {
        var data = {'log': '/var/log/samba/'};

        $.ajax('./check_log.php', {
            type: "POST",
            cache: false,
            data: data,
            success: function (res) {
                var arr = JSON.parse(res);
                // Put parsed values into array
                for (var i = 0; i < arr.length; i++)
                {
                    // Parse date to javascript date
                    arr[i]['p_date'] = Date.parse(arr[i].date);
                    // id consist of host name___Date
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
                $('#select_box').html('&nbsp');
                $("#select_box").append("<select id='host_selector' name='host_selector'>");
                $('#host_selector').append($('<option>').html("全て"));

                // Generating host selector
                for (var i = 0; i < host_array.length; i++)
                {
                    $('#host_selector').append($('<option>').html(host_array[i]));
                }
                $('#select_box').append($('</select>'));
                $('#host_selector').change(function () {
                    show_log();
                });

                // Generating year/month selector
                $("#select_box").append("<select id='year_selector' name='year_selector'>");
                for (var year in year_month_map)
                {
                    $('#year_selector').append($('<option>').html(year));
                }
                $('#select_box').append($('</select>'));
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

    var counter = 60;
    setInterval(function () {
        counter = counter - 1;
        $('#counter').html('ログの更新まで ' + counter + '...');

        if (counter == 0) {
            counter = 60;
            update();
        }
    }, 1000);

    update();
});


