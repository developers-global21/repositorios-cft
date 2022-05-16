<?php

namespace App\Entity;

use App\Repository\SubprocesoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubprocesoRepository::class)
 */
class Subproceso
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Categoria::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $categoria;

    /**
     * @ORM\ManyToOne(targetEntity=Subcategoria::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $subcategoria;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $directorio;

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

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDirectorio(): ?string
    {
        return $this->directorio;
    }

    public function setDirectorio(string $directorio): self
    {
        $this->directorio = $directorio;

        return $this;
    }
    // Registra el método mágico para imprimir el nombre del estado, por ejemplo, California
    public function __toString()
    {
        return $this->nombre;
    }
}
