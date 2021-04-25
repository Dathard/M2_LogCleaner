<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Service;

use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\Webapi\Rest\Request;

class GitApiService
{
    const API_REQUEST_URI = 'https://api.github.com/';

    const API_REQUEST_ENDPOINT = 'repos/';

    const REPOSITORY_NAME = 'Dathard/M2_LogCleaner';

    /**
     * @var \GuzzleHttp\Psr7\ResponseFactory
     */
    private $responseFactory;

    /**
     * @var \GuzzleHttp\ClientFactory
     */
    private $clientFactory;

    /**
     * GitApiService constructor.
     * @param \GuzzleHttp\ClientFactory $clientFactory
     * @param \GuzzleHttp\Psr7\ResponseFactory $responseFactory
     */
    public function __construct(
        ClientFactory $clientFactory,
        ResponseFactory $responseFactory
    ) {
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @return null|array
     */
    public function getReleasesList()
    {
        $uriEndpoint = static::API_REQUEST_ENDPOINT . static::REPOSITORY_NAME . '/releases';
        $response = $this->doRequest($uriEndpoint);

        if ($response->getStatusCode() != 200) {
            return null;
        }

        $responseContent = $response->getBody()->getContents(); // here you will have the API response in JSON format

        return json_decode($responseContent, true);
    }

    /**
     * @return null|array
     */
    public function getLatestRelease()
    {
        $uriEndpoint = static::API_REQUEST_ENDPOINT . static::REPOSITORY_NAME . '/releases/latest';
        $response = $this->doRequest($uriEndpoint);

        if ($response->getStatusCode() != 200) {
            return null;
        }

        $responseContent = $response->getBody()->getContents(); // here you will have the API response in JSON format

        return json_decode($responseContent, true);
    }

    /**
     * Do API request with provided params
     *
     * @param string $uriEndpoint
     * @param array $params
     * @param string $requestMethod
     *
     * @return Response
     */
    private function doRequest(
        string $uriEndpoint,
        array $params = [],
        string $requestMethod = Request::HTTP_METHOD_GET
    ): Response {
        /** @var \GuzzleHttp\ClientFactory $client */
        $client = $this->clientFactory->create(['config' => [
            'base_uri' => self::API_REQUEST_URI
        ]]);

        try {
            $response = $client->request(
                $requestMethod,
                $uriEndpoint,
                $params
            );
        } catch (GuzzleException $exception) {
            /** @var \GuzzleHttp\Psr7\ResponseFactory $response */
            $response = $this->responseFactory->create([
                'status' => $exception->getCode(),
                'reason' => $exception->getMessage()
            ]);
        }

        return $response;
    }
}
