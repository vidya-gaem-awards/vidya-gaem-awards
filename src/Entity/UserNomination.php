<?php
namespace App\Entity;

use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_nominations")
 * @ORM\Entity
 */
class UserNomination
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=45, nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="nomination", type="string", length=255, nullable=false)
     */
    private $nomination;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var Award
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Award", inversedBy="userNominations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="awardID", referencedColumnName="id")
     * })
     */
    private $award;

    /**
     * @param Award $award
     * @param User|UserInterface $user
     * @param string $nomination
     */
    public function __construct(Award $award, User $user, string $nomination)
    {
        $this->award = $award;
        $this->user = $user->getFuzzyID();
        $this->nomination = $nomination;
        $this->timestamp = new DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return UserNomination
     */
    public function setUser($user): UserNomination
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * Set nomination
     *
     * @param string $nomination
     *
     * @return UserNomination
     */
    public function setNomination($nomination): UserNomination
    {
        $this->nomination = $nomination;

        return $this;
    }

    /**
     * Get nomination
     *
     * @return string
     */
    public function getNomination(): string
    {
        return $this->nomination;
    }

    /**
     * Set timestamp
     *
     * @param DateTime $timestamp
     *
     * @return UserNomination
     */
    public function setTimestamp($timestamp): UserNomination
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return DateTime
     */
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    /**
     * Set award
     *
     * @param Award $award
     *
     * @return UserNomination
     */
    public function setAward(Award $award = null): UserNomination
    {
        $this->award = $award;

        return $this;
    }

    /**
     * Get award
     *
     * @return Award
     */
    public function getAward(): Award
    {
        return $this->award;
    }
}

