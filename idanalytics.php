<?php

defined( '_JEXEC' ) or die;

class plgSystemIdanalytics extends JPlugin
{
	public function onAfterRender()
	{
		$application = JFactory::getApplication();

		if ($application->isClient('administrator'))
		{
			return;
		}

		$analyticsCode = $this->getAnalyticsCode();

		$body = $application->getBody();
		$body = preg_replace(
			['/<head(| [^>]*)>/', '/<body(| [^>]*)>/'],
			['$0' . $analyticsCode['head'], '$0' . $analyticsCode['body']],
			$body
		);
		$application->setBody($body);
	}

	protected function getAnalyticsCode()
	{
		if ($this->params->get("ecommerceNeeded") == "true")
		{
			$ecommerce = $this->params->get("ecommerceVariable", "dataLayer");
			$ecommerce = ',ecommerce: "' . $ecommerce . '"';
		}

		$code = <<<HTML
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(%d, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:%s,
        trackHash:%s
        %s
   });
   
</script>
<!-- /Yandex.Metrika counter -->
HTML;
		$codeNoScript = <<<HTML
<!-- Yandex.Metrika counter Noscript-->
<noscript><div><img src="https://mc.yandex.ru/watch/%d" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter Noscript -->
HTML;

		$yandexMetrikaCounterId = $this->params->get('YandexMetrikaCounterId', 0);
		$webvisorNeeded = $this->params->get('webvisorNeeded', 'false');
		$trachHashNeeded = $this->params->get('trackHashNeeded', 'false');

		return [
			'head' => sprintf($code, $yandexMetrikaCounterId, $webvisorNeeded, $trachHashNeeded,isset($ecommerce) ? $ecommerce : null),
			'body' => sprintf($codeNoScript, $yandexMetrikaCounterId)
		];
	}
}
