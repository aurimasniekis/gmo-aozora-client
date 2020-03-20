<?php

namespace AurimasNiekis\GmoAozoraClient\Model;

use DateTimeImmutable;

/**
 * @package AurimasNiekis\GmoAozoraClient\Model
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class StatementEntry
{
    private array $raw;
    private DateTimeImmutable $data;
    private string $remark;
    private ?string $memo;
    private string $amount;
    private string $balance;
    private string $accountEntryNumber;
    private string $creditDebitType;

    /**
     * @param array $raw
     */
    public function __construct(array $raw)
    {
        $this->raw = $raw;
        $this->remark = $raw['remark'];
        $this->memo = $raw['statementMemo'];
        $this->amount = $raw['amount'];
        $this->balance = $raw['balance'];
        $this->accountEntryNumber = $raw['accountEntryNumber'];
        $this->creditDebitType = $raw['creditDebitType'];

        $dateTime = DateTimeImmutable::createFromFormat('Ymd', $raw['valueDate']);
        if (false !== $dateTime) {
            $this->data = $dateTime;
        }
    }

    public static function fromArray(array $statement): StatementEntry
    {
        switch ($statement['creditDebitType']) {
            case IncomeStatementEntry::CREDIT_DEBIT_TYPE:
                return new IncomeStatementEntry($statement);
            case PaymentStatementEntry::CREDIT_DEBIT_TYPE:
                return new PaymentStatementEntry($statement);
            default:
                return  new StatementEntry($statement);
        }
    }

    public function getRaw(): array
    {
        return $this->raw;
    }

    public function getData(): DateTimeImmutable
    {
        return $this->data;
    }

    public function getRemark(): string
    {
        return $this->remark;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getBalance(): string
    {
        return $this->balance;
    }

    public function getAccountEntryNumber(): string
    {
        return $this->accountEntryNumber;
    }

    public function getCreditDebitType(): string
    {
        return $this->creditDebitType;
    }
}