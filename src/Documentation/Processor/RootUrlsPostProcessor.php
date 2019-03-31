<?php

namespace App\Documentation\Processor;

class RootUrlsPostProcessor implements DocumentationProcessorInterface
{
    const REGEX = '#<a href="(?P<url>(CONTRIBUTING|CHANGELOG|LICENSE))(?:(?P<anchor>\#.+)?)">#';

    public function process(string $documentation): string
    {
        return preg_replace_callback(self::REGEX, function($matches) {
            return '<a href="' . strtolower($matches['url']) . ($matches['anchor'] ?? '') . '">';
        }, $documentation);
    }
}
