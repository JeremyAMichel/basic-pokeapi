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

    public function getAllPokemon($offset=0, $limit=50) : array {
        
        $pokemons=[];
        $response = $this->client->request('GET', 'pokemon/?offset='.$offset.'&limit='.$limit);
        
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
                
                if (!preg_match('/\?.*offset=([0-9]+).*limit=([0-9]+)|\?.*limit=([0-9]+).*offset=([0-9]+)/', $allPokemon['next'], $matches)) {
                    throw new \RuntimeException('Cannot match offset on next page.');
                }
                // var_dump($matches);
                // die();
                $nextOffset = $matches[1];

                // if (!preg_match('/\?.*limit=([0-9]+)/', $allPokemon['next'], $matches)) {
                //     throw new \RuntimeException('Cannot match offset on next page.');
                // }

                $nextLimit = $matches[2];

                $nextPokemons = $this->getAllPokemon($nextOffset,$nextLimit);
                
                $pokemons = array_merge($pokemons, $nextPokemons);
            }
            
            return $pokemons;
            
        }
    }
}
