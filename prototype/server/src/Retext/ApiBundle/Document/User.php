<?php

namespace Retext\ApiBundle\Document;

use Retext\ApiBundle\Exception\ValidationException;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ORM\Mapping as Doctrine;
use JMS\SerializerBundle\Annotation as SerializerBundle;

/**
 * @MongoDB\Document
 * @Doctrine\HasLifecycleCallbacks
 */
class User
{
    /**
     * @MongoDB\Id
     * @var $id
     */
    protected $id;

    /**
     * @MongoDB\String
     * @MongoDB\Index(unique=true, order="asc")
     */
    protected $email;

    /**
     * @MongoDB\String
     * @SerializerBundle\Exclude
     */
    protected $password;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        if (strstr($password, '$2a$12$')) {
            $hash = $password;
        } else {
            $hash = $this->hashPassword($password);
        }
        $this->password = $hash;
    }

    /**
     * Hash a password
     *
     * @param $password
     * @param $salt
     * @return string
     */
    public function hashPassword($password, $salt = null)
    {
        if ($salt == null) {
            $urand = fopen('/dev/urandom', 'r');
            $randstring = fread($urand, 1024);
            fclose($urand);
            $salt = '$2a$12$' . substr(str_replace('+', '.', base64_encode(sha1($randstring . microtime(), true))), 0, 22);
        }
        return crypt($password, $salt);
    }

    /**
     * Get password
     *
     * @return string $password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @MongoDB\PrePersist
     * @MongoDB\PreUpdate
     */
    public function validate()
    {
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) throw new ValidationException('email', 'invalid_format');
        if (strlen($this->password) < 8) throw new ValidationException('password', 'too_short');
    }
}
