<?php

declare(strict_types=1);

namespace App\Documentation\Processor;

interface DocumentationProcessorInterface
{
    public function process(string $documentation): string;
}
