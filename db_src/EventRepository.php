<?php

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventRepository extends EntityRepository 
{

    private function getDefaultResultSetMapping() {
        $rsm = new ResultSetMapping;
        $rsm->addEntityResult('Event', 'e');

        $rsm->addFieldResult('e', 'id', 'id');
        $rsm->addFieldResult('e', 'slug', 'slug');
        $rsm->addFieldResult('e', 'name', 'name');
        $rsm->addFieldResult('e', 'start', 'start');
        $rsm->addFieldResult('e', 'is_online', 'is_online');
        $rsm->addFieldResult('e', 'command_a', 'command_a');
        $rsm->addFieldResult('e', 'command_b', 'command_b');

        $rsm->addJoinedEntityResult('Category' , 'c', 'e', 'category');
        $rsm->addFieldResult('c', 'category_id', 'id');
        $rsm->addFieldResult('c', 'cat_slug', 'slug');

        $rsm->addJoinedEntityResult('Tournament' , 't', 'e', 'tournament');
        $rsm->addFieldResult('t', 'tournament_id', 'id');
        $rsm->addFieldResult('t', 't_name', 'name');
        $rsm->addFieldResult('t', 't_slug', 'slug');

        return $rsm;
    }

	public function getTournaments($days, $category_id = null) {
		$conn = $this->getEntityManager()->getConnection();

		if (!empty($category_id)) {
			$sql = 'SELECT t.name, t.id FROM events e, tournaments t WHERE t.id = e.tournament_id AND (SUBDATE(start, INTERVAL :days DAY) < NOW() AND NOW() <= DATE_ADD(start, INTERVAL 3 HOUR)) AND category_id = :category_id GROUP BY t.name, t.id
			';
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':category_id', $category_id);
		}
		else {
			$sql = 'SELECT t.name, t.id FROM events e, tournaments t WHERE t.id = e.tournament_id AND (SUBDATE(start, INTERVAL :days DAY) < NOW() AND NOW() <= DATE_ADD(start, INTERVAL 3 HOUR)) GROUP BY t.name, t.id
			';
			$stmt = $conn->prepare($sql);
		}
		
		$stmt->bindParam(':days', $days);
		$stmt->execute();
		return $stmt->fetchAll(); // PDO::FETCH_COLUMN, 0
	}

	public function getRecentCategories($days) {
		$conn = $this->getEntityManager()->getConnection();
		$sql = 'SELECT name, id from categories WHERE id in (SELECT e.category_id FROM events e, categories c WHERE (SUBDATE(start, INTERVAL :days DAY) < NOW() AND NOW() <= DATE_ADD(start, INTERVAL 3 HOUR)) GROUP BY e.category_id)
		';
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':days', $days);
		$stmt->execute();
		return $stmt->fetchAll();
	}


    // Получаем онлайн матчи опред. чемп. 
    public function getRelatedEvents($tournament_id, $event_id) {
        $sql = 'SELECT e.id, e.slug, e.name, e.start, e.is_online, e.broadcasts, e.category_id, e.tournament_id, e.command_a, e.command_b, c.slug AS cat_slug, t.name AS t_name, t.slug AS t_slug FROM events e, categories c, tournaments t WHERE e.is_online = 1 AND e.tournament_id = t.id AND e.tournament_id= t.id AND e.category_id = c.id AND e.tournament_id = :tournament_id AND e.id != :event_id';
        
        $query = $this->getEntityManager()->createNativeQuery($sql, $this->getDefaultResultSetMapping());
    
        $query->setParameter('tournament_id',  $tournament_id);
        $query->setParameter('event_id',  $event_id);
        
        return $query->getResult();
    }

	public function getRecentEvents($days, $tournament_id) {
        

        $sql = 'SELECT e.id, e.slug, e.name, e.start, e.is_online, e.broadcasts, e.category_id, e.tournament_id, e.command_a, e.command_b, c.slug AS cat_slug, t.name AS t_name, t.slug AS t_slug FROM events e, categories c, tournaments t WHERE (SUBDATE(start, INTERVAL :days DAY) < NOW() AND NOW() <= DATE_ADD(start, INTERVAL 3 HOUR)) AND e.tournament_id = t.id AND e.tournament_id= t.id AND e.category_id = c.id AND e.tournament_id = :tournament_id';
        
        $query = $this->getEntityManager()->createNativeQuery($sql, $this->getDefaultResultSetMapping());
    
        $query->setParameter('tournament_id',  $tournament_id);
        $query->setParameter('days',  $days);

        return $query->getResult();
    } 

    public function getRecentEventsByCategory($days, $category_id) {
    

        $sql = 'SELECT e.id, e.slug, e.name, e.start, e.is_online, e.broadcasts, e.category_id, e.tournament_id, e.command_a, e.command_b, c.slug AS cat_slug, t.name AS t_name, t.slug AS t_slug FROM events e, categories c, tournaments t WHERE e.tournament_id = t.id AND e.category_id = c.id AND e.tournament_id = t.id AND (SUBDATE(start, INTERVAL :days DAY) < NOW() AND NOW() <= DATE_ADD(start, INTERVAL 3 HOUR)) AND category_id = :category_id';
        $query = $this->getEntityManager()->createNativeQuery($sql, $this->getDefaultResultSetMapping());
        $query->setParameter('category_id', $category_id);

        $query->setParameter('days',  $days);

        return $query->getResult();
    }


    public function getRecentBugs($number = 30)
    {
        $dql = "SELECT b, e, r FROM Bug b JOIN b.engineer e JOIN b.reporter r ORDER BY b.created DESC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setMaxResults($number);
        return $query->getResult();
    }

    public function getRecentBugsArray($number = 30)
    {
        $dql = "SELECT b, e, r, p FROM Bug b JOIN b.engineer e ".
               "JOIN b.reporter r JOIN b.products p ORDER BY b.created DESC";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setMaxResults($number);
        return $query->getArrayResult();
    }

    public function getUsersBugs($userId, $number = 15)
    {
        $dql = "SELECT b, e, r FROM Bug b JOIN b.engineer e JOIN b.reporter r ".
               "WHERE b.status = 'OPEN' AND e.id = ?1 OR r.id = ?1 ORDER BY b.created DESC";

        return $this->getEntityManager()->createQuery($dql)
                             ->setParameter(1, $userId)
                             ->setMaxResults($number)
                             ->getResult();
    }

    public function getOpenBugsByProduct()
    {
        $dql = "SELECT p.id, p.name, count(b.id) AS openBugs FROM Bug b ".
               "JOIN b.products p WHERE b.status = 'OPEN' GROUP BY p.id";
        return $this->getEntityManager()->createQuery($dql)->getScalarResult();
    }
}
