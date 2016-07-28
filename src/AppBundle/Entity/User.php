<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

// DON'T forget this use statement!!!
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @UniqueEntity("email")
 * @UniqueEntity("name")
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 */
class User
{
    const CONFIRMED = 1;
    const UNCONFIRMED = 0;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    private $password;

    /**
     * oauth user id
     *
     * @ORM\Column(name="open_id", type="string", nullable=true)
     */
    private $openId;

    /**
     * oauth provider
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $provider;

    //Confirmable

    /**
     * A unique random token
     *
     * @ORM\Column(name="confirmation_token", type="string", nullable=true)
     */
    private $confirmationToken;

    /**
     * @ORM\Column(name="confirmation_token_expired_at", type="datetime", nullable=true)
     */
    private $confirmationTokenExpiredAt;

    /**
     * @ORM\Column(name="confirmed", type="boolean", nullable=false, options={"default": "0", "comment": "0:未激活，1:已激活"})
     */
    private $confirmed;

    /**
     * 重置密码token
     *
     * @ORM\Column(name="reset_password_token", type="string", nullable=true)
     */
    private $resetPasswordToken;

    /**
     * @ORM\Column(name="reset_password_token_expired_at", type="datetime", nullable=true)
     */
    private $resetPasswordTokenExpiredAt;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set openId
     *
     * @param string $openId
     * @return User
     */
    public function setOpenId($openId)
    {
        $this->openId = $openId;

        return $this;
    }

    /**
     * Get openId
     *
     * @return string 
     */
    public function getOpenId()
    {
        return $this->openId;
    }

    /**
     * Set provider
     *
     * @param string $provider
     * @return User
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider
     *
     * @return string 
     */
    public function getProvider()
    {
        return $this->provider;
    }


    //////////////////////////////////////////////非getter/setter方法区
    /**
     * 激活码是否过期了
     */
    public function isConfirmationTokenExpired()
    {
        return new \DateTime() > $this->confirmationTokenExpiredAt;
    }

    /**
     * 是否已激活
     */
    public function isConfirmed() {
        return $this->confirmed == User::CONFIRMED;
    }

    public function isResetPasswordTokenExpired()
    {
        return new \DateTime() > $this->resetPasswordTokenExpiredAt;
    }

    /**
     * Gets triggered only on insert
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Gets triggered every time on update
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    ////////////////////////////////////////////////getter/setter方法区

    //php app/console doctrine:generate:entities AppBundle/Entity/User

    /**
     * Set confirmationToken
     *
     * @param string $confirmationToken
     * @return User
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * Get confirmationToken
     *
     * @return string 
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Set confirmationTokenExpiredAt
     *
     * @param \DateTime $confirmationTokenExpiredAt
     * @return User
     */
    public function setConfirmationTokenExpiredAt($confirmationTokenExpiredAt)
    {
        $this->confirmationTokenExpiredAt = $confirmationTokenExpiredAt;

        return $this;
    }

    /**
     * Get confirmationTokenExpiredAt
     *
     * @return \DateTime 
     */
    public function getConfirmationTokenExpiredAt()
    {
        return $this->confirmationTokenExpiredAt;
    }

    /**
     * Set confirmed
     *
     * @param boolean $confirmed
     * @return User
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * Get confirmed
     *
     * @return boolean 
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Set resetPasswordToken
     *
     * @param string $resetPasswordToken
     * @return User
     */
    public function setResetPasswordToken($resetPasswordToken)
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    /**
     * Get resetPasswordToken
     *
     * @return string 
     */
    public function getResetPasswordToken()
    {
        return $this->resetPasswordToken;
    }

    /**
     * Set resetPasswordTokenExpiredAt
     *
     * @param \DateTime $resetPasswordTokenExpiredAt
     * @return User
     */
    public function setResetPasswordTokenExpiredAt($resetPasswordTokenExpiredAt)
    {
        $this->resetPasswordTokenExpiredAt = $resetPasswordTokenExpiredAt;

        return $this;
    }

    /**
     * Get resetPasswordTokenExpiredAt
     *
     * @return \DateTime 
     */
    public function getResetPasswordTokenExpiredAt()
    {
        return $this->resetPasswordTokenExpiredAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
