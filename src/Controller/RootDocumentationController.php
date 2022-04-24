<?php

declare(strict_types=1);

namespace App\Controller;

use App\Documentation\DocumentationParser;
use App\Documentation\FileNotFoundException;
use App\Documentation\Processor\InternalUrlPostProcessor;
use App\Documentation\Processor\NotePostProcessor;
use App\Documentation\Processor\RootUrlsPostProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class RootDocumentationController extends AbstractController
{
    private DocumentationParser $documentationParser;

    public function __construct(DocumentationParser $documentationParser)
    {
        $this->documentationParser = $documentationParser;
    }

    public function index($file): Response
    {
        $this->documentationParser->addPostProcessor(new NotePostProcessor());
        $this->documentationParser->addPostProcessor(new InternalUrlPostProcessor());
        $this->documentationParser->addPostProcessor(new RootUrlsPostProcessor());

        $file = strtoupper($file);

        try {
            return $this->render('documentation.html.twig', [
                'html' => $this->documentationParser->parseFile("{$file}.md"),
            ]);
        } catch (FileNotFoundException $exception) {
            throw $this->createNotFoundException();
        }
    }
}
