<?php

/*
 * This file is part of the basic-pokeapi package.
 *
 * (c) Benjamin Georgeault
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hb\BasicPokeapi;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class Pokedex
 *
 * @author Benjamin Georgeault
 */
class Pokedex
{
    private HttpClientInterface $client;

    public function __construct()
    {
        $this->client = HttpClient::createForBaseUri('https://pokeapi.co/api/v2/');
    }

    public function getPikachu(): array
    {

        $response = $this->client->request('GET', 'pokemon/25');

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Error from Pokeapi.co');
        }else {
            $infosPokemon= $response->toArray();
            $pokemon=array_intersect_key($infosPokemon,array_flip(['base_experience','id','name','weight']));
            $pokemon['img']=$infosPokemon['sprites']['front_default'];
            return $pokemon;
        }
    }

    public function getAllPokemon($nextPokemons = null, $pokemons=[]) : array {

        if($nextPokemons==null){
            $response = $this->client->request('GET', 'pokemon/');
        }else{
            $response = $this->client->request('GET', 'pokemon/'.$nextPokemons);
        }
        
        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Error from Pokeapi.co');
        }else {
            $allPokemon= $response->toArray();
            foreach($allPokemon['results'] as $pokemon){            
                $id = explode('/',$pokemon['url']);
                $pokemons[]=[
                    'id'=> $id[6],
                    'name'=> $pokemon['name']
                ];
            }
            
            if(isset($allPokemon['next'])){
                $next = explode('/',$allPokemon['next']);
                return $this->getAllPokemon($next[6],$pokemons);
            }else{
                return $pokemons;
            }  
        }
    }
}
