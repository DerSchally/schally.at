<html>
<head>
	<meta HTTP-EQUIV="content-type" CONTENT="text/html;charset=windows-1250">
</head>
<body>
<form method="post">
<textarea name="inp" rows=10 cols=50></textarea>
<input type="submit">
</form>
<textarea rows=10 cols=50>
<?
if($inp){
//	echo iconv('windows-1250', 'UTF-8', $inp);
//	echo $inp;
//	echo htmlentities($inp);
//echo html_entity_decode( "&#xC7;&#xE0;&#xFF;&#xE2;&#xEA;&#xE0;", ENT_QUOTES, 'cp1251');
echo htmlentities( $inp, ENT_QUOTES, 'cp1252'); 
 

//	echo html_entity_encode(html_entity_decode($inp));
}
?>
</textarea>
<br /><br />
<?
	echo str_replace("&amp;#","&#",htmlentities($inp));
?>
</body>
