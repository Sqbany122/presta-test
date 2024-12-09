<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

// namespace PrestaShop\Module\Rediconpaypo\Payload;

class CustomerPayload
{
    private $name;

    private $surname;

    private $email;

    private $phone = null;

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $value      = mb_strtoupper(trim($name), 'UTF-8');
        $this->name = $value ? (string) $value : $name;

        return $this;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function setSurname(string $surname)
    {
        $value         = mb_strtoupper(trim($surname), 'UTF-8');
        $this->surname = $value ? (string) $value : $surname;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = mb_strtolower(trim($email), 'UTF-8');

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone(string $phone)
    {
        $this->phone = $phone;

        return $this;
    }
}
