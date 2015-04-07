<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>ログイン</title>
<link href="css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<div class="admin-box">

<div class="header">
<img src="img/logo.png" width="200" height="40" alt="" class="logo"/>　　　<a href="index.php">ログアウト</a> </div>

<div class="left-box">
  <ul>
    <li><a href="log.php">ログ監視</a></li>
    <li><a href="access.php">アクセス制御</a></li>
  </ul>
</div>

<div class="right-box">
<div class="access-box" id="access-box">

<table width="100%">
  <tbody>
    <tr>
      <th>設定場所</th>
      <td>/home/aaa/bbb</td>
      <td><form><input type="submit"  value="参照"></form></td>
    </tr>
  </tbody>
</table>


<table width="100%">
  <tbody>
    <tr>
      <th>PC名</th>
      <th>状態</th>
      <th>アクセス許可</th>
    </tr>
    <tr>
      <td>yoshio-pc</td>
      <td>可</td>
      <td><form><input type="submit"  value="不可にする"></form></td>
    </tr>
    <tr>
      <td>yoshio-pc2</td>
      <td>不可</td>
      <td><form><input type="submit"  value="可にする"></form></td>
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