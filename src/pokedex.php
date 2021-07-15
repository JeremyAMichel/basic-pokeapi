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
            // $baseExp = $infosPokemon['base_experience'];
            // $id= $infosPokemon['id'];
            // $name=$infosPokemon['name'];
            // $weight=$infosPokemon['weight'];
            $pokemon['img']=$infosPokemon['sprites']['front_default'];

            return $pokemon;

            // return [
            //     'id' => $pokemon['id'],
            //     'name' => $pokemon['name'],
            //     'base_experience' => $pokemon['base_experience'],
            //     'weight' => $pokemon['weight'],
            //     'img' => $img
            // ];

        }

        



        // return [
        //     'name' => 'Pikachu',
        //     'id' => 25,
        //     'types' => [
        //         'electric',
        //     ],
        // ];
    }
}
