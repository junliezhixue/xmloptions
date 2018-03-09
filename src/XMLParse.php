<?php
namespace xmlparse;

use xmlparse\ErrorCode;
use xmlparse\XmlException;

/**
 * XMLParse class
 *
 * 提供提取消息格式中的密文及生成回复消息格式的接口.
 */
class XMLParse
{

    /**
     * 提取出xml数据包中的字段消息
     * @param string $xmltext   待提取的xml字符串
     * @return json 提取出的加密消息字符串数组
     * @author  qidongbo
     * @date    2018/3/8
     */
    public static function extract($xmltext)
    {
        $xmlarr = [];
        try {
            $xml = new \DOMDocument();
            $xml->loadXML($xmltext);
            $x = $xml->documentElement;
            foreach ($x->childNodes as $item)
            {
                if (is_string($item->nodeValue)) {
                    $normal = str_replace(array("\r\n", "\r", "\n"), '', $item->nodeValue);
                }
                if (!empty($normal) || $item->nodeValue === '0') {
                    $xmlarr[$item->nodeName] = $item->nodeValue;
                }
            }
            return $xmlarr;
        } catch (\Exception $e) {
            throw new WxencryptException(ErrorCode::$ParseXmlError);
        }
    }

    /**
     * 将消息密文和安全签名等xml数据打包成xml格式
     * @param   string $dataArray 数据数组，键名为xml标签，键值为xml CDATA值
     * @author  qidongbo
     * @date    2018/3/8
     */
    public static function generate($dataArray)
    {
        $format = "<xml>";
        if (!is_array($dataArray) || empty($dataArray)) {
            throw new WxencryptException(ErrorCode::$XmlParamError);
        }
        foreach ($dataArray as $k => $v) {
            $format .= '<' . ucfirst($k) . '><![CDATA[' . $v . ']]></' . ucfirst($k) . '>';
        }
        $format .= "</xml>";
        return $format;
    }
}


?>