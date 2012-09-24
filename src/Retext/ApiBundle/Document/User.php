<?php

namespace Retext\ApiBundle\Document;

use Retext\ApiBundle\Exception\ValidationException;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ORM\Mapping as Doctrine;
use JMS\SerializerBundle\Annotation as SerializerBundle;

/**
 * Repräsentiert einen Benutzer der Anwendung.
 *
 * @author Markus Tacker <m@tckr.cc>
 *
 * @MongoDB\Document
 * @Doctrine\HasLifecycleCallbacks
 */
class User extends \Retext\ApiBundle\Model\Base
{
    /**
     * @MongoDB\Id
     * @var $id
     */
    protected $id;

    /**
     * @MongoDB\String
     * @MongoDB\UniqueIndex(order="asc")
     */
    protected $email;

    /**
     * @MongoDB\String
     * @SerializerBundle\Exclude
     */
    protected $password;

    /**
     * @MongoDB\String
     * @SerializerBundle\Exclude
     */
    protected $code;

    /**
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
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

    /**
     * Gibt die Namen der verknüpften Dokumente zurück
     *
     * @return \Retext\ApiBundle\Model\DocumentRelation[]|null
     */
    function getRelatedDocuments()
    {
        return null;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
