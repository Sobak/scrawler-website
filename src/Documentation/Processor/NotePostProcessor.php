<?php

declare(strict_types=1);

namespace App\Documentation\Processor;

class NotePostProcessor implements DocumentationProcessorInterface
{
    const REGEX = '#<blockquote>(\n)<p><strong>Note:<\/strong>+(?P<note>.+?)<\/p>(\n)+<\/blockquote>#ms';

    public function process(string $documentation): string
    {
        return preg_replace_callback(self::REGEX, function($matches) {
            return '<div class="note"><p>' . $this->ucfirst(trim($matches['note'])) . '</p></div>';
        }, $documentation);
    }

    protected function ucfirst($string)
    {
        $strlen = mb_strlen($string);
        $firstChar = mb_substr($string, 0, 1);
        $then = mb_substr($string, 1, $strlen - 1);

        return mb_strtoupper($firstChar) . $then;
    }
}
