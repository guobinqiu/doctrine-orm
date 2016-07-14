<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GroupRepository")
 * @ORM\Table(name="groups")
 * @ORM\HasLifecycleCallbacks
 */
class Group
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
     * cascade={"persist", "remove"}意味着
     * 保存Group对象的同时保存所有关联它的UserGroup对象
     * 删除Group对象的同时删除所有关联它的UserGroup对象
     *
     * @ORM\OneToMany(targetEntity="UserGroup", mappedBy="group", cascade={"persist", "remove"})
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
     * @return Customer
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
     * @return Group
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
