<?php
error_reporting(0);
echo '<a href="index.html"><-返回</a><br>';
echo "糖豆视频下载器 vid_dl（适用于所有<=v94，部分v95） Version：20180807 by：Sylpha" . "<br>";
echo "==========" . "<br>";

$vid = $_GET['vid'];
	if($vid == "" || is_numeric($vid) == false)
		{die("输入vid为空或不是数字或不合法！");}


echo "您选择的糖豆视频vid：" . $vid . "<br>";

//============curl 相关设置 start==============
$sharePageLink = "https://share.tangdou.com/play.php?vid=" . $vid;
$getPageSource = curl_init();
$getPageSourceOption = array(CURLOPT_URL => $sharePageLink,CURLOPT_RETURNTRANSFER => 1,CURLOPT_HEADER => 0,CURLOPT_SSL_VERIFYPEER => false,CURLOPT_SSL_VERIFYHOST => false);
curl_setopt_array($getPageSource, $getPageSourceOption);
$getPageSourceResult = curl_exec($getPageSource);
curl_close($getPageSource);
//============curl 相关设置 end================

if($getPageSourceResult == FALSE)
{
	die("从糖豆sharePage获取源码失败！");
}

$start = strpos($getPageSourceResult,"var videourl = '");
if($start === false)
	{die("查找videourl失败！");}
$titleStart = strpos($getPageSourceResult,"<title>");
$titleEnd = strpos($getPageSourceResult,"</title>");

$videourl = substr($getPageSourceResult,$start + 16,32);
$videoName = substr($getPageSourceResult, $titleStart + 7,$titleEnd - $titleStart - 7);

$videourlMatch = strpos($videourl, "'");

if($videourlMatch !== false)
	{die("获取到的视频代码id格式不符！<br>注意：出现这个说明可能是因为这个视频不能使用vid方式下载，可以试试url方式下载。");}

echo "视频代码id（videourl）：" . $videourl . "<br>";
echo "视频名称：" . $videoName . "<br>";
echo "==========" . "<br>";

$accweb = strpos($getPageSourceResult,"accweb");
$aucshare = strpos($getPageSourceResult,"aucshare");

if($accweb == false && $aucshare == false)
	{die("视频信息获取失败，请确认该视频是否被删除！");}
if($accweb >= 0 && $aucshare == false)
{	
	echo "方式：accweb<br>";
	$accwebLink = "https://accweb.tangdou.com/" . $videourl . "-10.mp4";
	$accwebStart = strpos($getPageSourceResult,$accwebLink);
	$accwebSign = substr($getPageSourceResult,$accwebStart + strlen($accwebLink) + 1,55);
	echo "accwebSign：" . $accwebSign . "<br>";
	$accwebDlLink = $accwebLink . "?" . $accwebSign;
	echo 'accweb下载地址：' .$accwebDlLink . "<br>";
	
}

if($aucshare >= 0 && $accweb == false)
	{echo "方式：aucshare<br>";
	$aucshareLink = "https://aucshare.tangdou.com/" . $videourl . "-10.mp4";
	$aucshareStart = strpos($getPageSourceResult,$aucshareLink);
	$aucshareSign = substr($getPageSourceResult,$aucshareStart + strlen($aucshareLink) + 1,31);
	echo "aucshareSign：" . $aucshareSign . "<br>";
	$aucshareDlLink = $aucshareLink . "?" . $aucshareSign;
    echo 'aucshare下载地址：' . $aucshareDlLink . "<br>";

}
	echo "==========" . "<br>";
    echo '注意：请直接复制链接到浏览器或迅雷中下载，地址会查HTTP Referer来源。';
    exit;


?>
