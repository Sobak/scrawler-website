<?php

namespace App\CommonMark;

use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Util\Xml;

class HeadingRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        if (!($node instanceof Heading)) {
            throw new \InvalidArgumentException('Incompatible node type: ' . get_class($node));
        }

        $tagName = 'h' . $node->getLevel();

        $attributes = [];
        $attributes['id'] = $this->generateAnchor($childRenderer->renderNodes($node->children()));

        foreach ($node->data->get('attributes', []) as $key => $value) {
            $attributes[$key] = Xml::escape($value);
        }

        return new HtmlElement($tagName, $attributes, $childRenderer->renderNodes($node->children()));
    }

    public function generateAnchor($title)
    {
        return preg_replace_callback('#[a-zA-Z0-9\' ]+#', function ($matches) {
            return strtolower(str_replace(["'", ' '], '-', $matches[0]));
        }, $title);
    }
}
