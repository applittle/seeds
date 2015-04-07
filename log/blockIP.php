<?php

$ip = $_POST['ip'];

$cmd = "echo $ip >> ./black_list.txt";
exec($cmd);
$cmd = "sudo iptables -A INPUT -t filter -s $ip -j DROP ";
exec($cmd);
echo("<center><p>I refused your $ip.<br> This $ip address is refused<br>");
echo("don't ever come again</p></center>");

?>

