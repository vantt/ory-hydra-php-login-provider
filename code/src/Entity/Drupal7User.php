<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use stdClass;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Drupal7User
 *
 * This entity object is loaded by Symfony-Doctrine when doing authentication
 * and being kept (a simple version) in the Session after a successful-authentication
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})}, indexes={@ORM\Index(name="mail", columns={"mail"}), @ORM\Index(name="created", columns={"created"}), @ORM\Index(name="access", columns={"access"}), @ORM\Index(name="picture", columns={"picture"})})
 */
class Drupal7User implements UserInterface, \Serializable {
    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="integer", nullable=false, options={"unsigned"=true,"comment"="Primary Key: Unique user ID."})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $uid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=60, nullable=false)
     */
    public $name = '';

    /**
     * @var string
     *
     * @ORM\Column(name="pass", type="string", length=128, nullable=false)
     */
    public $pass = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="mail", type="string", length=254, nullable=true)
     */
    public $mail = '';

    /**
     * @var string
     *
     * @ORM\Column(name="theme", type="string", length=255, nullable=false)
     */
    public $theme = '';

    /**
     * @var string
     *
     * @ORM\Column(name="signature", type="string", length=255, nullable=false)
     */
    public $signature = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="signature_format", type="string", length=255, nullable=true)
     */
    public $signatureFormat;

    /**
     * @var int
     *
     * @ORM\Column(name="created", type="integer", nullable=false, options={"comment"="Timestamp for when user was created."})
     */
    public $created = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="access", type="integer", nullable=false, options={"comment"="Timestamp for previous time user accessed the site."})
     */
    public $access = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="login", type="integer", nullable=false, options={"comment"="Timestamp for user’s last login."})
     */
    public $login = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=false, options={"comment"="Whether the user is active(1) or blocked(0)."})
     */
    public $status = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="timezone", type="string", length=32, nullable=true)
     */
    public $timezone;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=12, nullable=false)
     */
    public $language = '';

    /**
     * @var int
     *
     * @ORM\Column(name="picture", type="integer", nullable=false, options={"comment"="Foreign key: file_managed.fid of user’s picture."})
     */
    public $picture = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="init", type="string", length=254, nullable=true)
     */
    public $init = '';

    /**
     * @var array
     *
     * @ORM\Column(name="data", type="array", length=0, nullable=true, options={"comment"="A serialized array of name value pairs that are related to the user. Any form values posted during user edit are stored and are loaded into the $user object during user_load(). Use of this field is discouraged and it will likely disappear in a future..."})
     */
    public $data = [];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Drupal7Role", fetch="EAGER")
     * @ORM\JoinTable(
     *     name="users_roles",
     *     joinColumns={
     *         @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="rid", referencedColumnName="rid")
     *     }
     * )
     * @var Collection of DrupalRole
     */
    public $_roles = [];

    public  $roles          = [];
    public  $symfonyRoles   = [];
    private $isRolesRebuilt = false;

    public function __construct() {
        $this->_roles = new ArrayCollection();
    }

    /**
     * Symfony will call this method to get list of User's Roles
     *
     * We have to convert drupal-roles-list to symfony list
     *
     * @return array
     */
    final public function getRoles(): array {
        $this->buildRoles();

        return $this->symfonyRoles;
    }

    final public function getPassword(): string {
        return $this->pass;
    }

    /**
     * Covert this Doctrine-Entity object to a Global-User-StdClass object
     *
     * @return stdClass
     */
    final public function toDrupalUser(): stdClass {
        $this->buildRoles();

        $user = (object)(array)$this;
        unset($user->_roles, $user->symfonyRoles, $user->isRolesRebuilt);

        return $user;
    }

    private function buildRoles(): void {
        if (!$this->isRolesRebuilt) {
            // build drupal Roles
            $drupalRoles  = [];
            $symfonyRoles = ['ROLE_ALLOWED_TO_SWITCH'];

            foreach ($this->_roles as $role) {
                // build Drupal Roles
                $drupalRoles[$role->getRid()] = $role->getName();

                // build Symfony Roles
                $symfonyRoles[] = 'ROLE_DRUPAL_' . strtoupper(str_replace(' ', '_', $role->getName()));
            }

            $this->roles          = $drupalRoles;
            $this->symfonyRoles   = $symfonyRoles;
            $this->isRolesRebuilt = true;
        }
    }

    final public function getSalt(): ?string {
        return null;
    }

    final public function getUsername(): string {
        return $this->name;
    }

    final public function eraseCredentials(): void {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or &null;
     */
    final public function serialize() {
        $values = [];

        foreach (['uid', 'name', 'pass', 'symfonyRoles'] as $name) {
            $values[$name] = $this->$name;
        }

        return serialize($values);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @return mixed the original value unserialized.
     */
    public function unserialize($serialized) {
        $values = unserialize($serialized);

        foreach ($values as $name => $value) {
            $this->$name = $value;
        }

        return $values;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data): self {
        $this->data = $data;

        return $this;
    }
}
