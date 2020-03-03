<?php
// db_src/News.php

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="news")
 */
class News
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
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

    /**
     * Text of News 
     * @ORM\Column(type="text") 
     */
    protected $text;

     /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $created;

    /**
     * One News has One Category.
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function setText($text)
    {
        $this->text = $text;
    }
    public function setCreated(DateTime $created)
    {
        $this->created = $created;
    }

    public function setCategory(Category $category)
    {
        $this->category = $category;
    }
}
