<?php

namespace AurimasNiekis\GmoAozoraClient;

use AurimasNiekis\GmoAozoraClient\Exception\InvalidLoginDataException;
use AurimasNiekis\GmoAozoraClient\Exception\InvalidResponseReceivedException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @package AurimasNiekis\GmoAozoraClient\Http
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class WebClient
{
    private const USER_AGENT = 'GMO Aozora Client';

    private ClientInterface         $client;
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface  $streamFactory;
    private Configuration           $configuration;

    /**
     * @param Configuration           $configuration
     * @param ClientInterface         $client
     * @param RequestFactoryInterface $requestFactory
     * @param StreamFactoryInterface  $streamFactory
     */
    public function __construct(
        Configuration $configuration,
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory
    ) {
        $this->configuration  = $configuration;
        $this->client         = $client;
        $this->requestFactory = $requestFactory;
        $this->streamFactory  = $streamFactory;
    }

    public function authenticate(): void
    {
        $url         = $this->configuration->getSsoDomain() . '/b2c/v1/tickets';
        $requestForm = [
            'username' => $this->configuration->getUsername(),
            'password' => $this->configuration->getPassword(),
        ];

        if (false === empty($this->configuration->getDeviceToken())) {
            $requestForm['dfp'] = $this->configuration->getDeviceToken();
        }

        $request      = $this->createSSOPostRequest($url, $requestForm);
        $response     = $this->client->sendRequest($request);
        $responseData = $this->parseResponse($response);

        if (201 !== $response->getStatusCode()) {
            $response->getBody()->rewind();
            $responseBody = $response->getBody()->getContents();

            $errorSubCode = $responseData['errorSubCode'] ?? '';
            if ($errorSubCode === '084001') {
                throw new InvalidLoginDataException($responseData['errorMessage'] ?? '');
            }

            throw new InvalidResponseReceivedException('Unknown response received: "' . $responseBody . '"');
        }

        $dfp = $responseData['dfp'] ?? '';
        if (false === empty($dfp)) {
            $this->configuration->setDeviceToken($dfp);
        }

        $url         = $this->configuration->getSsoDomain() . '/b2c/v1/service-tickets';
        $requestForm = [
            'ga' => $responseData['ga'] ?? '',
            'service' => $this->configuration->getServiceType(),
        ];

        $request      = $this->createSSOPostRequest($url, $requestForm);
        $response     = $this->client->sendRequest($request);
        $responseData = $this->parseResponse($response);

        if (200 !== $response->getStatusCode()) {
            $response->getBody()->rewind();
            $responseBody = $response->getBody()->getContents();

            throw new InvalidResponseReceivedException('Unknown response received: "' . $responseBody . '"');
        }

        $stToken  = $responseData['st'] ?? '';
        $url      = $this->configuration->getApiDomain() . '?ticket=' . $stToken;
        $request  = $this->createApiGetRequest($url, false);
        $response = $this->client->sendRequest($request);

        if (200 !== $response->getStatusCode()) {
            $response->getBody()->rewind();
            $responseBody = $response->getBody()->getContents();

            throw new InvalidResponseReceivedException('Unknown response received: "' . $responseBody . '"');
        }

        $setCookies = $response->getHeader('Set-Cookie');
        $faToken    = null;
        foreach ($setCookies as $setCookie) {
            if (preg_match('/^fa=([^;]+);/', $setCookie, $matches)) {
                $faToken = $matches[1];

                break;
            }
        }

        if (null === $faToken) {
            throw new InvalidResponseReceivedException(
                'Missing fa token in request: "' . json_encode($response->getHeaders()) . '"'
            );
        }

        $this->configuration->setFaToken($faToken);
    }

    private function createSSOPostRequest(string $url, array $requestForm): RequestInterface
    {
        $request = $this->requestFactory->createRequest('POST', $url);

        return $request
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded; charset=utf-8')
            ->withHeader('User-Agent', static::USER_AGENT)
            ->withBody(
                $this->streamFactory->createStream(
                    http_build_query($requestForm)
                )
            );
    }

    public function parseResponse(ResponseInterface $response): array
    {
        $responseBody = $response->getBody()->getContents();

        return json_decode($responseBody, true, JSON_THROW_ON_ERROR);
    }

    private function createApiGetRequest(string $url, bool $authenticated = true): RequestInterface
    {
        $request = $this->requestFactory->createRequest('GET', $url)
            ->withHeader('User-Agent', static::USER_AGENT);

        if ($authenticated) {
            $request = $request->withHeader('Cookie', 'fa=' . $this->configuration->getFaToken() . ';');
        }

        return $request;
    }

    public function executeRequest(string $apiPath, bool $reAuthenticate = true): ResponseInterface
    {
        $url      = $this->configuration->getApiDomain() . $apiPath;
        $request  = $this->createApiGetRequest($url);
        $response = $this->client->sendRequest($request);

        if ($reAuthenticate && 490 === $response->getStatusCode()) {
            $this->authenticate();

            $request  = $this->createApiGetRequest($url);
            $response = $this->client->sendRequest($request);
        }

        return $response;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }
}