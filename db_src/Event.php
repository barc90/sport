<?php
// db_src/Event.php
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="EventRepository")
 * @ORM\Table(name="events")
 */
class Event
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue
     */
    protected $id;
    /** 
     * @ORM\Column(type="string", length=255, unique=true) 
     */
    protected $slug;
    /** 
     * @ORM\Column(type="string", length=255) 
     */
    protected $name;
    /**
     * Text of Event 
     * @ORM\Column(type="text", nullable=true) 
     */
    protected $text;

    /** 
     * @ORM\Column(type="string", length=255, nullable=true) 
     */
    protected $command_a;

    /** 
     * @ORM\Column(type="string", length=255, nullable=true) 
     */
    protected $command_b;

    /** 
     * @ORM\Column(type="string", length=255, nullable=true) 
     */
    protected $command_a_ru;

    /** 
     * @ORM\Column(type="string", length=255, nullable=true) 
     */
    protected $command_b_ru;

    /**
     * Current broadcasts
     * @ORM\Column(type="text", nullable=true) 
     */
    protected $broadcasts;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $created;

    /** 
     * @ORM\Column(type="string", length=255, nullable=true) 
     */
    protected $source_url;

    /** 
     * @ORM\Column(type="boolean", nullable=true, options={"unsigned":true, "default":0}) 
     */
    protected $is_online;

    /** 
     * @ORM\Column(type="string", length=10, nullable=true) 
     */
    protected $score;

    /**
     * Datetime of start Event
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $start;
    /**
     * One Events has One Category.
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * League champions
     * @ORM\ManyToOne(targetEntity="Tournament")
     * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id", nullable=true) 
     */
    protected $tournament;

    public function setIsOnline($is_online) {
        $this->is_online = $is_online;
    }
    public function setName($name) {
        $this->name = $name;
    }
    public function setText($text) {
        $this->text = json_encode($text);
    }
    public function setBroadcasts($broadcasts) {
        $this->broadcasts = json_encode($broadcasts);
    }
    public function setSlug($slug) {
        $this->slug = $slug;
    }
    public function setCreated(DateTime $created) {
        $this->created = $created;
    }
    public function setStart(DateTime $start) {
        $this->start = $start;
    }
    public function setCategory(Category $category) {
        $this->category = $category;
    }
    public function setTournament(Tournament $tournament) {
        $this->tournament = $tournament;
    }
    public function setSourceUrl($source_url) {
        $this->source_url = $source_url;
    }
    public function setCommandA($command_a) {
        $this->command_a = $command_a;
    }
    public function setCommandB($command_b) {
        $this->command_b = $command_b;
    }
    public function setCommandA_RU($command_a_ru) {
        $this->command_a_ru = $command_a_ru;
    }
    public function setCommandB_RU($command_b_ru) {
        $this->command_b_ru = $command_b_ru;
    }

    public function setScore($score) {
        $this->score = $score;
    }
   
    //-------------------------Getters-------------------------//
    public function getSourceUrl() {
        return $this->source_url;
    }
    public function getId() {
        return $this->id;
    }
    public function getCategory() {
        return $this->category;
    }
    public function getName() {
        return $this->name;
    }
    public function getText() {
    	return json_decode($this->text, true);
    }
    public function getBroadcasts() {
        return json_decode($this->broadcasts, true);
    }
    public function getSlug() {
        return $this->slug;
    }
    public function getTournament() {
        return $this->tournament;
    } 
    public function getStart() {
        return $this->start;
    }
    public function getIsOnline() {
        return $this->is_online;
    }
    public function getCommandA() {
        return $this->command_a;
    }
    public function getCommandB() {
        return $this->command_b;
    }
    public function getCommandA_RU() {
        return $this->command_a_ru;
    }
    public function getCommandB_RU() {
        return $this->command_b_ru;
    }
    public function getScore() {
        return $this->score;
    }
}

