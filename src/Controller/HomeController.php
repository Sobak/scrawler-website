<?php

namespace App\Controller;

use App\Documentation\DocumentationParser;
use App\Documentation\Processor\HomeHeaderPreProcessor;
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

        return $this->render('documentation.html.twig', [
            'html' => $this->documentationParser->parseFile('README.md'),
        ]);
    }
}
