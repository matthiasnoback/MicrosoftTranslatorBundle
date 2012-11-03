<?php

namespace MatthiasNoback\MicrosoftTranslatorBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Buzz\Listener\History\Journal;

class MicrosoftTranslatorDataCollector extends DataCollector
{
    private $journal;

    public function __construct(Journal $journal)
    {
        $this->journal = $journal;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'journal' => $this->journal,
        );
    }

    public function getNumberOfCalls()
    {
        return count($this->data['journal']);
    }

    public function getJournal()
    {
        return $this->data['journal'];
    }

    public function getName()
    {
        return 'microsoft_translator';
    }
}
