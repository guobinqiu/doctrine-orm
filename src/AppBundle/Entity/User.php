<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
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
     * @ORM\Column(type="string", nullable=false)
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=false, length=64)
     */
    private $password;

    /**
     * cascade="remove"代表删除User对象的同时删除关联的UserProfile对象
     *
     * @ORM\OneToOne(targetEntity="UserProfile", mappedBy="user", cascade="remove")
     */
    private $userProfile;

    /**
     * cascade={"persist", "remove"}意味着
     * 保存User对象的同时保存所有关联它的UserGroup对象
     * 删除User对象的同时删除所有关联它的UserGroup对象
     *
     * @ORM\OneToMany(targetEntity="UserGroup", mappedBy="user", cascade={"persist", "remove"})
     */
    private $userGroups;

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
     * Set userProfile
     *
     * @param \AppBundle\Entity\UserProfile $userProfile
     * @return User
     */
    public function setUserProfile(\AppBundle\Entity\UserProfile $userProfile)
    {
        $this->userProfile = $userProfile;

        return $this;
    }

    /**
     * Get userProfile
     *
     * @return \AppBundle\Entity\UserProfile 
     */
    public function getUserProfile()
    {
        return $this->userProfile;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userGroups = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add userGroups
     *
     * @param \AppBundle\Entity\UserGroup $userGroups
     * @return User
     */
    public function addUserGroup(\AppBundle\Entity\UserGroup $userGroups)
    {
        $this->userGroups[] = $userGroups;

        return $this;
    }

    /**
     * Remove userGroups
     *
     * @param \AppBundle\Entity\UserGroup $userGroups
     */
    public function removeUserGroup(\AppBundle\Entity\UserGroup $userGroups)
    {
        $this->userGroups->removeElement($userGroups);
    }

    /**
     * Get userGroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserGroups()
    {
        return $this->userGroups;
    }


}
