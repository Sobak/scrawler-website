<?php

namespace App\Documentation;

use App\CommonMark\CodeBlockRenderer;
use App\CommonMark\HeadingRenderer;
use App\Documentation\Processor\DocumentationProcessorInterface;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;

class DocumentationParser
{
    protected $commonMarkRenderer;

    protected $commonMarkParser;

    /** @var DocumentationProcessorInterface[] */
    protected $preProcessors = [];

    /** @var DocumentationProcessorInterface[] */
    protected $postProcessors = [];

    protected $scrawlerSourcesPath;

    public function __construct(string $scrawlerSourcesPath)
    {
        $commonMarkEnvironment = $this->configureCommonMarkEnvironment();

        $this->scrawlerSourcesPath = $scrawlerSourcesPath;
        $this->commonMarkParser = new DocParser($commonMarkEnvironment);
        $this->commonMarkRenderer = new HtmlRenderer($commonMarkEnvironment);
    }

    public function addPreProcessor(DocumentationProcessorInterface $processor)
    {
        $this->preProcessors[] = $processor;

        return $this;
    }

    public function addPostProcessor(DocumentationProcessorInterface $processor)
    {
        $this->postProcessors[] = $processor;

        return $this;
    }

    public function parseFile(string $filename): string
    {
        $contents = $this->readFileFromScrawler($filename);

        foreach  ($this->preProcessors as $preProcessor) {
            $contents = $preProcessor->process($contents);
        }

        $document = $this->commonMarkParser->parse($contents);

        $html = $this->commonMarkRenderer->renderBlock($document);

        foreach ($this->postProcessors as $postProcessor) {
            $html = $postProcessor->process($html);
        }

        return $html;
    }

    protected function readFileFromScrawler(string $filename): string
    {
        if (array_reverse(explode('.', $filename))[0] !== 'md') {
            throw new FileNotFoundException();
        }

        $path = $this->scrawlerSourcesPath . '/' . $filename;

        if (is_file($path) === false || is_readable($path) === false) {
            throw new FileNotFoundException();
        }

        return file_get_contents($path);
    }

    protected function configureCommonMarkEnvironment(): Environment
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addBlockRenderer(FencedCode::class, new CodeBlockRenderer());
        $environment->addBlockRenderer(Heading::class, new HeadingRenderer());

        return $environment;
    }
}
