<?php

namespace App\Documentation\Processor;

interface DocumentationProcessorInterface
{
    public function process(string $documentation): string;
}
