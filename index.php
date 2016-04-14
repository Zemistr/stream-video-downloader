<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="copyright" content="Zemistr">
	<meta name="description" content="Stahování videí ze serveru Stream.cz">
	<meta name="google-site-verification" content="V7HHmvwhPSNzq4W_gZxoswxkgSLmsP7wT1Ml-dJgnXw">
	<meta name="keywords" content="Stream,Stream.cz,Video,Download,Downloader,Stream Download,Stream Downloader,Stream.cz video downloader,Zemistr">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>Stream Video Downloader</title>
	<link rel="shortcut icon" href="favicon.ico">
	<link rel="stylesheet" href="downloader.css">
	<!--[if lte IE 8]>
	<script>
		var i = 0, e = ["footer", "header", "section"];
		while(e[i]) document.createElement(e[i++]);

		var lte_IE8 = true;
	</script>
	<![endif]-->
	<!--[if lte IE 9]>
	<script>
		var lte_IE9 = true;
	</script>
	<![endif]-->
	<script>
		var lte_IE8 = !!lte_IE8;
		var lte_IE9 = !!lte_IE9;
	</script>
	<script src="ajax.class.min.js?v2"></script>
	<script src="downloader.min.js?v2"></script>

  <!-- Piwik -->
  <script type="text/javascript">
    var _paq = _paq || [];
    _paq.push(["setDomains", ["*.stream.zemistr.eu"]]);
    _paq.push(['trackPageView']);
    _paq.push(['enableLinkTracking']);
    (function() {
      var u="//piwik.zdft.net/";
      _paq.push(['setTrackerUrl', u+'piwik.php']);
      _paq.push(['setSiteId', 6]);
      var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
      g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
    })();
  </script>
  <noscript><p><img src="//piwik.zdft.net/piwik.php?idsite=6" style="border:0;" alt="" /></p></noscript>
  <!-- End Piwik Code -->

</head>
<body>
<div id="downloader">
	<div id="loading" class="process"></div>
	<header id="main-header" class="main">
		<h1 id="main-title">Video Downloader</h1>
	</header>
	<section id="main-search" class="main">
		<input type="text" id="url-input" class="input" placeholder="Zadejte adresu videa">
		<a class="btn" id="url-submit" href="#search">Vyhledat</a>

		<div id="url-example">http://www.stream.cz/nejnovejsi/peklonataliri/10000721-ryze</div>
	</section>
	<section id="main-results_mask" class="">
		<div id="main-results" class="main">
			<input type="text" class="input" value="" id="result-name">
			<span id="result-btns"></span>
		</div>
	</section>
	<footer id="main-footer" class="main">
		Idea &amp; parser by <a href="http://zemistr.eu" target="_blank" class="footer-link">Zemistr</a>,
		HTML/CSS &amp; effects by <a href="http://marekzeman.cz" target="_blank" class="footer-link">Marek Zeman</a>
		<i class="version">Version 3.2.2 / <?= date('Y') ?></i>
	</footer>
</div>
</body>
</html>
