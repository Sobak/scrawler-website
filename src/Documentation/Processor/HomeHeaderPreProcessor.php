<?php

namespace App\Documentation\Processor;

class HomeHeaderPreProcessor implements DocumentationProcessorInterface
{
    public function process(string $documentation): string
    {
        $lines = explode("\n", $documentation);

        for ($i = 0; $i <= count($lines); $i++) {
            if ($lines[$i] !== "\n" && (isset($lines[$i][0]) && $lines[$i][0] !== '#')) {
                break;
            }

            unset($lines[$i]);
        }

        return implode("\n", $lines);
    }
}
