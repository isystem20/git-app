<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use App\User;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

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

        // $fn2 = function (\Illuminate\Http\Client\Pool $pool) use ($a) {
        //     foreach ($users as $value) {
        //         $arrayPools[] = $pool->get($aVal);
        //     }
        //     return $arrayPools;
        // };

        // foreach ($users as $value) {

        try {

            $cached_user = Redis::get($user->github_username);

            if ($cached_user != null) {
                Log::info('Data retrieved from cache. (' . $user->github_username . ')');
                return json_decode($cached_user);
            }

        } catch (ModelNotFoundException $exception) {
            Log::critical($exception->getMessage());
            return back()->withError($exception->getMessage())->withInput();
        }


        try {
            
            $github_record = Http::withHeaders([
                'Accept' => 'application/vnd.github.v3+json'
            ])->get($this->github_url . '/users' . '/' . $user->github_username);

        } catch (ModelNotFoundException $exception) {
            Log::critical($exception->getMessage());
            return back()->withError($exception->getMessage())->withInput();
        }

        if ($github_record->status() == 404) {
            $response = [
                'message' => 'Github account not found'
            ];
            Log::error($response);
        }
        else {
            $response = $github_record->json();
            Redis::set($user->github_username,json_encode($response));
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

        try {

            $user = DB::table('users')->where('id',$id)->first();

            if($user) {
                $github_user = $this->retrieveGithubRecords($user);
                $user->github_data = $github_user;
            }

        } catch (ModelNotFoundException $ex) { 
            abort(422, 'Invalid id: User not found');
        } catch (Exception $ex) { 
            abort(500, 'Something went wrong when fetching your data.');
        }

        $user_obj = [
            'user' => $user,
        ];

        $response = [
            'result' => $user_obj,
            'status' => 'success',
        ];

        return response($response,201);

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

        try {

            $file = User::where('id', $id)->first(); // File::find($id)

            if($file) {
    
                return $file->delete();
            }

        } catch (ModelNotFoundException $ex) { 
            abort(422, 'Invalid id: User not found');
        } catch (Exception $ex) { 
            abort(500, 'Something went wrong when deleting your data.');
        }


    }
}
