# GMO Aozora Client

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Total Downloads][ico-downloads]][link-downloads]

[![Email][ico-email]][link-email]

A PSR-18/17 based client for accessing your bank information from https://gmo-aozora.com/


## Install

Via Composer

```bash
$ composer require aurimasniekis/gmo-aozora-client
```

**This package depends on PSR-17 and PSR-18 packages to be able use this package. For simpler version please use [aurimasniekis/gmo-aozora-simple-client](https://github.com/aurimasniekis/gmo-aozora-simple-client).**

## Configuration

There are two ways to define a configuration, one is statically defining values and another is loading from JSON config file
which will be updated with authentication token and device token. Device token is required to be preserved to prevent
giving notification from a new login.

#### Static Configuration:

```php
<?php

use AurimasNiekis\GmoAozoraClient\Configuration;

$config = new Configuration(
    string $username,
    string $password,
    string $deviceToken = null,
    string $faToken = null,
    string $serviceType = self::SERVICE_TYPE, // https://bank.gmo-aozora.com/v1
    string $ssoDomain = self::SSO_DOMAIN,     // https://sso.gmo-aozora.com
    string $apiDomain = self::API_DOMAIN      // https://bank.gmo-aozora.com/v1
);
```

#### JSON Config File:

```php
<?php

use AurimasNiekis\GmoAozoraClient\JsonFileConfiguration;

$config = new JsonFileConfiguration(
    string $configFile
);
```

```json
{
    "username": "1233456",
    "password": "1233456"
}
```

**Available options:**

|Property|Type|Required|Default|Description|
|--|--|--|--|--|
|`username`|`string`|`TRUE`||Username|
|`password`|`string`|`TRUE`||Password|
|`device_token`|`string`|`FALSE`||Device token received from SSO Authentication service (Prevents getting login from new device notification)|
|`fa_token`|`string`|`FALSE`||Authentication token|
|`service_type`|`string`|`FALSE`|`https://bank.gmo-aozora.com/v1`|Service type used for SSO authentication|
|`api_domain`|`string`|`FALSE`|`https://bank.gmo-aozora.com/v1`|API domain used for API calls|
|`sso_domain`|`string`|`FALSE`|`https://sso.gmo-aozora.com`|SSO domain used for Authentication calls|


## Usage

In the example PSR-17 and PSR-18 is provided by: `nyholm/psr7` and `kriswallsmith/buzz` packages.

```php
<?php

use Buzz\Client\Curl;
use Nyholm\Psr7\Factory\Psr17Factory;
use AurimasNiekis\GmoAozoraClient\Client;
use AurimasNiekis\GmoAozoraClient\WebClient;
use AurimasNiekis\GmoAozoraClient\JsonFileConfiguration;

$factory = new Psr17Factory();
$client  = new Curl($factory);

$config    = new JsonFileConfiguration(__DIR__ . '/config.json');
$webClient = new WebClient($config, $client, $factory, $factory);
$client    = new Client($webClient);
```

### To get account details

```php
$accountDetails = $client->accountDetails();
```

```php
^ AurimasNiekis\GmoAozoraClient\Model\AccountDetails^ {#9
  -raw: array:27 [
   ...
  ]
  -customerName: null
  -customerType: null
  -lastLoginDatetime: DateTimeImmutable @1584736703 {#10
    date: 2020-03-20 20:38:23.626 UTC (+00:00)
  }
  -queryDatetime: DateTimeImmutable @1584738476 {#11
    date: 2020-03-20 21:07:56.664 UTC (+00:00)
  }
  -isLock: null
  -rankName: "１テックま君"
  -rankLogoUrl: null
  -atmFeeFreeCount: 2
  -transferFeeFreeCount: 1
  -transferLimitAmount: "500000000000"
  -oneDayTransferLimitAmount: "500000000000"
  -lastDayTotalBalance: "9999999"
  -totalBalance: "9999999"
  -ordinaryDepositTotalBalance: "9999999"
  -sweepTotalBalance: ""
  -lastDaySweepTotalBalance: ""
  -termDepositTotalBalance: ""
  -fcyOrdinaryDepositTotalJpyBalance: ""
  -uncollectedAmount: "0"
  -uncollectedDeducationBalance: null
  -branchCode: "123"
  -branchName: "..."
  -accountNumber: "1234567"
  -debitPlanList: []
  -rateList: array:8 [
    0 => array:16 [
      "customerRateId" => "1212321"
      "currency" => "USD"
      "ttb" => "110.07"
      "tts" => "110.11"
      "ttbRiseOrFall" => "1"
      "ttmRiseOrFall" => "1"
      "ttsRiseOrFall" => "1"
      "isServiceTime" => "1"
      "isValidRate" => "1"
      "currencyName" => "米ドル"
      "displayFractionLength" => 2
      "orderFractionLength" => 2
      "currencyServiceStatus" => "1"
      "validSeconds" => 10
      "generatedAt" => "20200320210755456"
      "ttm" => "110.09"
    ]
    ...
  ]
  -authorityList: []
  -statementList: array:5 [
    0 => AurimasNiekis\GmoAozoraClient\Model\PaymentStatementEntry^ {#14
      -raw: array:7 [
        ...
      ]
      -data: DateTimeImmutable @1584706076 {#13
        date: 2020-03-20 12:07:56.0 UTC (+00:00)
      }
      -remark: "Visa"
      -memo: null
      -amount: "400"
      -balance: "123456789"
      -accountEntryNumber: "123456"
      -creditDebitType: "2"
    }
    1 => AurimasNiekis\GmoAozoraClient\Model\IncomeStatementEntry^ {#14
      -raw: array:7 [
        ...
      ]
      -data: DateTimeImmutable @1584706076 {#13
        date: 2020-03-20 12:07:56.0 UTC (+00:00)
      }
      -remark: "Visa"
      -memo: null
      -amount: "123456789"
      -balance: "123456789"
      -accountEntryNumber: "123456"
      -creditDebitType: "1"
    }
    ...
  ]
}
```

### Statements

Ordinary Deposit Statements:

```php
$statements = $client->ordinaryDepositStatement(
   DateTimeInterface $toDate = null,
   DateTimeInterface $fromDate = null,
   int $perPage = 2000
)
```

Foreign Currency Ordinary Deposit Statements:

```php
$statements = $client->foreignOrdinaryDepositStatement(
   DateTimeInterface $toDate = null,
   DateTimeInterface $fromDate = null,
   int $perPage = 2000
)
```

Term Deposit Statements:

```php
$statements = $client->termDepositStatement(
   DateTimeInterface $toDate = null,
   DateTimeInterface $fromDate = null,
   int $perPage = 2000
)
```

Sweep Account Statements:

```php
$statements = $client->sweepAccountStatement(
   DateTimeInterface $toDate = null,
   DateTimeInterface $fromDate = null,
   int $perPage = 2000
)
```

Raw statement:

```php
$statements = $client->rawStatement(
    string $type,
    array $options,
    DateTimeInterface $toDate = null,
    DateTimeInterface $fromDate = null,
    int $offset = 0,
    int $limit = 20
)
```

Raw statements with all pages fetched:

```php
$statements = $client->rawStatementAll(
   string $type,
   array $options,
   DateTimeInterface $toDate = null,
   DateTimeInterface $fromDate = null,
   int $perPage = 2000
)
```

Results for these functions are the same:

```php
array:100 [
    0 => AurimasNiekis\GmoAozoraClient\Model\PaymentStatementEntry^ {#14
      -raw: array:7 [
        ...
      ]
      -data: DateTimeImmutable @1584706076 {#13
        date: 2020-03-20 12:07:56.0 UTC (+00:00)
      }
      -remark: "Visa"
      -memo: null
      -amount: "400"
      -balance: "123456789"
      -accountEntryNumber: "123456"
      -creditDebitType: "2"
    },
    ...
]
```


Visa Statements:

```php
$statements = $client->visaStatement(
   DateTimeInterface $toDate = null,
   DateTimeInterface $fromDate = null,
   int $perPage = 2000
)
```

```php
array:100 [
   0 => AurimasNiekis\GmoAozoraClient\Model\VisaStatementEntry^ {#21
    -raw: array:9 [
      ...
    ]
    -date: DateTimeImmutable @1584274771 {#22
      date: 2020-03-15 12:19:31.0 UTC (+00:00)
    }
    -usage: "ＡＭＡＺＯＮ．ＣＯ．ＪＰ"
    -amount: "1234"
    -status: "1"
    -localCurrencyAmount: "0.0"
    -atmUseFee: null
    -currency: null
    -conversionRate: null
    -approvalNumber: "12345"
  }
  ...
]
```

## Testing

Run PHP style checker

```bash
$ composer cs-check
```

Run PHP style fixer

```bash
$ composer cs-fix
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.


## License

Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/aurimasniekis/gmo-aozora-client.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/com/aurimasniekis/gmo-aozora-client/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/aurimasniekis/gmo-aozora-client.svg?style=flat-square
[ico-email]: https://img.shields.io/badge/email-aurimas@niekis.lt-blue.svg?style=flat-square

[link-travis]: https://travis-ci.org/aurimasniekis/gmo-aozora-client
[link-packagist]: https://packagist.org/packages/aurimasniekis/gmo-aozora-client
[link-downloads]: https://packagist.org/packages/aurimasniekis/gmo-aozora-client/stats
[link-email]: mailto:aurimas@niekis.lt