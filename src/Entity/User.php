<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"}, message="username_exists")
 * @UniqueEntity(fields={"email"}, message="email_exists")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 * @method string getUserIdentifier()
 */
class User implements UserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=128, unique=true)
     * @Assert\Email()
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Assert\Type(type="alnum")
     * @Assert\Length(min=4,max=30)
     */
    private string $username;

    /**
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [self::ROLE_USER];

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isVerified = false;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private ?string $avatarName = null;

    /**
     * @Assert\Image(
     *     maxSize = "1M",
     *     mimeTypes = {"image/jpeg", "image/png"},
     *     minWidth = 128, maxWidth = 800,
     *     minHeight = 128, maxHeight = 800,
     *     minWidthMessage="Votre photo doit faire minimum 128px de largeur.",
     *     minHeightMessage="Votre photo doit faire minimum 128px de hauteur.",
     *     maxSizeMessage = "The maxmimum allowed file size is 1MB.",
     *     mimeTypesMessage = "Only the filetypes image are allowed."
     * )
     * @Vich\UploadableField(mapping="user_avatar", fileNameProperty="avatarName")
     */
    private ?File $avatarFile = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $allowMemberContact = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $allowPostNotification = true;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private string $facebookId;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private string $remoteAddr;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    public function setAvatarName(?string $avatarName): self
    {
        $this->avatarName = $avatarName;

        return $this;
    }

    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    public function setAvatarFile(?File $avatarFile = null): self
    {
        $this->avatarFile = $avatarFile;

        if (null !== $avatarFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }

        return $this;
    }

    public function getAllowMemberContact(): ?bool
    {
        return $this->allowMemberContact;
    }

    public function setAllowMemberContact(bool $allowMemberContact): self
    {
        $this->allowMemberContact = $allowMemberContact;

        return $this;
    }

    public function getAllowPostNotification(): ?bool
    {
        return $this->allowPostNotification;
    }

    public function setAllowPostNotification(bool $allowPostNotification): self
    {
        $this->allowPostNotification = $allowPostNotification;

        return $this;
    }

    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    public function setFacebookId(string $facebookId): self
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    public function getRemoteAddr(): ?string
    {
        return $this->remoteAddr;
    }

    public function setRemoteAddr(string $remoteAddr): self
    {
        $this->remoteAddr = $remoteAddr;

        return $this;
    }

    public function __call(string $name, array $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }
}
