<?php
error_reporting(0);
echo '<a href="index.html"><-返回</a><br>';
echo "糖豆视频下载器 url_dl（适用于全部视频，包括部分无法使用vid_dl的v95） Version：20180807 by：Sylpha" . "<br>";
echo "==========" . "<br>";
$videoPageUrl = $_GET['vurl'];

//视频播放页面url鉴别器===start===
if($videoPageUrl == "")
	{die("视频播放页URL不能为空！");}

if(strpos($videoPageUrl,"m.tangdou.com") !== false)
	{$urlType = "m";}
elseif(strpos($videoPageUrl,"share.tangdou.com") !== false)
	{
		$urlType = "share";}
elseif(strpos($videoPageUrl,"www.tangdou.com") !== false)
	{$urlType = "www";}
else{die("urlType无匹配！");}
//视频播放页面url鉴别器===end===

echo "播放页面url类型（urlType）：" . $urlType . "<br>";

//建立curl连接设置===start===
$curl_link = curl_init();
$curl_opt = array(CURLOPT_URL => $videoPageUrl,CURLOPT_RETURNTRANSFER => 1,CURLOPT_HEADER => 0,CURLOPT_SSL_VERIFYPEER => false,CURLOPT_SSL_VERIFYHOST => false);
curl_setopt_array($curl_link,$curl_opt);
$curl_result = curl_exec($curl_link);
curl_close($curl_link);
//建立curl连接设置===end===

if($curl_result === false)
{die("从糖豆网页面获取源码失败！");}

if($urlType == "share")
{
	$start = strpos($curl_result,"var vid = '");
	if($start === false)
		{die("不是一个有效的视频地址！（请不要忘记http或https！）");}
	$end = strpos($curl_result,"var videourl");
	$vid = substr($curl_result,$start + 11,$end - $start - 11);
	$vid = substr($vid,0,strpos($vid,"';")); //二次去掉结尾的 [';]
	echo "糖豆视频vid：" . $vid;
	echo "将跳转到vid_dl页面进行处理...请稍等...";
	header("Location:vid_dl.php?vid=" . $vid);
	exit;
}

if($urlType == "m")
{
	$start = strpos($curl_result,"var vid = '");
	if($start === false)
		{die("不是一个有效的视频地址！（请不要忘记http或https！）");}
	$end = strpos($curl_result,"var videourl");
	$vid = substr($curl_result,$start + 11,$end - $start - 11);
	$vid = substr($vid,0,strpos($vid,"';")); //二次去掉结尾的 [';]
	echo "糖豆视频vid：" . $vid . "<br>";
	$start = strpos($curl_result,"var videourl = '");
	$end = strpos($curl_result,"</script>");
	$videourl = substr($curl_result,$start + 16,32);
	$titleStart = strpos($curl_result, "<title>");
	$titleEnd = strpos($curl_result,"</title>");
	$videoName = substr($curl_result, $titleStart + 7,$titleEnd - $titleStart - 7);
	echo "视频代码id（videourl）：" . $videourl . "<br>";
	echo "视频名称：" . $videoName . "<br>";
	echo "==========" . "<br>";

	//建立curl连接设置===start===
	$mp4PageUrl = "https://share.tangdou.com/mp4.php?vid=" . $vid . "&videourl=" . $videourl;
	$curl_link = curl_init();
	$curl_opt = array(CURLOPT_URL => $mp4PageUrl,CURLOPT_RETURNTRANSFER => 1,CURLOPT_HEADER => 0,CURLOPT_SSL_VERIFYPEER => false,CURLOPT_SSL_VERIFYHOST => false);
	curl_setopt_array($curl_link, $curl_opt);
	$curl_result = curl_exec($curl_link);
	curl_close($curl_link);
	//建立curl连接设置===end===
	$start = strpos($curl_result,'src=\"');
	$end = strpos($curl_result, '\" wmode=');
	$dlLink = substr($curl_result,$start + 6,$end - $start - 6);
	$start = strpos($dlLink, "k=");
	$aucshareSign = substr($dlLink, $start,31);
	echo "方式：aucshare<br>";
	echo "aucshareSign：" . $aucshareSign . "<br>";
	echo "aucshare下载地址：" . $dlLink . "<br>";
	echo "==========" . "<br>";
    echo '注意：请直接复制链接到浏览器或迅雷中下载，地址会查HTTP Referer来源。';
    exit;
}

if($urlType == "www")
{
	$start = strpos($curl_result,'checkUrlAndGo_Td');
	if($start === true)
		{die("不是一个有效的视频地址！（请不要忘记http或https！）");}
	$start = strpos($videoPageUrl, "com/") + 4;
	$mPageUrl = "http://m.tangdou.com/" . substr($videoPageUrl, $start);
	echo "移动端页面url：" . $mPageUrl . "<br>";
	echo "将跳转到移动端页面进行处理...请稍等...";
	header("Location:url_dl.php?vurl=" . $mPageUrl);
	exit;
}

?>

