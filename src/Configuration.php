<?php

namespace AurimasNiekis\GmoAozoraClient;

/**
 * @package AurimasNiekis\GmoAozoraClient
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class Configuration
{
    public const SSO_DOMAIN   = 'https://sso.gmo-aozora.com';
    public const API_DOMAIN   = 'https://bank.gmo-aozora.com/v1';
    public const SERVICE_TYPE = 'https://bank.gmo-aozora.com/v1';

    private string  $username;
    private string  $password;
    private ?string $deviceToken;
    private ?string $faToken;
    private string  $serviceType;
    private string  $ssoDomain;
    private string  $apiDomain;

    /**
     * @param string      $username
     * @param string      $password
     * @param string|null $deviceToken
     * @param string|null $faToken
     * @param string      $serviceType
     * @param string      $ssoDomain
     * @param string      $apiDomain
     */
    public function __construct(
        string $username,
        string $password,
        string $deviceToken = null,
        string $faToken = null,
        string $serviceType = self::SERVICE_TYPE,
        string $ssoDomain = self::SSO_DOMAIN,
        string $apiDomain = self::API_DOMAIN
    ) {
        $this->username    = $username;
        $this->password    = $password;
        $this->deviceToken = $deviceToken;
        $this->faToken     = $faToken;
        $this->serviceType = $serviceType;
        $this->ssoDomain   = $ssoDomain;
        $this->apiDomain   = $apiDomain;
    }

    /**
     * @return string
     */
    public function getSsoDomain(): string
    {
        return $this->ssoDomain;
    }

    /**
     * @return string
     */
    public function getApiDomain(): string
    {
        return $this->apiDomain;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getFaToken(): ?string
    {
        return $this->faToken;
    }

    /**
     * @param string|null $faToken
     *
     * @return Configuration
     */
    public function setFaToken(?string $faToken): self
    {
        $this->faToken = $faToken;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDeviceToken(): ?string
    {
        return $this->deviceToken;
    }

    /**
     * @param string|null $deviceToken
     *
     * @return Configuration
     */
    public function setDeviceToken(?string $deviceToken): self
    {
        $this->deviceToken = $deviceToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getServiceType(): string
    {
        return $this->serviceType;
    }
}