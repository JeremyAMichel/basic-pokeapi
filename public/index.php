<?php 

require_once __DIR__ . '/../vendor/autoload.php';


// $array = [
//     'name' => 'Pikachu',
//     'id' => 25,
//     'types' => [
//         'electric',
//     ],
// ];

$pokedex = new \Hb\BasicPokeapi\Pokedex();

header('Content-Type: application/json');

echo json_encode($pokedex->getAllPokemon());


?>
