<?php

namespace LLPhant\Tool;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use LLPhant\Experimental\Agent\Render\CLIOutputUtils;
use LLPhant\Experimental\Agent\Render\OutputAgentInterface;

class ApiRequest extends ToolBase
{
    /**
     * @throws Exception
     */
    public function __construct(bool $verbose = false, public OutputAgentInterface $outputAgent = new CLIOutputUtils())
    {
        parent::__construct($verbose);
    }

    /**
     * Perform a http get call to the provided url
     */
    public function get_data_from_url(string $url): string
    {
        try {
            $this->outputAgent->renderTitleAndMessageOrange('🔧 Executing tool ApiRequest', $url, $this->verbose);
            $client = new Client();
            $response = $client->request('GET', $url);

            $rawContent = $response->getBody()->getContents();
            $this->outputAgent->render("Results from ApiRequest to {$url}: {$rawContent}", $this->verbose);
            $this->wasSuccessful = true;

        } catch (GuzzleException $e) {
            $this->wasSuccessful = false;
            $this->lastResponse = $e->getMessage();
        }

        return $rawContent ?? '';
    }
}
