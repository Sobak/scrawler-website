<?php

declare(strict_types=1);

namespace App\Controller;

use App\Documentation\DocumentationParser;
use App\Documentation\Processor\HomeHeaderPreProcessor;
use App\Documentation\Processor\InternalUrlPostProcessor;
use App\Documentation\Processor\NotePostProcessor;
use App\Documentation\Processor\RootUrlsPostProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    protected $documentationParser;

    public function __construct(DocumentationParser $documentationParser)
    {
        $this->documentationParser = $documentationParser;
    }

    public function index()
    {
        $this->documentationParser->addPreProcessor(new HomeHeaderPreProcessor());
        $this->documentationParser->addPostProcessor(new NotePostProcessor());
        $this->documentationParser->addPostProcessor(new InternalUrlPostProcessor());
        $this->documentationParser->addPostProcessor(new RootUrlsPostProcessor());

        return $this->render('documentation.html.twig', [
            'html' => $this->documentationParser->parseFile('README.md'),
        ]);
    }
}
