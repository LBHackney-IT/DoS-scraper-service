<?php

namespace App\Component\Utility;

/**
 * Provides DOMDocument helpers for parsing HTML strings.
 *
 * @package App\Plugins\WebPageScraper\Component\Utility
 */
class Html
{

    /**
     * Parse a full HTML string and return is as an DOMXPath object.
     *
     * @param string $html
     * @return \DOMXPath
     */
    public static function loadXpath($html)
    {
        $dom = static::load($html);
        $xpath = new \DOMXPath($dom);
        return $xpath;
    }

    /**
     * Parses a full HTML string and returns it as a DOM object.
     *
     * @param $html
     *
     * @return \DOMDocument
     */
    public static function load($html)
    {
        $dom = new \DOMDocument();

        // Ignore warnings during HTML soup loading.
        @$dom
            ->loadHTML($html);
        return $dom;
    }

    /**
     * Parses an HTML snippet and returns it as a DOM object.
     *
     * @param string $html
     *
     * @return \DOMDocument
     */
    public static function loadSnippet($html)
    {
        $document = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
<body>!html</body>
</html>
EOD;

        // PHP's \DOMDocument serialization adds extra whitespace when the markup
        // of the wrapping document contains newlines, so ensure we remove all
        // newlines before injecting the actual HTML body to be processed.
        $document = strtr($document, array(
            "\n" => '',
            '!html' => $html,
        ));
        $dom = new \DOMDocument();

        // Ignore warnings during HTML soup loading.
        @$dom
            ->loadHTML($document);
        return $dom;
    }

    /**
     * Normalizes an HTML snippet.
     *
     * This function is essentially \DOMDocument::normalizeDocument(), but operates on an HTML string instead of
     * a \DOMDocument.
     *
     * @param $html
     * @return mixed
     */
    public static function normalize($html)
    {
        $document = static::loadSnippet($html);
        return static::serialize($document);
    }

    /**
     * Converts the body of a \DOMDocument back to an HTML snippet.
     *
     * @param \DOMDocument $document
     * @return string
     */
    public static function serialize(\DOMDocument $document)
    {
        $body_node = $document
            ->getElementsByTagName('body')
            ->item(0);
        $html = '';
        if ($body_node !== null) {
            foreach ($body_node
                         ->getElementsByTagName('script') as $node) {
                static::escapeCdataElement($node);
            }
            foreach ($body_node
                         ->getElementsByTagName('style') as $node) {
                static::escapeCdataElement($node, '/*', '*/');
            }
            foreach ($body_node->childNodes as $node) {
                $html .= $document
                    ->saveXML($node);
            }
        }
        return $html;
    }

    /**
     * Adds comments around a <!CDATA section in a \DOMNode.
     *
     * @param \DOMNode $node
     * @param string $comment_start
     * @param string $comment_end
     */
    public static function escapeCdataElement(\DOMNode $node, $comment_start = '//', $comment_end = '')
    {
        foreach ($node->childNodes as $child_node) {
            if ($child_node instanceof \DOMCdataSection) {
                $embed_prefix = "\n<!--{$comment_start}--><![CDATA[{$comment_start} ><!--{$comment_end}\n";
                $embed_suffix = "\n{$comment_start}--><!]]>{$comment_end}\n";

                // Prevent invalid cdata escaping as this would throw a DOM error.
                // This is the same behavior as found in libxml2.
                // Related W3C standard: http://www.w3.org/TR/REC-xml/#dt-cdsection
                // Fix explanation: http://wikipedia.org/wiki/CDATA#Nesting
                $data = str_replace(']]>', ']]]]><![CDATA[>', $child_node->data);
                $fragment = $node->ownerDocument
                    ->createDocumentFragment();
                $fragment
                    ->appendXML($embed_prefix . $data . $embed_suffix);
                $node
                    ->appendChild($fragment);
                $node
                    ->removeChild($child_node);
            }
        }
    }

    /**
     * Escapes text by converting special characters to HTML entities.
     *
     * This method escapes HTML for sanitization purposes by replacing the
     * following special characters with their HTML entity equivalents:
     * - & (ampersand) becomes &amp;
     * - " (double quote) becomes &quot;
     * - ' (single quote) becomes &#039;
     * - < (less than) becomes &lt;
     * - > (greater than) becomes &gt;
     * Special characters that have already been escaped will be double-escaped
     * (for example, "&lt;" becomes "&amp;lt;"), and invalid UTF-8 encoding
     * will be converted to the Unicode replacement character ("�").
     *
     * @param string $text
     * @return string
     */
    public static function escape($text)
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
