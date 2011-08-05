<html>
<head>
	<title>Quantitative Trend Tracker</title>
</head>
<body>
	<h2>Quantitative Trend Tracker</h2>
	<p>QTT tracks how frequently a term appears on 新浪微博, and provides a data feed which can be used in later researches.<p>
	<p><?php echo "<a href='daily.php?date=".date('Y-m-d',time()-24*60*60)."'>"; ?>點此查看數據樣例.</a></p>
	<hr />
	<form action='term.php?' method='get'>
	词频统计：
	<input type='text' name='t' />
	<input type='submit' name='submit' />
	</form>
	<p><i>This Site is under heavy construction.<br />
			Please help by connecting your weibo account. <a href="connect.php">
	<img alt="24x24.png" src="http://open.sinaimg.cn/wikipic/icon/24x24.png" style="width: 24px; height: 24px;">Click HERE.</a></i></p>
</body>
</html>

