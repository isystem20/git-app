<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use App\User;

class UserController extends Controller
{

    protected $github_url;
    
    public function __construct() {
        $this->github_url = 'https://api.github.com';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = DB::table('users')->orderby('name','desc')->limit(10)->get();

        foreach ($users as $value) {
            $github_user = $this->retrieveGithubRecords($value);
            $value->github_data = $github_user;
        }
        return $users;

    }


    private function retrieveGithubRecords($user) {

        // $responses = Http::pool(fn (Pool $pool) =>
        // [
        //     foreach ($users as $value) {
        //         $pool->as('first')->get('http://localhost/first'),
        //     } 

        // ]);

        // $fn2 = function (\Illuminate\Http\Client\Pool $pool) use ($a) {
        //     foreach ($users as $value) {
        //         $arrayPools[] = $pool->get($aVal);
        //     }
        //     return $arrayPools;
        // };

        // foreach ($users as $value) {

        // $github_record = Http::withHeaders([
        //     'Accept' => 'application/vnd.github.v3+json'
        // ])->get($this->github_url . '/users' . '/' . $user->github_username);
        

        $github_record = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json'
        ])::pool(fn (Pool $pool) => [
            $pool->get($this->github_url . '/users' . '/' . $user->github_username),
        ]);


        if ($github_record[0]->status() == 404) {
            $response = [
                'message' => 'Github account not found'
            ];
        }
        else {
            $response = $github_record[0]->json();
        }

        return $response;

    }



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
