<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use App\User;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Request;

class UserController extends Controller
{

    protected $github_uri;
    
    public function __construct() {
        $this->github_uri = 'https://api.github.com/users';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = DB::table('users')->orderby('name','desc')->limit(10)->get();

        // foreach ($users as $value) {
        //     $github_user = $this->retrieveGithubRecords($value);
        //     $value->github_data = $github_user;
        // }
        // return $users;

        return $this->fetchGithubData();
    }


    private function fetchGithubData() {
        $client = new Client(['base_uri' => $this->github_uri]);
        // Initiate each request but do not block
        $promises = [
            0 => $client->getAsync('/isystem20'),
        ];

        $headers = ['accept' => 'application/vnd.github.v3+json'];


        $request = new Request('GET', $this->github_uri . '/isystem20', $headers, null);
        $promise = $client->send($request);
        // $promise->then(
        //     function (ResponseInterface $res) {
        //         echo $promise->getBody();
        //         echo $res->getStatusCode() . "123\n123";
        //     },
        //     function (RequestException $e) {
        //         echo $promise->getBody();
        //         echo $e->getMessage() . "111\n2222";
        //         // echo $e->getRequest()->getMethod();
        //     }
        // );

        return $promise->getBody();


        // Wait for the requests to complete; throws a ConnectException
        // if any of the requests fail
        // $responses = Promise\Utils::unwrap($promises);
    }




    // private function retrieveGithubRecords($user) {

    //     // $responses = Http::pool(fn (Pool $pool) =>
    //     // [
    //     //     foreach ($users as $value) {
    //     //         $pool->as('first')->get('http://localhost/first'),
    //     //     } 

    //     // ]);

    //     // $fn2 = function (\Illuminate\Http\Client\Pool $pool) use ($a) {
    //     //     foreach ($users as $value) {
    //     //         $arrayPools[] = $pool->get($aVal);
    //     //     }
    //     //     return $arrayPools;
    //     // };

    //     // foreach ($users as $value) {

    //     // $github_record = Http::withHeaders([
    //     //     'Accept' => 'application/vnd.github.v3+json'
    //     // ])->get($this->github_url . '/users' . '/' . $user->github_username);
        

    //     $github_record = Http::withHeaders([
    //         'Accept' => 'application/vnd.github.v3+json'
    //     ])::pool(fn (Pool $pool) => [
    //         $pool->get($this->github_url . '/users' . '/' . $user->github_username),
    //     ]);


    //     if ($github_record[0]->status() == 404) {
    //         $response = [
    //             'message' => 'Github account not found'
    //         ];
    //     }
    //     else {
    //         $response = $github_record[0]->json();
    //     }

    //     return $response;

    // }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
