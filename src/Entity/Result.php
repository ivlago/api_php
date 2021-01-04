<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;


/**
 * @ORM\Entity
 *
 * @Serializer\XmlNamespace(uri="http://www.w3.org/2005/Atom", prefix="atom")
 * @Serializer\AccessorOrder(
 *     "custom",
 *     custom={ "id", "result", "user", "_links" }
 *     )
 *
 * @Hateoas\Relation(
 *     name="parent",
 *     href="expr(constant('\\App\\Controller\\ApiResultsController::RUTA_API'))"
 * )
 *
 * @Hateoas\Relation(
 *     name="self",
 *     href="expr(constant('\\App\\Controller\\ApiResultsController::RUTA_API') ~ '/' ~ object.getId())"
 * )
 */

class Result {
    public const RESULT_ID_ATTR = 'id';
    public const RESULT_ATTR = 'result';
    public const USER_ATTR = 'user';
    public const TIME_ATTR = 'time';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @Serializer\XmlAttribute
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="integer")
     *
     * @Serializer\SerializedName(Result::RESULT_ATTR)
     * @Serializer\XmlElement(cdata=false)
     */
    private int $result;

    /**
     *
     *
     * @Serializer\SerializedName(Result::USER_ATTR)
     * @Serializer\XmlElement(cdata=false)
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="user")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="id", onDelete="cascade")
     *     })
     *
     */
    private User $user;

    /**
     * @ORM\Column(
     *     name= "time",
     *     type= "datetime",
     *     nullable= false)
     */
    private ?DateTime $time;

    /**
     * Result constructor.
     * @param integer $result
     * @param User $user
     * @param DateTime $time
     */
    public function __construct(int $result, User $user, DateTime $time)
    {
        $this->result = $result;
        $this->user = $user;
        $this->time = $time;
    }

    /**
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param integer|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return integer
     */
    public function getResult(): int
    {
        return $this->result;
    }

    /**
     * @param integer $result
     */
    public function setResult(int $result): void
    {
        $this->result = $result;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return DateTime|int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param DateTime|int $time
     */
    public function setTime($time): void
    {
        $this->time = $time;
    }
}
