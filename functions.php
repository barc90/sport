<?php


// Транслитерация строк.
function translit($str) {
    $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
    $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
    return str_replace($rus, $lat, $str);
}

function gen_slug ($title) {
	$slug = str_replace('.', '', $title);
    $slug = str_replace([' &ndash; ', ' '], '-', $slug);

    return strtolower(translit($slug));
}

function filter_slug($str) {
    $slug = str_replace(' – ', '–', $str);
    $slug = trim(strtolower(translit($slug)));
    $slug = str_replace('.–', '–', $slug);
    $slug = str_replace('.-', '-', $slug);
    $slug = str_replace('`', '', $slug);
    $slug = str_replace(' ', '-', $slug);
    $slug = str_replace('/', '_', $slug);
    

    return $slug;
}
function queryCurl($url, $referer = "http://www.google.ru/", $useragent = "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.6) Gecko/20100625 Firefox/3.6.6") 
{
     
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($c, CURLOPT_HEADER, false);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_USERAGENT, $useragent);
    curl_setopt($c, CURLOPT_REFERER, $referer);
 
    curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 20);
 
    $curlresult = curl_exec($c);
 
    $get_curl_error = curl_errno($c);
 
    if ($get_curl_error) 
    {
        echo 'CURL Error: ' . curl_error($c);
        curl_close($c);
        return false;
    }
    curl_close($c);
    return $curlresult;
}


function getLiveLinks($event_live_links) {
    $unique_links = [];
    foreach ($event_live_links as $key => $value) {
        $unique_links["http:".trim(html_entity_decode($value->href))] = "";
    }
    return $unique_links;
}
function saveImages($dom_team_imgs, $commands, $category_slug) {

    echo "DIR: " .dirname(__DIR__) . "\n";
    $i = 0;
    foreach ($dom_team_imgs as $key => $value) {
        if (strpos($value->src, 'teams') !== false) {

            $img_path = "http:".$value->src;
            $img_type = exif_imagetype($img_path);
    
            if (!file_exists(__DIR__ . "/upload/imgs/events/$category_slug/". $commands[$i] . ".png")) { 
                if ($img_type != IMAGETYPE_PNG) { // Преобразуем в png для не png
                    imagepng(imagecreatefromstring(file_get_contents($img_path)), __DIR__ . "/upload/imgs/events/$category_slug/". $commands[$i] . ".png");
                }
                else {
                    file_put_contents(__DIR__ ."/upload/imgs/events/$category_slug/".  $commands[$i] . ".png", file_get_contents($img_path));
                }
            }
            $i++;
        }
       
    }
}