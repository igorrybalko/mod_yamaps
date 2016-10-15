<?php
/**
 * @package mod_yamaps
 * @author Rybalko Igor
 * @version 1.0
 * @copyright (C) 2016 http://wolfweb.com.ua
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 *
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once dirname(__FILE__) . '/helper.php';

$i = 0; 
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$breadthCent = htmlspecialchars(trim($params->get('breadth_cent')));
$longitudeCent = htmlspecialchars(trim($params->get('longitude_cent')));
$zoom = htmlspecialchars(trim($params->get('zoom')));
$height = (int) htmlspecialchars(trim($params->get('height')));
$info = json_decode($params->get('items'), 1);

$language = JFactory::getLanguage()->getTag();

switch ($language) {
    case 'ru-RU':
        $lang = 'ru_RU';
        break;
    case 'uk-UA':
        $lang = 'uk_UA';
        break;   
    default:
         $lang = 'en_GB';
        break;
}

$scriptSource = "https://api-maps.yandex.ru/2.1/?lang=" . $lang;

$doc = JFactory::getDocument();
$doc->addScript($scriptSource);

$correct = rand(1,1000);

if(!$height){
    $height = 300;
}

if( !is_numeric($zoom) || intval($zoom) < 1 ){
    $zoom = 14;
}

if( !is_numeric($breadthCent)){
    $breadthCent = 50.4472746;
}
if( !is_numeric($longitudeCent)){
    $longitudeCent = 30.5236766;
}

if($info){
    $items = yaMapsModHelper::convert($info);
    if(count($items) == 1){
        $breadthCent = $items[0]['breadth'];
        $longitudeCent = $items[0]['longitude'];
    }
}else{
    $items = array();
    $items[0]["breadth"] = $breadthCent;
    $items[0]["longitude"] = $longitudeCent;
    $items[0]["desc"] = '';
}

foreach($items as $item){
    $i++;
    $coords .= "[" . $item['breadth']. ", " . $item['longitude'] . " ],";
    $descs  .= "'" . $item['desc'] . "',";
    $clustcaps  .= "'" . ($item['capcluster'] ? $item['capcluster'] : 'Title ' .$i) . "',";  
}

?>

<script>
    
function initYaMaps(){ 

    var myMap,
        myGeoObjects = [],
        coords = [<?php echo trim($coords, ','); ?>],
        descs = [<?php echo trim($descs, ','); ?>],
        clustcaps = [<?php echo trim($clustcaps, ','); ?>];

    myMap = new ymaps.Map('map<?php echo $correct ?>', {
        center: [<?php echo $breadthCent . ', ' . $longitudeCent;?>],
        zoom: <?php echo $zoom; ?>,
        controls: ['zoomControl']
    }); 
    
    for (var i = 0; i < coords.length; i++) {
      myGeoObjects[i] = new ymaps.GeoObject({
        geometry: {
          type: "Point",
          coordinates: coords[i]
        },
        properties: {
          clusterCaption: clustcaps[i],
          balloonContentBody: descs[i]
        }
      });
    }

    var myClusterer = new ymaps.Clusterer(
      {clusterDisableClickZoom: true}
    );
    myClusterer.add(myGeoObjects);
    myMap.geoObjects.add(myClusterer);
}

ymaps.ready(initYaMaps);

</script>
<?php
require JModuleHelper::getLayoutPath('mod_yamaps', $params->get('layout', 'default'));