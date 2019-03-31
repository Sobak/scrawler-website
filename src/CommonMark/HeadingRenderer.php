<?php

namespace App\CommonMark;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Util\Xml;

class HeadingRenderer implements BlockRendererInterface
{
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false)
    {
        if (!($block instanceof Heading)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        $tag = 'h' . $block->getLevel();

        $attrs = [];
        $attrs['id'] = $this->generateAnchor($block->getStringContent());

        foreach ($block->getData('attributes', []) as $key => $value) {
            $attrs[$key] = Xml::escape($value);
        }

        return new HtmlElement($tag, $attrs, $htmlRenderer->renderInlines($block->children()));
    }

    public function generateAnchor($title)
    {
        return preg_replace_callback('#[a-zA-Z0-9\' ]+#', function ($matches) {
            return strtolower(str_replace(["'", ' '], '-', $matches[0]));
        }, $title);
    }
}
