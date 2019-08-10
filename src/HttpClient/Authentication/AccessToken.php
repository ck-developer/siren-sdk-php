<?php

namespace Siren\HttpClient\Authentication;

class AccessToken
{
    /**
     * @var string
     */
    private $token;
    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $expireIn;
    /**
     * @var \DateTimeInterface
     */
    private $createdAt;

    /**
     * @return self
     */
    public static function fromArray(array $data)
    {
        return new self(
            $data['access_token'],
            $data['token_type'],
            $data['expires_in']
        );
    }

    public function __construct(string $token, string $type, int $expireIn, \DateTimeInterface $createdAt = null)
    {
        $this->token = $token;
        $this->type = $type;
        $this->expireIn = $expireIn;
        $this->createdAt = $createdAt ?? new \DateTime();
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getExpireIn()
    {
        return $this->expireIn;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getExpireAt(): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())
            ->setTimestamp($this->getCreatedAt()->getTimestamp() + $this->getExpireIn())
        ;
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        return $this->getExpireAt() > new \DateTime();
    }
}
