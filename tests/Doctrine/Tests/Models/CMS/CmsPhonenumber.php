<?php

namespace Doctrine\Tests\Models\CMS;

/**
 * @Entity
 * @Table(name="cms_phonenumbers")
 */
class CmsPhonenumber
{
    /**
     * @Column(type="string", length=50)
     * @Id
     */
    public $phonenumber;
    /**
     * @ManyToOne(targetEntity="CmsUser")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    public $user;

    public function setUser(CmsUser $user) {
        $this->user = $user;
        $user->addPhonenumber($this);
    }
}
