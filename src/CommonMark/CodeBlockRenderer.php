<?php

declare(strict_types=1);

namespace App\CommonMark;

use Kadet\Highlighter\Formatter\HtmlFormatter;
use Kadet\Highlighter\Language\Language;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

class CodeBlockRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        if (!($node instanceof FencedCode)) {
            throw new \InvalidArgumentException('Incompatible node type: ' . get_class($node));
        }

        $source = $node->getLiteral();
        $languageName = $node->getInfo();

        $language = Language::byName(empty($languageName) ? 'text' : $languageName);
        $formatter = new HtmlFormatter();

        $result = \Kadet\Highlighter\highlight($source, $language, $formatter);

        return '<pre class="keylighter"><code>' . $result . '</code></pre>';
    }
}
