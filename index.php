<?php
require __DIR__ . '/vendor/autoload.php';

$memcacheD = new Memcached();
$memcacheD->addServer('127.0.0.1', 11211);

use Stplus\Chain\ZipcodeChecker;

//we instantiëren nu de handlers voor de ketting
$chain = [];
$chain[] = new ZipcodeChecker\Handler\cacheHandler($memcacheD);
$chain[] = new ZipcodeChecker\Handler\googleHandler();
$chain[] = new ZipcodeChecker\Handler\postcodeApiHandler();
$chain[] = new ZipcodeChecker\Handler\proxyHandler(); //we voegen even een simpele class om een idee te krijgen van een proxy.

//met een foreach loop rijg ik de ketting aan elkaar.
foreach($chain as $key=>$handler){
    if(!empty($chain[$key+1])){
        $handler->setNextHandler($chain[$key+1]);
    }
}
//Hier definieer ik nu de hanger.
//$pendant = new ZipcodeChecker\addressPendant('11','3907JN','Nederland');
$pendant = new ZipcodeChecker\addressPendant('43','2274RG','Nederland');

//even een dump om te laten zien wat er nu in de hanger zit.
var_dump($pendant->getAttributesArray());

//pak de eerste handler.
$firstHandler = reset($chain);

//Nu uitvoeren.
$result = $firstHandler->start($pendant);
//Hebben we resultaat, dan gaan we dit resultaat tonen, en vervolgens opslaan in de cache.
if($result) {
    $key = $pendant->getAttribute('zipcode').$pendant->getAttribute('streetnumber');
    $memcacheD->set($key, $pendant->getAttributesArray());
    var_dump($pendant->getAttributesArray());
}else{
    var_dump('no data', $result);
}