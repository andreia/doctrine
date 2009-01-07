<?php

#namespace Doctrine\Tests\Models\Forum;

#use Doctrine\ORM\Entity;

/**
 * @DoctrineEntity
 * @DoctrineInheritanceType("joined")
 * @DoctrineDiscriminatorColumn(name="dtype", type="varchar", length=20)
 * @DoctrineDiscriminatorMap({"user" = "ForumUser", "admin" = "ForumAdministrator"})
 * @DoctrineSubclasses({"ForumAdministrator"})
 */
class ForumUser
{
    /**
     * @DoctrineColumn(type="integer")
     * @DoctrineId
     * @DoctrineIdGenerator("auto")
     */
    public $id;
    /**
     * @DoctrineColumn(type="varchar", length=50)
     */
    public $username;
    /**
     * @DoctrineOneToOne(
           targetEntity="ForumAvatar",
           joinColumns={"avatar_id" = "id"},
           cascade={"save"})
     */
    public $avatar;
}