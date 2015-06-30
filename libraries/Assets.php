<?php 

/**
*Volta framework
*
*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Assets
{
	public function replaceAssets($html)
	{
		preg_match_all('/{@ assets type="(css|js)" path="(.*)" @}/', $html, $assets_external);
		$html = preg_replace('/{@ css_inline @}(.*?){@ end @}/s', '<style type="text/css">\\1</style>', $html);
		$html = preg_replace('/{@ js_inline @}(.*?){@ end @}/s', '<script type="text/javascript">\\1</script>', $html);
		preg_match_all('/<script type="text\/javascript">(.*?)<\/script>/s', $html, $assets_inline_js);
		preg_match_all('/<style type="text\/css">(.*?)<\/style>/s', $html, $assets_inline_css);
		$html = preg_replace('/<style type="text\/css">(.*?)<\/style>/s', '', $html);
		$html = preg_replace('/<script type="text\/javascript">(.*?)<\/script>/s', '', $html);
		
		$css_inline = array();
		$js_inline = array();
		$css = array();
		$js = array();
		$paths = array();
		$inlines = array(
			'css' => $assets_inline_css[1],
			'js' => $assets_inline_js[1]
		);
		
		if (sizeof($assets_external[1]) > 0) {
			foreach ($assets_external[1] as $key => $type) {
				if ($this->exist($paths, $assets_external[2][$key])) {
					continue;
				}
				if ($type == 'css') {
					$paths[] = $assets_external[2][$key];
					$css[] = "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"". $assets_external[2][$key] . "\" />\n";
				} elseif ($type == 'js') {
					$paths[] = $assets_external[2][$key];
					$js[] =  "\n<script type=\"text/javascript\" src=\"" . $assets_external[2][$key] . "\"></script>\n";
				}
			}
		}
		foreach ($inlines as $type => $inline) {
			if ($type == 'css') {
				foreach ($inline as $code) {
					$css_inline[] = "\n<style type=\"text/css\">" . $code . "</style>\n";
				}
			} elseif ($type == 'js') {
				foreach ($inline as $code) {
					$js_inline[] = "\n<script type=\"text/javascript\">" . $code . "</script>\n";
				}
			}
		}
		
		$cssString = implode("", array_merge($css, $css_inline));
		$jsString = implode("", array_merge($js, $js_inline));
		$cssString = (empty($cssString)) ? '' : $cssString;
		$jsString = (empty($jsString)) ? '' : $jsString;
		$html = str_replace(array('{@ css @}', '{@ javascripts @}'), array($cssString, $jsString), $html);
		$html = preg_replace('/{@ assets type="(img|css|js)" path="(.*)" @}/', '', $html);
	}
	
	
	private function exist($assets, $current)
	{
		return (in_array($current, $assets)) ? true : false;
	}
}

?>