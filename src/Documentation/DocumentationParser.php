<?php

namespace App\Documentation;

use App\CommonMark\CodeBlockRenderer;
use App\Kernel;
use Exception;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;

class DocumentationParser
{
    protected $commonMarkRenderer;

    protected $commonMarkParser;

    protected $projectRoot;

    protected $scrawlerSourcesPath;

    public function __construct(string $scrawlerSourcesPath, string $projectRoot)
    {
        $commonMarkEnvironment = $this->configureCommonMarkEnvironment();

        $this->scrawlerSourcesPath = $scrawlerSourcesPath;
        $this->commonMarkParser = new DocParser($commonMarkEnvironment);
        $this->commonMarkRenderer = new HtmlRenderer($commonMarkEnvironment);
        $this->projectRoot = $projectRoot;
    }

    public function parseFile(string $filename): string
    {
        $contents = $this->readFileFromScrawler($filename);

        $document = $this->commonMarkParser->parse($contents);

        return $this->commonMarkRenderer->renderBlock($document);
    }

    protected function readFileFromScrawler(string $filename): string
    {
        $path = $this->projectRoot . '/' . $this->scrawlerSourcesPath . '/' . $filename;

        if (is_file($path) === false || is_readable($path) === false) {
            throw new Exception('File not found');
        }

        return file_get_contents($path);
    }

    protected function configureCommonMarkEnvironment(): Environment
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addBlockRenderer(FencedCode::class, new CodeBlockRenderer());

        return $environment;
    }
}
