<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 */
class User
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
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
     * A timestamp when the confirmation_token was generated (not sent)
     *
     * @ORM\Column(name="confirmation_sent_at", type="datetime", nullable=true)
     */
    private $confirmationSentAt;

    /**
     * A timestamp when the user clicked the confirmation link
     *
     * @ORM\Column(name="confirmed_at", type="datetime", nullable=true)
     */
    private $confirmedAt;

    //Recoverable

    /**
     * 重置密码token
     *
     * @ORM\Column(name="reset_password_token", type="string", nullable=true)
     */
    private $resetPasswordToken;

    /**
     * 什么时候重置的
     *
     * @ORM\Column(name="reset_password_sent_at", type="datetime", nullable=true)
     */
    private $resetPasswordSentAt;

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
        return new \DateTime() > $this->confirmationSentAt->modify('+ 24 hour');
    }

    /**
     * 是否已激活
     */
    public function isConfirmed() {
        return $this->confirmedAt != null;
    }

    public function isResetPasswordTokenExpired()
    {
        return new \DateTime() > $this->resetPasswordSentAt->modify('+ 24 hour');
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

    ////////////////////////////////////////////////

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
     * Set confirmationSentAt
     *
     * @param \DateTime $confirmationSentAt
     * @return User
     */
    public function setConfirmationSentAt($confirmationSentAt)
    {
        $this->confirmationSentAt = $confirmationSentAt;

        return $this;
    }

    /**
     * Get confirmationSentAt
     *
     * @return \DateTime 
     */
    public function getConfirmationSentAt()
    {
        return $this->confirmationSentAt;
    }

    /**
     * Set confirmedAt
     *
     * @param \DateTime $confirmedAt
     * @return User
     */
    public function setConfirmedAt($confirmedAt)
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
    }

    /**
     * Get confirmedAt
     *
     * @return \DateTime 
     */
    public function getConfirmedAt()
    {
        return $this->confirmedAt;
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
     * Set resetPasswordSentAt
     *
     * @param \DateTime $resetPasswordSentAt
     * @return User
     */
    public function setResetPasswordSentAt($resetPasswordSentAt)
    {
        $this->resetPasswordSentAt = $resetPasswordSentAt;

        return $this;
    }

    /**
     * Get resetPasswordSentAt
     *
     * @return \DateTime 
     */
    public function getResetPasswordSentAt()
    {
        return $this->resetPasswordSentAt;
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
