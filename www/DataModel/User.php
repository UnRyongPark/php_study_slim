<?php

namespace DataModel;

use Respect\Validation\Rules\Key;
use Respect\Validation\Validator as v;

Class User
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $nickname;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $cellphone;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string|null
     */
    private $gender;

    /**
     * @var int
     */
    private $signup_date;

    /**
     * @var array
     */
    private const reqired = [
        'name',
        'nickname',
        'password',
        'cellphone',
        'email',
    ];

    /**
     * @var array
     */
    private const optional = [
        'gender',
    ];

    /**
     * @var array
     */
    private const doNotString = [
        'required',
        'optional',
        'doNotString',
        'password',
    ];

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->$name);
    }

    /**
     * @return array
     */
    public function getOutputData(): array
    {
        $vars = get_object_vars($this);
        return array_diff_key($vars, array_flip(self::doNotString));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)json_encode($this->getOutputData());
    }

    /**
     * @param array $array
     * @return User
     */
    public function fromArray(array $array): User
    {
        foreach ($array as $k => $v) {
            if (is_numeric($k) === false) {
                $this->$k = $v;
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function insertValidation(): bool
    {
        $validator = v::attribute('name', v::stringType()->length(1, 20)->notOptional()->notBlank()->noWhitespace())
            ->attribute('password', v::stringType()->length(10, 50)->notOptional()->notBlank()->noWhitespace())
            ->attribute('nickname', v::stringType()->length(1, 30)->notOptional()->notBlank()->noWhitespace())
            ->attribute('cellphone', v::phone()->notOptional()->notBlank()->noWhitespace())
            ->attribute('email', v::email()->length(5, 20)->notOptional()->notBlank()->noWhitespace())
            ->attribute('gender', v::optional(v::in(['F', 'M'])->notBlank()->noWhitespace()));

        return $validator->assert($this);
    }

    /**
     * @return bool
     */
    public function signInValidation(): bool
    {
        $validator = v::attribute('password',
            v::stringType()->length(10, 50)->notOptional()->notBlank()->noWhitespace())
            ->attribute('email', v::email()->length(5, 20)->notOptional()->notBlank()->noWhitespace());

        return $validator->assert($this);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNickname(): string
    {
        return $this->nickname;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getCellphone(): string
    {
        return $this->cellphone;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @return int
     */
    public function getSignupDate(): int
    {
        return $this->signup_date;
    }
}