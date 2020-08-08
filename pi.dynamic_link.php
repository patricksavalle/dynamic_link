<?php /** @noinspection PhpUnused */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
		$key = __CLASS__ . '/' . md5($url);
		$metadata = ee()->cache->get($key);
		if (empty($metadata))
		{
			// read the file with our own user-agent, restore after
			$user_agent = ini_get( 'user_agent');
			ini_set('user_agent', 'We come in peace - https://hersengarage.nl');
			$html = @file_get_contents($url);
			ini_set('user_agent', $user_agent);

			if (empty($html)) {
				$metadata = ["crawled" => false];
			} else {
				$metadata = ["crawled" => true];
				libxml_use_internal_errors(true);
				$doc = new DomDocument();
				$doc->loadHTML($html);
				$xpath = new DOMXPath($doc);

				// first try og:
				$metadata['link_url'] = @$xpath->query('/*/head/meta[@property="og:url"]/@content')->item(0)->nodeValue;
				$metadata['link_title'] = @$xpath->query('/*/head/meta[@property="og:title"]/@content')->item(0)->nodeValue;
				$metadata['link_description'] = @$xpath->query('/*/head/meta[@property="og:description"]/@content')->item(0)->nodeValue;
				$metadata['link_image'] = @$xpath->query('/*/head/meta[@property="og:image"]/@content')->item(0)->nodeValue;
				$metadata['link_site_name'] = @$xpath->query('/*/head/meta[@property="og:site_name"]/@content')->item(0)->nodeValue;

				// than try twitter:
				if (empty($metadata['link_url'])) {
					$metadata['link_url'] = @$xpath->query('/*/head/meta[@name="twitter:url"]/@content')->item(0)->nodeValue;
				}
				if (empty($metadata['link_title'])) {
					$metadata['link_title'] = @$xpath->query('/*/head/meta[@name="twitter:title"]/@content')->item(0)->nodeValue;
				}
				if (empty($metadata['link_description'])) {
					$metadata['link_description'] = @$xpath->query('/*/head/meta[@name="twitter:description"]/@content')->item(0)->nodeValue;
				}
				if (empty($metadata['link_image'])) {
					$metadata['link_image'] = @$xpath->query('/*/head/meta[@name="twitter:image"]/@content')->item(0)->nodeValue;
				}
				if (empty($metadata['link_site_name'])) {
					$metadata['link_site_name'] = @$xpath->query('/*/head/meta[@name="twitter:site"]/@content')->item(0)->nodeValue;
				}

				// than be opportunistic, try other tags
				if (empty($metadata['link_description'])) {
					$metadata['link_description'] = @$xpath->query('/*/head/meta[@name="description"]/@content')->item(0)->nodeValue;
				}
				if (empty($metadata['link_description'])) {
					$metadata['link_description'] = @$xpath->query('/*/head/meta[@name="keywords"]/@content')->item(0)->nodeValue;
				}
				if (empty($metadata['link_title'])) {
					$metadata['link_title'] = @$xpath->query('/*/head/title')->item(0)->nodeValue;
				}
				if (empty($metadata['link_url'])) {
					$metadata['link_url'] = @$xpath->query('/*/head/link[@rel="canonical"]/@href')->item(0)->nodeValue;
				}
				if (empty($metadata['link_url'])) {
					$metadata['link_url'] = $url;
				}
				if (empty($metadata['link_image'])) {
					$metadata['link_image'] = @$xpath->query('/*/head/link[@rel="apple-touch-icon"]/@href')->item(0)->nodeValue;
				}

				// @Todo use Google JSON-LD data

				$metadata['link_domain'] = parse_url($metadata['link_url'], PHP_URL_HOST);
				if (empty($metadata['link_site_name'])) {
					$metadata['link_site_name'] = $metadata['link_domain'];
				}
				ee()->cache->save($key, $metadata, 60 * 60 * 24);
			}
		}
        $this->return_data = ee()->TMPL->parse_variables($tagdata, array($metadata));
	}

    function encode()
    {
        return base64_encode(ee()->TMPL->fetch_param('url'));
    }

}
