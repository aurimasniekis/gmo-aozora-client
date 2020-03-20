<?php

namespace AurimasNiekis\GmoAozoraClient;


use InvalidArgumentException;

/**
 * @package AurimasNiekis\GmoAozoraClient
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class JsonFileConfiguration extends Configuration
{
    private string $configFile;
    private array  $raw;

    public function __construct(
        string $configFile
    ) {
        $this->configFile = $configFile;

        if (false === file_exists($configFile)) {
            throw new InvalidArgumentException('File "' . $configFile . '" does not exist!');
        }

        $this->raw = json_decode(file_get_contents($configFile), true, 2, JSON_THROW_ON_ERROR);

        if (false === isset($this->raw['username'])) {
            throw new InvalidArgumentException('"username" property is missing from config file');
        }

        if (false === isset($this->raw['password'])) {
            throw new InvalidArgumentException('"password" property is missing from config file');
        }

        $username    = $this->raw['username'];
        $password    = $this->raw['password'];
        $deviceToken = $this->raw['device_token'] ?? null;
        $faToken     = $this->raw['fa_token'] ?? null;
        $serviceType = $this->raw['service_type'] ?? static::SERVICE_TYPE;
        $ssoDomain   = $this->raw['sso_domain'] ?? static::SSO_DOMAIN;
        $apiDomain   = $this->raw['api_domain'] ?? static::API_DOMAIN;

        parent::__construct($username, $password, $deviceToken, $faToken, $serviceType, $ssoDomain, $apiDomain);
    }

    public function setFaToken(?string $faToken): Configuration
    {
        parent::setFaToken($faToken);

        $this->raw['fa_token'] = $faToken;

        $this->saveConfig();

        return $this;
    }

    public function saveConfig(): void
    {
        $json = json_encode($this->raw, JSON_PRETTY_PRINT);

        file_put_contents($this->configFile, $json);
    }

    public function setDeviceToken(?string $deviceToken): Configuration
    {
        parent::setDeviceToken($deviceToken);

        $this->raw['device_token'] = $deviceToken;

        $this->saveConfig();

        return $this;
    }

}