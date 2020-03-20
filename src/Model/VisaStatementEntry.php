<?php

namespace AurimasNiekis\GmoAozoraClient\Model;

use DateTimeImmutable;

/**
 * @package AurimasNiekis\GmoAozoraClient\Model
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class VisaStatementEntry
{
    private const DATE_FORMAT = 'Ymd';

    private array             $raw;
    private DateTimeImmutable $date;
    private string            $usage;
    private string            $amount;
    private string            $status;
    private string            $localCurrencyAmount;
    private ?string           $atmUseFee;
    private ?string           $currency;
    private ?string           $conversionRate;
    private string            $approvalNumber;

    /**
     * @param array $raw
     */
    public function __construct(array $raw)
    {
        $this->raw = $raw;

        $this->usage               = $raw['usage'];
        $this->amount              = $raw['amount'];
        $this->status              = $raw['status'];
        $this->localCurrencyAmount = $raw['localCurrencyAmount'];
        $this->atmUseFee           = $raw['atmUseFee'];
        $this->currency            = $raw['currency'];
        $this->conversionRate      = $raw['conversionRate'];
        $this->approvalNumber      = $raw['approvalNumber'];

        $dateTime = DateTimeImmutable::createFromFormat(static::DATE_FORMAT, $raw['useDate']);
        if (false !== $dateTime) {
            $this->date = $dateTime;
        }
    }

    public function getRaw(): array
    {
        return $this->raw;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getUsage(): string
    {
        return $this->usage;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getLocalCurrencyAmount(): string
    {
        return $this->localCurrencyAmount;
    }

    public function getAtmUseFee(): ?string
    {
        return $this->atmUseFee;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getConversionRate(): ?string
    {
        return $this->conversionRate;
    }

    public function getApprovalNumber(): string
    {
        return $this->approvalNumber;
    }
}