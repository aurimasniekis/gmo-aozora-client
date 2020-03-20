<?php

namespace AurimasNiekis\GmoAozoraClient;

use AurimasNiekis\GmoAozoraClient\Exception\InvalidResponseReceivedException;
use AurimasNiekis\GmoAozoraClient\Model\AccountDetails;
use AurimasNiekis\GmoAozoraClient\Model\StatementEntry;
use AurimasNiekis\GmoAozoraClient\Model\VisaStatementEntry;
use DateTime;
use DateTimeInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @package AurimasNiekis\GmoAozoraClient
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class Client
{
    public const  STATEMENT_VISA     = 'visa/statement/account';
    public const  STATEMENT_ORDINARY = 'ordinary-deposits/statement';
    public const  STATEMENT_SWEEP    = 'sweep-accounts/statement';
    public const  STATEMENT_TERM     = 'term-deposits/statement';
    public const  STATEMENT_FOREIGN  = 'fcy-ordinary-deposits/statement';

    public const STATEMENT_TYPES = [
        self::STATEMENT_VISA => true,
        self::STATEMENT_SWEEP => true,
        self::STATEMENT_ORDINARY => true,
        self::STATEMENT_TERM => true,
        self::STATEMENT_FOREIGN => true,
    ];

    private WebClient $client;

    /**
     * @param WebClient $client
     */
    public function __construct(WebClient $client)
    {
        $this->client = $client;
    }

    public function accountDetails(): AccountDetails
    {
        $response     = $this->executeRequest('/top');
        $responseData = $this->client->parseResponse($response);

        return new AccountDetails($responseData);
    }

    public function executeRequest(string $path): ResponseInterface
    {
        $response = $this->client->executeRequest($path);

        if (200 !== $response->getStatusCode()) {
            $response->getBody()->rewind();
            $responseBody = $response->getBody()->getContents();

            throw new InvalidResponseReceivedException('Unknown response received: "' . $responseBody . '"');
        }

        return $response;
    }

    public function ordinaryDepositStatement(
        DateTimeInterface $toDate = null,
        DateTimeInterface $fromDate = null,
        int $perPage = 2000
    ): array {
        $options = [
            'depositOrderType' => 2,
        ];

        $statementList = $this->rawStatementAll(static::STATEMENT_ORDINARY, $options, $toDate, $fromDate, $perPage);

        $results = [];
        foreach ($statementList as $statement) {
            $results[] = StatementEntry::fromArray($statement);
        }

        return $results;
    }

    public function rawStatementAll(
        string $type,
        array $options,
        DateTimeInterface $toDate = null,
        DateTimeInterface $fromDate = null,
        int $perPage = 2000
    ): array {
        $continue      = true;
        $page          = 0;
        $statementList = [];
        while ($continue) {
            $offset        = $perPage * $page;
            $response      = $this->rawStatement($type, $options, $toDate, $fromDate, $offset, $perPage);
            $foundResults  = $response['statementList'] ?? [];
            $statementList = array_merge($statementList, $foundResults);

            if (count($foundResults) < 1) {
                $continue = false;
            }

            $page++;
        }

        return $statementList;
    }

    public function rawStatement(
        string $type,
        array $options,
        DateTimeInterface $toDate = null,
        DateTimeInterface $fromDate = null,
        int $offset = 0,
        int $limit = 20
    ): array {
        if (false === isset(static::STATEMENT_TYPES[$type])) {
            throw new \InvalidArgumentException(
                'Invalid statement type "' . $type . '" allowed: "' . implode(', ', static::STATEMENT_TYPES) . '"'
            );
        }

        if ($limit > 2000) {
            $limit = 2000;
        }

        if ($limit <= 0) {
            $limit = 1;
        }

        if ($offset < 0) {
            $offset = 0;
        }

        if (null === $toDate) {
            $toDate = new DateTime();
        }

        $queryData = array_merge(
            [
                'limit' => $limit,
                'offset' => $offset,
                'toDate' => $toDate->format('Ymd'),
            ],
            $options
        );

        if (null !== $fromDate) {
            $queryData['fromDate'] = $fromDate->format('Ymd');
        }

        $url      = '/' . $type . '?' . http_build_query($queryData);
        $response = $this->executeRequest($url);

        return $this->client->parseResponse($response);
    }

    public function foreignOrdinaryDepositStatement(
        string $currency,
        DateTimeInterface $toDate = null,
        DateTimeInterface $fromDate = null,
        int $perPage = 2000
    ): array {
        $options = [
            'currency' => $currency,
            'depositOrderType' => 2,
        ];

        $statementList = $this->rawStatementAll(static::STATEMENT_FOREIGN, $options, $toDate, $fromDate, $perPage);

        $results = [];
        foreach ($statementList as $statement) {
            $results[] = StatementEntry::fromArray($statement);
        }

        return $results;
    }

    public function visaStatement(
        DateTimeInterface $toDate = null,
        DateTimeInterface $fromDate = null,
        int $perPage = 2000
    ): array {
        $statementList = $this->rawStatementAll(static::STATEMENT_VISA, [], $toDate, $fromDate, $perPage);

        $results = [];
        foreach ($statementList as $statement) {
            $results[] = new VisaStatementEntry($statement);
        }

        return $results;
    }

    public function termDepositStatement(
        DateTimeInterface $toDate = null,
        DateTimeInterface $fromDate = null,
        int $perPage = 2000
    ): array {
        $options = [
            'depositOrderType' => 2,
        ];

        $statementList = $this->rawStatementAll(static::STATEMENT_TERM, $options, $toDate, $fromDate, $perPage);

        $results = [];
        foreach ($statementList as $statement) {
            $results[] = StatementEntry::fromArray($statement);
        }

        return $results;
    }

    public function sweepAccountStatement(
        DateTimeInterface $toDate = null,
        DateTimeInterface $fromDate = null,
        int $perPage = 2000
    ): array {
        $options = [
            'depositOrderType' => 2,
        ];

        $statementList = $this->rawStatementAll(static::STATEMENT_SWEEP, $options, $toDate, $fromDate, $perPage);

        $results = [];
        foreach ($statementList as $statement) {
            $results[] = StatementEntry::fromArray($statement);
        }

        return $results;
    }
}