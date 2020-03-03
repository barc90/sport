<?php
// php -S localhost:8080 -t .
require 'config.php';
require 'vendor/autoload.php';
require 'functions.php';
// Assuming you installed from Composer:
require 'bootstrap.php'; 
require 'db_src/Event.php';
require 'db_src/Category.php';
require 'db_src/EventRepository.php';
require 'db_src/Tournament.php';

//use Monolog\Logger;
//use Monolog\Handler\StreamHandler;

use Doctrine\ORM\Query\ResultSetMapping;


$app            = System\App::instance();
$app->request   = System\Request::instance();
$app->route     = System\Route::instance($app->request);

$route          = $app->route;


$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig   = new \Twig\Environment($loader, ['debug' => false]);  // 'cache' => 'cache', 
$twig->addExtension(new \Twig\Extension\DebugExtension());

// Главная 
$route->any('/', function() {
	global $twig, $entityManager;

	$categories_events = []; // ['Футбол' => [Event1, Event2...]]

	$all_categories = $entityManager->getRepository(Category::class)->findAll();
	$recent_categories = $entityManager->getRepository(Event::class)->getRecentCategories(1);

	foreach ($recent_categories as $value) {
		$categories_events[$value["name"]] = $entityManager->getRepository(Event::class)->getRecentEventsByCategory(1,  $value["id"]);
	}

    echo $twig->render('index.html', ['categories' => $all_categories, 'categories_events' => $categories_events]);
});


// Категория
$route->get('/cat/?', function($cat_slug) {
    global $twig, $entityManager;

    $categories = $entityManager->getRepository(Category::class)->findAll();
    $category   = $entityManager->getRepository(Category::class)->findOneBy(array('slug' => $cat_slug));

    if ($category == null) {
        http_response_code(404);
		echo $twig->render('404.html', ['page' => $cat_slug, 'categories' => $categories]);
    }
    else {
    	$tournament_events = []; // ['ЛЧ' => [Event1, Event2...]]

    	$tournaments_of_events = $entityManager->getRepository(Event::class)->getTournaments(3, $category->getId());

    	foreach ($tournaments_of_events as $tournament) {
    		$tournament_events[$tournament["name"]] = $entityManager->getRepository(Event::class)->getRecentEvents(3, $tournament["id"]);
    	}
    	
    	echo $twig->render('category.html', ['categories' => $categories, 'category' => $category, 'tournament_events' => $tournament_events]);
    }

});

// Сам Event
$route->get('/?', function($event_slug) {
    global $twig, $entityManager;

    $categories = $entityManager->getRepository(Category::class)->findAll();
    $event      = $entityManager->getRepository(Event::class)->findOneBy(array('slug' => $event_slug));

    if ($event == null) {
    	http_response_code(404);
    	echo $twig->render('404.html', ['page' => $event_slug, 'categories' => $categories]);
    	exit();
    }

    $related_events = $entityManager->getRepository(Event::class)->getRelatedEvents($event->getTournament(), $event->getId());

    echo $twig->render('event.html', ['categories' => $categories, 'event' => $event, 'related_events' => $related_events]);
});

// Просмотр
$route->get('/watch/{channel}/{url}', function($channel, $url) {
    global $twig;


    $url = base64_decode(urldecode($url));

    if ($channel == "alieztv") {

        $encode_url = urlencode($url);

        $encode_useragent = urlencode($_SERVER['HTTP_USER_AGENT']);

        $html_alieztv = queryCurl(PROXY_SCRIPT.'?url='.$encode_url . '&user_agent='.$encode_useragent);
        preg_match('/(http|https):\/\/(\d{1,3}\.){3}\d{1,3}(:\d{1,5})\/[\w\/\-?=%.]+/', $html_alieztv, $matches);

        // if (empty($matches)) {
            //     echo "content is blocked\n";
            // }
       //if empty($matches)
            // preg_match('/(http|https):\/\/(\d{1,3}\.){3}\d{1,3}(:\d{1,5})\/[\w\/\-?=%.]+/', $html_alieztv, $matches);

        $iframe = $twig->render('channels/alieztv.html', ['url' => $matches[0]]);
    }
    else {
        $iframe= '<iframe style="width: 100%; height: 100%; position: absolute" src="'.$url.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
    }
    
    echo $twig->render('watch.html', ['iframe' => $iframe]);
});

$route->end();
