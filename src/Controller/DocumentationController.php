<?php

namespace App\Controller;

use App\Documentation\DocumentationParser;
use App\Documentation\FileNotFoundException;
use App\Documentation\Processor\InternalUrlPostProcessor;
use App\Documentation\Processor\NotePostProcessor;
use App\Documentation\Processor\RootUrlsPostProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DocumentationController extends AbstractController
{
    protected $documentationParser;

    public function __construct(DocumentationParser $documentationParser)
    {
        $this->documentationParser = $documentationParser;
    }

    public function index($file)
    {
        $this->documentationParser->addPostProcessor(new NotePostProcessor());
        $this->documentationParser->addPostProcessor(new InternalUrlPostProcessor());
        $this->documentationParser->addPostProcessor(new RootUrlsPostProcessor());

        try {
            return $this->render('documentation.html.twig', [
                'html' => $this->documentationParser->parseFile("docs/{$file}.md"),
            ]);
        } catch (FileNotFoundException $exception) {
            throw $this->createNotFoundException();
        }
    }
}
