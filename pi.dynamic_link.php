<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dynamic_link Class
 *
 * @package			ExpressionEngine
 * @category		Plugin
 * @author			Patrick
 * @copyright		None
 * @link
 */

class Dynamic_link {

	var $return_data;

	function __construct()
	{
        $tagdata = ee()->TMPL->tagdata;
        $url = ee()->TMPL->fetch_param('url');
		$encoded = ee()->TMPL->fetch_param('encoded');
		if (strcasecmp($encoded, 'base64') === 0) {
			$url = base64_decode($url);
		}
		$rmetas = ee()->cache->get('/dynamic_link/' . md5($url));
		if (empty($rmetas))
		{
			$html = @file_get_contents($url);
			if (empty($html)) {
				$rmetas = ["crawled" => false];
			} else {
				$rmetas = ["crawled" => true];
				libxml_use_internal_errors(true);
				$doc = new DomDocument();
				$doc->loadHTML($html);
				$xpath = new DOMXPath($doc);

				// first try og:
				$rmetas['link_url'] = @$xpath->query('/*/head/meta[@property="og:url"]/@content')->item(0)->nodeValue;
				$rmetas['link_title'] = @$xpath->query('/*/head/meta[@property="og:title"]/@content')->item(0)->nodeValue;;
				$rmetas['link_description'] = @$xpath->query('/*/head/meta[@property="og:description"]/@content')->item(0)->nodeValue;;
				$rmetas['link_image'] = @$xpath->query('/*/head/meta[@property="og:image"]/@content')->item(0)->nodeValue;;

				// than try twitter:
				if (empty($rmetas['link_url'])) {
					$rmetas['link_url'] = @$xpath->query('/*/head/meta[@name="twitter:url"]/@content')->item(0)->nodeValue;
				}
				if (empty($rmetas['link_title'])) {
					$rmetas['link_title'] = @$xpath->query('/*/head/meta[@name="twitter:title"]/@content')->item(0)->nodeValue;
				}
				if (empty($rmetas['link_description'])) {
					$rmetas['link_description'] = @$xpath->query('/*/head/meta[@name="twitter:description"]/@content')->item(0)->nodeValue;
				}
				if (empty($rmetas['link_image'])) {
					$rmetas['link_image'] = @$xpath->query('/*/head/meta[@name="twitter:image"]/@content')->item(0)->nodeValue;
				}

				// than be opportunistic, try other tags
				if (empty($rmetas['link_description'])) {
					$rmetas['link_description'] = @$xpath->query('/*/head/meta[@name="description"]/@content')->item(0)->nodeValue;;
				}
				if (empty($rmetas['link_description'])) {
					$rmetas['link_description'] = @$xpath->query('/*/head/meta[@name="keywords"]/@content')->item(0)->nodeValue;;
				}
				if (empty($rmetas['link_title'])) {
					$rmetas['link_title'] = @$xpath->query('/*/head/title')->item(0)->nodeValue;
				}
				if (empty($rmetas['link_url'])) {
					$rmetas['link_url'] = @$xpath->query('/*/head/link[@rel="canonical"]/@href')->item(0)->nodeValue;
				}
				if (empty($rmetas['link_url'])) {
					$rmetas['link_url'] = $url;
				}
				if (empty($rmetas['link_image'])) {
					$rmetas['link_image'] = @$xpath->query('/*/head/link[@rel="apple-touch-icon"]/@href')->item(0)->nodeValue;
				}
			}
			ee()->cache->save('/dynamic_link/' . md5($url), $rmetas, 60 * 60 * 24);
		}
        $this->return_data = ee()->TMPL->parse_variables($tagdata, array($rmetas));
	}

    function encode()
    {
        return base64_encode(ee()->TMPL->fetch_param('url'));
    }
}
