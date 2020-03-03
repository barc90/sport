<?php
// db_src/Tournament.php
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tournaments")
 */
class Tournament
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    /** 
     * @ORM\Column(type="string") 
     */
    protected $name;
    /** 
     * @ORM\Column(type="string", unique=true) 
     */
    protected $slug;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
    public function getSlug()
    {
        return $this->slug;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }
}
