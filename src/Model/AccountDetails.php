<?php

namespace AurimasNiekis\GmoAozoraClient\Model;

use DateTime;
use DateTimeImmutable;

/**
 * @package AurimasNiekis\GmoAozoraClient\Model
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class AccountDetails
{
    private const DATE_FORMAT = 'YmdHisv';

    private array     $raw;
    private ?string   $customerName;
    private ?string   $customerType;
    private ?DateTimeImmutable $lastLoginDatetime;
    private ?DateTimeImmutable $queryDatetime;
    private           $isLock;
    private ?string   $rankName;
    private ?string   $rankLogoUrl;
    private int       $atmFeeFreeCount;
    private int       $transferFeeFreeCount;
    private string    $transferLimitAmount;
    private string    $oneDayTransferLimitAmount;
    private string    $lastDayTotalBalance;
    private string    $totalBalance;
    private string    $ordinaryDepositTotalBalance;
    private string    $sweepTotalBalance;
    private string    $lastDaySweepTotalBalance;
    private string    $termDepositTotalBalance;
    private string    $fcyOrdinaryDepositTotalJpyBalance;
    private string    $uncollectedAmount;
    private ?string   $uncollectedDeducationBalance;
    private string    $branchCode;
    private string    $branchName;
    private string    $accountNumber;
    private array     $debitPlanList;
    private array     $rateList;
    private array     $authorityList;

    /** @var StatementEntry[] */
    private array     $statementList;

    public function __construct(array $raw) {
        $this->raw = $raw;
        $this->customerName = $raw['customerName'];
        $this->customerType = $raw['customerType'];

        $dateTime = DateTimeImmutable::createFromFormat(static::DATE_FORMAT, $raw['lastLoginDatetime']);
        if (false !== $dateTime) {
            $this->lastLoginDatetime = $dateTime;
        }

        $dateTime = DateTimeImmutable::createFromFormat(static::DATE_FORMAT, $raw['queryDatetime']);
        if (false !== $dateTime) {
            $this->queryDatetime = $dateTime;
        }

        $this->isLock = $raw['isLock'];
        $this->rankName = $raw['rankName'];
        $this->rankLogoUrl = $raw['rankLogoUrl'];
        $this->atmFeeFreeCount = $raw['atmFeeFreeCount'];
        $this->transferFeeFreeCount = $raw['transferFeeFreeCount'];
        $this->transferLimitAmount = $raw['transferLimitAmount'];
        $this->oneDayTransferLimitAmount = $raw['oneDayTransferLimitAmount'];
        $this->lastDayTotalBalance = $raw['lastDayTotalBalance'];
        $this->totalBalance = $raw['totalBalance'];
        $this->ordinaryDepositTotalBalance = $raw['ordinaryDepositTotalBalance'];
        $this->sweepTotalBalance = $raw['sweepTotalBalance'];
        $this->lastDaySweepTotalBalance = $raw['lastDaySweepTotalBalance'];
        $this->termDepositTotalBalance = $raw['termDepositTotalBalance'];
        $this->fcyOrdinaryDepositTotalJpyBalance = $raw['fcyOrdinaryDepositTotalJpyBalance'];
        $this->uncollectedAmount = $raw['uncollectedAmount'];
        $this->uncollectedDeducationBalance = $raw['uncollectedDeducationBalance'];
        $this->branchCode = $raw['branchCode'];
        $this->branchName = $raw['branchName'];
        $this->accountNumber = $raw['accountNumber'];
        $this->statementList = [];

        foreach ($raw['statementList'] as $statement) {
            $this->statementList[] = StatementEntry::fromArray($statement);
        }

        $this->debitPlanList = $raw['debitPlanList'];
        $this->rateList = $raw['rateList'];
        $this->authorityList = $raw['authorityList'];
    }

    /**
     * @return array
     */
    public function getRaw(): array
    {
        return $this->raw;
    }

    /**
     * @return mixed|string|null
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * @return mixed|string|null
     */
    public function getCustomerType()
    {
        return $this->customerType;
    }

    /**
     * @return DateTime|mixed|null
     */
    public function getLastLoginDatetime()
    {
        return $this->lastLoginDatetime;
    }

    /**
     * @return DateTime|mixed|null
     */
    public function getQueryDatetime()
    {
        return $this->queryDatetime;
    }

    /**
     * @return mixed
     */
    public function getIsLock()
    {
        return $this->isLock;
    }

    /**
     * @return mixed|string|null
     */
    public function getRankName()
    {
        return $this->rankName;
    }

    /**
     * @return mixed|string|null
     */
    public function getRankLogoUrl()
    {
        return $this->rankLogoUrl;
    }

    /**
     * @return int|mixed
     */
    public function getAtmFeeFreeCount()
    {
        return $this->atmFeeFreeCount;
    }

    /**
     * @return int|mixed
     */
    public function getTransferFeeFreeCount()
    {
        return $this->transferFeeFreeCount;
    }

    /**
     * @return mixed|string
     */
    public function getTransferLimitAmount()
    {
        return $this->transferLimitAmount;
    }

    /**
     * @return mixed|string
     */
    public function getOneDayTransferLimitAmount()
    {
        return $this->oneDayTransferLimitAmount;
    }

    /**
     * @return mixed|string
     */
    public function getLastDayTotalBalance()
    {
        return $this->lastDayTotalBalance;
    }

    /**
     * @return mixed|string
     */
    public function getTotalBalance()
    {
        return $this->totalBalance;
    }

    /**
     * @return mixed|string
     */
    public function getOrdinaryDepositTotalBalance()
    {
        return $this->ordinaryDepositTotalBalance;
    }

    /**
     * @return mixed|string
     */
    public function getSweepTotalBalance()
    {
        return $this->sweepTotalBalance;
    }

    /**
     * @return mixed|string
     */
    public function getLastDaySweepTotalBalance()
    {
        return $this->lastDaySweepTotalBalance;
    }

    /**
     * @return mixed|string
     */
    public function getTermDepositTotalBalance()
    {
        return $this->termDepositTotalBalance;
    }

    /**
     * @return mixed|string
     */
    public function getFcyOrdinaryDepositTotalJpyBalance()
    {
        return $this->fcyOrdinaryDepositTotalJpyBalance;
    }

    /**
     * @return mixed|string
     */
    public function getUncollectedAmount()
    {
        return $this->uncollectedAmount;
    }

    /**
     * @return mixed|string|null
     */
    public function getUncollectedDeducationBalance()
    {
        return $this->uncollectedDeducationBalance;
    }

    /**
     * @return mixed|string
     */
    public function getBranchCode()
    {
        return $this->branchCode;
    }

    /**
     * @return mixed|string
     */
    public function getBranchName()
    {
        return $this->branchName;
    }

    /**
     * @return mixed|string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @return array|mixed
     */
    public function getStatementList()
    {
        return $this->statementList;
    }

    /**
     * @return array|mixed
     */
    public function getDebitPlanList()
    {
        return $this->debitPlanList;
    }

    /**
     * @return array|mixed
     */
    public function getRateList()
    {
        return $this->rateList;
    }

    /**
     * @return array|mixed
     */
    public function getAuthorityList()
    {
        return $this->authorityList;
    }
}