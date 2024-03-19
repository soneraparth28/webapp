<?php


namespace App\Helpers\Traits;


use App\Helpers\Traits\Contracts\ShouldParseTemplate;
use DOMDocument;

trait InteractsWithTemplate
{

    private function template($htmlString): ShouldParseTemplate
    {
        return new class($htmlString) implements ShouldParseTemplate
        {
            private string $body;
            public $dom;

            public function __construct(string $body)
            {
                $this->body = $body;
                $this->dom = (new DomDocument);
                $this->dom->loadHtml($body);
            }

            public function bypassAnchors(\Closure $to): ShouldParseTemplate
            {
                foreach ($this->dom->getElementsByTagName('a') as $item) {
                    $item->setAttribute('href', $to($item));
                }

                $this->body = $this->dom->saveHTML();

                return $this;
            }

            public function restoreTrackerAnchors(): ShouldParseTemplate
            {
                return $this->bypassAnchors(function ($item) {
                    $queries = preg_split("/to=/", urldecode(parse_url($item->getAttribute('href'), PHP_URL_QUERY)));
                    return count($queries) > 1 ? $queries[1] : $item->getAttribute('href');
                });
            }

            public function removeTrackerElement(): ShouldParseTemplate
            {
                foreach ($this->dom->getElementsByTagName('img') as $item) {
                    /** @var \DOMElement $item */
                    if (str_contains($item->getAttribute('src'), 'opened')) {
                        $item->parentNode->removeChild($item);
                    }
                }

                $this->body = $this->dom->saveHTML();

                return $this;
            }

            public function get(): string
            {
                return $this->body;
            }

        };
    }
}