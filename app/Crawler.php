<?php

namespace App;

class Crawler
{
    protected string $url;
    protected array $uriCompose;
    protected \DOMDocument $dom;

    public function __construct(string $url, \DOMDocument $dom)
    {
        $this->loadClass($url, $dom);
    }

    public function loadClass(string $url, \DOMDocument $dom) {
        $this->url = $url;
        $this->uriCompose = parse_url($url);
        $this->dom = $dom;
    }

    protected function processHref($node)
    {
        $elements = $this->dom->getElementsByTagName($node);

        foreach ($elements as $element) {
            $href = $element->getAttribute('href');
            if (0 !== strpos($href, 'http')) {
                $path = '/' . ltrim($href, '/');
                if (extension_loaded('http')) {
                    $href = http_build_url($this->url, array('path' => $path));
                } else {
                    $parts = $this->uriCompose;
                    $href = $parts['scheme'] . '://';
                    if (isset($parts['user']) && isset($parts['pass'])) {
                        $href .= $parts['user'] . ':' . $parts['pass'] . '@';
                    }
                    $href .= $parts['host'];
                    if (isset($parts['port'])) {
                        $href .= ':' . $parts['port'];
                    }
                    $href .= $path;
                }
            }
            $element->setAttribute('href', $href);
            $this->dom->saveHTML();
        }
    }

    public function lastModification(\DateTime &$lastDate, &$result) {
        $classname = 'resultadoBuscaItem';
        $finder = new \DomXPath($this->dom);
        $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");

        $tmpDom = new \DOMDocument();
        foreach ($nodes as $node) {
            $tmpDom->appendChild($tmpDom->importNode($node,true));
            $anchor = $tmpDom->getElementsByTagName('a');
            if ($anchor->length > 0) {
                $anchor = $anchor[0];
                $date = trim(explode('-', trim($anchor->nodeValue))[0]);
                $lastDate = \DateTime::createFromFormat('d/m/Y', $date);
                $result = $anchor->getAttribute('href');
            }

            break;
        }
    }

    protected function crawlPage()
    {
        $content = file_get_contents($this->url);
        @$this->dom->loadHTML($content);
        $this->processHref('link');
        $this->processHref('a');
    }

    public function render() {
        return $this->dom->saveHTML();
    }

    public function run()
    {
        $this->crawlPage();
    }
}