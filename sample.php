<?php
/*
$url = 'http://ecs.amazonaws.jp/onca/xml';

$queryArray = array(
        'AWSAccessKeyId' => 'AKIAIS7XB6XXS4OOABHQ',
        'Oparation'      => 'ItemSearch',
        'SearchIndex'    => 'Books',
        );

$query = '';
*/

$request = array(
        'locale' => 'http://ecs.amazonaws.jp/onca/xml',
        'Service' => 'AWSECommerceService',
        'Operation' => 'ItemSearch',
        'AWSAccessKeyId' => 'AKIAIS7XB6XXS4OOABHQ',
        'AssociateTag' => 'cross_search-22',
        'SearchIndex' => 'Grocery',
        'Keywords' => '水',
        'ResponseGroup' => 'ItemAttributes,OfferSummary',
        'Version' => '20011-08-01',
        'secret_key' => '9u6RvFcBwLw2V8VhUpJOSn1wJJEOWVX2CvfptZHW',
        );

$response = Amazon($request);
print_r($response);


function Amazon($array)
{
    foreach($array as $key => $value) {
        if($key != 'secret_key' && $key != 'locale') {
            if(isset($params)) {
                $params .= sprintf('&%s=%s', $key, $value);
            } else {
                $params = sprintf('%s=%s', $key, $value);
            }
        }
    }
    $url = $array['locale'] . '?' . $params;
    $url_array = parse_url($url);
    parse_str($url_array['query'], $param_array);
    $param_array['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
    ksort($param_array);
    $str = sprintf("GET\n%s\n%s\n", $url_array['host'], $url_array['path']);
    $str_param = '';
    while(list($key, $value) = each($param_array))
        $str_param .= sprintf('%s=%s&', strtr($key, '_', '.'), rawurlencode($value));
    $str .= substr($str_param, 0, strlen($str_param) - 1);
    $signature = base64_encode(hash_hmac('sha256', $str, $array['secret_key'], true));
    $url_sig =  sprintf('%s://%s?%sSignature=%s', $url_array['scheme'], $url_array['host'] . $url_array['path'], $str_param, rawurlencode($signature));
    $xml = file_get_contents($url_sig);
    if($xml) {
        return simplexml_load_string($xml);
    } else {
        return false;
    }
}

