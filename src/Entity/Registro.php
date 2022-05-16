<?php

namespace App\Entity;

use App\Repository\RegistroRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RegistroRepository::class)
 */
class Registro
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Categoria::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $categoria;

    /**
     * @ORM\ManyToOne(targetEntity=Subcategoria::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $subcategoria;

    /**
     * @ORM\ManyToOne(targetEntity=Subproceso::class)
     * @ORM\JoinColumn(nullable=true)   
     */
    private $subproceso;

    /**
     * @ORM\ManyToOne(targetEntity=Periodo::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $periodo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getSubcategoria(): ?Subcategoria
    {
        return $this->subcategoria;
    }

    public function setSubcategoria(?Subcategoria $subcategoria): self
    {
        $this->subcategoria = $subcategoria;

        return $this;
    }

    public function getSubproceso(): ?Subproceso
    {
        return $this->subproceso;
    }

    public function setSubproceso(?Subproceso $subproceso): self
    {
        $this->subproceso = $subproceso;

        return $this;
    }

    public function getPeriodo(): ?Periodo
    {
        return $this->periodo;
    }

    public function setPeriodo(?Periodo $periodo): self
    {
        $this->periodo = $periodo;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
