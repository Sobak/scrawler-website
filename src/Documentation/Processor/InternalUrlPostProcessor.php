<?php

namespace App\Documentation\Processor;

class InternalUrlPostProcessor implements DocumentationProcessorInterface
{
    const REGEX = '#<a href="(?P<url>.+\.md)(?:(?P<anchor>\#.+)?)">#';

    public function process(string $documentation): string
    {
        return preg_replace_callback(self::REGEX, function($matches) {
            return '<a href="' . substr($matches['url'], 0, -3) . ($matches['anchor'] ?? '') . '">';
        }, $documentation);
    }
}
