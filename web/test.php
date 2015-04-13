<?php
/**
`sudo iptables -F`;

`sudo iptables -A INPUT -m udp -p udp -s localhost --dport 137:138 -j DROP`;
`sudo iptables -A INPUT -m tcp -p tcp -s localhost --dport 139 -j DROP`;

`sudo iptables -D INPUT -m udp -p udp -s localhost --dport 137:138 -j DROP`;
`sudo iptables -D INPUT -m tcp -p tcp -s localhost --dport 139 -j DROP`;
`sudo iptables save`;
`sudo iptables restart`;
**/
$result = `sudo iptables -L`;
print "<pre>$result</pre>";

?>

