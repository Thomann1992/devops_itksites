<?php

namespace App\Entity;

use App\Repository\InstallationRepository;
use App\Types\FrameworkTypes;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstallationRepository::class)]
#[ORM\UniqueConstraint(name: 'server_rootdir_idx', fields: ['server', 'rootDir'])]
class Installation extends AbstractHandlerResult
{
    #[ORM\OneToMany(mappedBy: 'installation', targetEntity: Site::class)]
    private Collection $sites;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $type = 'unknown';

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $phpVersion = 'unknown';

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $composerVersion = 'unknown';

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $frameworkVersion = FrameworkTypes::UNKNOWN;

    #[ORM\Column(type: 'boolean')]
    private bool $lts = false;

    #[ORM\Column(type: 'string', length: 30)]
    private string $eof = '';

    #[ORM\ManyToMany(targetEntity: PackageVersion::class, mappedBy: 'installations')]
    private Collection $packageVersions;

    public function __construct()
    {
        $this->sites = new ArrayCollection();
        $this->packageVersions = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getServer().$this->getRootDir();
    }

    /**
     * @return Collection<int, Site>
     */
    public function getSites(): Collection
    {
        return $this->sites;
    }

    public function addSite(Site $site): self
    {
        if (!$this->sites->contains($site)) {
            $this->sites[] = $site;
            $site->setInstallation($this);
        }

        return $this;
    }

    public function removeSite(Site $site): self
    {
        if ($this->sites->removeElement($site)) {
            // set the owning side to null (unless already changed)
            if ($site->getInstallation() === $this) {
                $site->setInstallation(null);
            }
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPhpVersion(): ?string
    {
        return $this->phpVersion;
    }

    public function setPhpVersion(?string $phpVersion): self
    {
        $this->phpVersion = $phpVersion;

        return $this;
    }

    public function getComposerVersion(): ?string
    {
        return $this->composerVersion;
    }

    public function setComposerVersion(?string $composerVersion): self
    {
        $this->composerVersion = $composerVersion;

        return $this;
    }

    public function getFrameworkVersion(): ?string
    {
        return $this->frameworkVersion;
    }

    public function setFrameworkVersion(?string $frameworkVersion): self
    {
        $this->frameworkVersion = $frameworkVersion;

        return $this;
    }

    public function getDomain(): ?string
    {
        if ($this->sites->count() > 0) {
            return $this->sites->first()->getPrimaryDomain();
        }

        return null;
    }

    public function isLts(): ?bool
    {
        return $this->lts;
    }

    public function setLts(bool $lts): self
    {
        $this->lts = $lts;

        return $this;
    }

    public function getEof(): ?string
    {
        return $this->eof;
    }

    public function setEof(string $eof): self
    {
        $this->eof = $eof;

        return $this;
    }

    /**
     * @return Collection<int, PackageVersion>
     */
    public function getPackageVersions(): Collection
    {
        return $this->packageVersions;
    }

    public function setPackageVersions(Collection $newPackageVersions): self
    {
        foreach ($this->packageVersions as $packageVersion) {
            if (!$newPackageVersions->contains($packageVersion)) {
                $packageVersion->removeInstallation($packageVersion);
                $this->packageVersions->removeElement($packageVersion);
            }
        }

        foreach ($newPackageVersions as $newPackageVersion) {
            $this->addPackageVersion($newPackageVersion);
        }

        return $this;
    }

    public function addPackageVersion(PackageVersion $packageVersion): self
    {
        if (!$this->packageVersions->contains($packageVersion)) {
            $this->packageVersions[] = $packageVersion;
            $packageVersion->addInstallation($this);
        }

        return $this;
    }

    public function removePackageVersion(PackageVersion $packageVersion): self
    {
        if ($this->packageVersions->removeElement($packageVersion)) {
            $packageVersion->removeInstallation($this);
        }

        return $this;
    }
}
