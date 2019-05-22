<?php 

$sdebug=0;

$debug=@$_REQUEST["debug"];
if($_FILES['report']['tmp_name'] == "") {
	echo "you need upload file name report";
	exit(0);
}
$cmd = "gpg --batch --quiet --homedir /var/www/.gnupg --decrypt ".$_FILES['report']['tmp_name']." 2>/dev/null";
$result = null;
$output = [];
exec($cmd, $output, $result);
if ($debug==1) {
	echo "gpg result: ";
	echo $result;
	echo "<br>\n";
	echo $output[0];
	echo "<br>\n";
}
if ($result!=0) {
	echo "gpg error\n";
	if ($sdebug==1)move_uploaded_file($_FILES['report']['tmp_name'],"/tmp/1");
	exit(0);
}
if ($output[0]=="") {
	echo "null or error\n";
	if ($sdebug==1)move_uploaded_file($_FILES['report']['tmp_name'],"/tmp/2");
	exit(0);
}
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost:8086/write?db=ahstatus&u=admin&p=admin");
curl_setopt($ch, CURLOPT_POST, 1);
$r = implode("\n",$output);
if($debug ==1) 
	echo $r;
curl_setopt($ch, CURLOPT_POSTFIELDS, $r);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec ($ch);
curl_close ($ch);
if ($debug == 1) {
	echo "send to influx result:";
	echo var_dump($server_output);
}
echo "OK\n"
?>
