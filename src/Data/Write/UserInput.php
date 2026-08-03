<?php

namespace CodebarAg\DocuWare\Data\Write;

use CodebarAg\DocuWare\DTO\General\UserManagement\CreateUpdateUser\User as UserRequestDto;

/**
 * Input for creating a user. Maps to the DocuWare user-create payload.
 */
final class UserInput
{
    public function __construct(
        public string $name,
        public string $dbName,
        public string $email,
        public string $password,
        public ?string $networkId = null,
    ) {}

    public static function make(
        string $name,
        string $dbName,
        string $email,
        string $password,
        ?string $networkId = null,
    ): self {
        return new self($name, $dbName, $email, $password, $networkId);
    }

    public function toRequestDto(): UserRequestDto
    {
        return new UserRequestDto(
            name: $this->name,
            dbName: $this->dbName,
            email: $this->email,
            password: $this->password,
            networkId: $this->networkId,
        );
    }
}
