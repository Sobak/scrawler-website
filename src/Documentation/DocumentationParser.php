<?php

declare(strict_types=1);

namespace App\Documentation;

use App\CommonMark\CodeBlockRenderer;
use App\Documentation\Processor\DocumentationProcessorInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Renderer\HtmlRenderer;

class DocumentationParser
{
    private HtmlRenderer $commonMarkRenderer;

    private MarkdownParser $commonMarkParser;

    /** @var DocumentationProcessorInterface[] */
    private array $preProcessors = [];

    /** @var DocumentationProcessorInterface[] */
    private array $postProcessors = [];

    private string $scrawlerSourcesPath;

    public function __construct(string $scrawlerSourcesPath)
    {
        $commonMarkEnvironment = $this->configureCommonMarkEnvironment();

        $this->scrawlerSourcesPath = $scrawlerSourcesPath;
        $this->commonMarkParser = new MarkdownParser($commonMarkEnvironment);
        $this->commonMarkRenderer = new HtmlRenderer($commonMarkEnvironment);
    }

    public function addPreProcessor(DocumentationProcessorInterface $processor): self
    {
        $this->preProcessors[] = $processor;

        return $this;
    }

    public function addPostProcessor(DocumentationProcessorInterface $processor): self
    {
        $this->postProcessors[] = $processor;

        return $this;
    }

    /**
     * @param string $filename
     * @return string
     *
     * @throws FileNotFoundException
     */
    public function parseFile(string $filename): string
    {
        $contents = $this->readFileFromScrawler($filename);

        foreach  ($this->preProcessors as $preProcessor) {
            $contents = $preProcessor->process($contents);
        }

        $document = $this->commonMarkParser->parse($contents);

        $html = $this->commonMarkRenderer->renderDocument($document);
        $html = $html->getContent();

        foreach ($this->postProcessors as $postProcessor) {
            $html = $postProcessor->process($html);
        }

        return $html;
    }

    /**
     * @param string $filename
     * @return string
     *
     * @throws FileNotFoundException
     */
    private function readFileFromScrawler(string $filename): string
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

    private function configureCommonMarkEnvironment(): Environment
    {
        $config = [
            'heading_permalink' => [
                'html_class' => 'heading-permalink',
                'id_prefix' => '',
                'fragment_prefix' => '',
                'insert' => 'after',
                'min_heading_level' => 1,
                'max_heading_level' => 4,
                'symbol' => '#',
            ],
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());
        $environment->addRenderer(FencedCode::class, new CodeBlockRenderer());

        return $environment;
    }
}
