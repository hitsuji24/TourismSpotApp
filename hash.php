<?php
// ハッシュ化   
$pw = password_hash("test1", PASSWORD_DEFAULT);
echo $pw;
// var_dump(password_verify("test1", $pw));
?>