<?php

class AccessLibrary {

    public function search($SearchIndex, $keyword) {
        $AccesskeyID = 'AKIAIS7XB6XXS4OOABHQ';
        $SecretAccessKey = '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';

        $method = "GET";
        //$host = 'webservices.amazon.co.jp';
        $host = 'ecs.amazonaws.jp';
        $uri = "/onca/xml";

        $params['AWSAccessKeyId'] = $AccesskeyID;
        $params['Service'] = 'AWSECommerceService';
        $params['Version'] = '2009-01-06';
        $params['Operation'] = 'ItemSearch';
        $params['ResponseGroup'] = 'Large';
        //$params['ResponseGroup'] = 'Small,Images';
        $params['SearchIndex'] = $SearchIndex;
        $params['Keywords'] = $keyword;
        $params['AssociateTag'] = 'cross_search-22';
        //$params['Timestamp'] = gmdate('Y-m-dTH:i:sZ');
        $params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');

        ksort($params);

        $query = array();
        foreach ($params as $param => $value) {
            $param = str_replace("%7E", "~", rawurlencode($param));
            $value = str_replace("%7E", "~", rawurlencode($value));
            $query[] = $param . "=" . $value;
        }
        $query = implode("&", $query);

        $Signature = $method . "n" . $host . "n" . $uri . "n" . $query;
        $Signature = base64_encode(hash_hmac("sha256", $Signature, $SecretAccessKey, True));
        $Signature = str_replace("%7E", "~", rawurlencode($Signature));

        $url = "http://" . $host . $uri . "?" . $query . "&Signature=" . $Signature;

        $ret = "";
        $xml = file_get_contents($url);
        //$xml = simplexml_load_file($url);

        if ($xml === false) {
            return false;
        }
        else {
            foreach ($xml->Items->Item as $Item) {
                $ret .= '<a href="' . $Item->DetailPageURL . '"><img src="' . $Item->MediumImage->URL . "></a><br />n";
                $ret .= '<a href="' . $Item->DetailPageURL . '">' . $Item->ItemAttributes->Title . "</a><br />n";
            }
        }

        return $ret;
    }

    //function Amazon($array)
    function Amazon($keyword, $index = 'All', $page = 1)
    {
        $array = array(
                //'locale' => 'http://ecs.amazonaws.jp/onca/xml',
                'locale' => 'http://xml-jp.amznxslt.com/onca/xml',
                'Service' => 'AWSECommerceService',
                'Operation' => 'ItemSearch',
                'AWSAccessKeyId' => 'AKIAIS7XB6XXS4OOABHQ',
                'AssociateTag' => 'cross_search-22',
                'SearchIndex' => $index,
                //'SearchIndex' => 'All',
                //'SearchIndex' => 'Apparel',
                'Keywords' => $keyword,
                //'Version' => '2010-09-01',
                'Version' => '2009-07-01',
                'secret_key' => '9u6RvFcBwLw2V8VhUpJOSn1wJJEOWVX2CvfptZHW',
                //'ResponseGroup' => 'Images',
                'ResponseGroup' => 'Medium',
                'ItemPage' => $page,
                //'ContentType'   => 'text/html',
                //'Style'         => 'http://search.motanilab.info/public/xslt_style_sheet.xsl',
                'Sort'          => 'salesrank',
                //'Sort'          => '-price',
                );

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
       
        if ($xml === false) {
            return false;
        }
        if($xml) {
            $resultObject = simplexml_load_string($xml);
            foreach ($resultObject->Items->Item as $Item) {
                $this->items[] = $this->object2array($Item);
            }
            $this->totalCount = (string)$resultObject->Items->TotalResults;
            $this->totalPage  = (string)$resultObject->Items->TotalPages;
            //return $this->object2array(simplexml_load_string($xml));
            return true;
        }
        
        if ($xml === false) {
            return false;
        }
        else {
            foreach ($xml->Items->Item as $Item) {
                $ret .= '<a href="' . $Item->DetailPageURL . '"><img src="' . $Item->MediumImage->URL . "></a><br />n";
                $ret .= '<a href="' . $Item->DetailPageURL . '">' . $Item->ItemAttributes->Title . "</a><br />n";
            }
        }
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getCount()
    {
        return $this->totalCount;
    }

    public function getPage()
    {
        return $this->totalPage;
    }

    public function object2array($data)
    {
        if (is_object($data)) {
            $data = (array)$data;
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $key1 = (string)$key;
                $key2 = preg_replace('/\W/', ':', $key1);

                if (is_object($value) or is_array($value)) {
                    $data[$key2] = $this->object2array($value);
                } else {
                    $data[$key2] = (string)$value;
                }

                if ($key1 != $key2) {
                    unset($data[$key1]);
                }
            }
        }

        return $data;
    }
}
