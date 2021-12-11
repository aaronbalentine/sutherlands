<?php

namespace App\Custom;
use Illuminate\Support\Facades\Http;

class tmdb{

    public $response;
    private $key;
    private $url;

    function __construct(){

        //Set API Details
        $this->key = env('TMDB_API_KEY');
        $this->url = env('TMDB_API_URL');
    }

    //Function to hit 'search' endpoint
    private function send_search($query){
        
        $params = [
            'api_key' => $this->key,
            'query' => $query,
            'page' => 1
        ];

        $enc_params = http_build_query($params);

        return Http::get($this->url.'/search/movie?'.$enc_params)->json();
    }

    //Function to get details for specific movie id
    private function get_details($id){

        $params = [
            'api_key' => $this->key,
        ];

        $enc_params = http_build_query($params);

        $res = Http::get($this->url.'/movie/'.$id.'?'.$enc_params)->json();

        return $res;

    }

    //Function to get credits for specific movie id
    private function get_credits($id){

        $params = [
            'api_key' => $this->key,
        ];

        $enc_params = http_build_query($params);

        $res = Http::get($this->url.'/movie/'.$id.'/credits?'.$enc_params)->json();

        return $res;

    }

    //Drive function with error handling
    public function search($query){

        $search_res = $this->send_search($query);

        //If movie was found
        if(isset($search_res['total_results']) AND  $search_res['total_results'] > 0){

            $id = $search_res['results'][0]['id'];

            $details_res = $this->get_details($id);
            $credits_res = $this->get_credits($id);

            //If there was an error retrieving details
            if(isset($details_res['status_code'])){

                unset($this->response);
                $this->response['status'] = 'error';
                $this->response['message'] = 'Finding Movie Details: '.$details_res['status_message'];

            } //Else if there was an error retrieving credits
            else if(isset($credits_res['status_code'])){

                unset($this->response);
                $this->response['status'] = 'error';
                $this->response['message'] = 'Finding Movie Credits: '.$credits_res['status_message'];
            } //Else format the date and set the response
            else{
                $this->response['status'] = 'success';

                $details_res['formatted_date'] = date('m/d/Y', strtotime($details_res['release_date']));
                $this->response['details'] = $details_res;
                $this->response['credits'] = $credits_res;
            }
        } //Else if there was an error reported
        else if(isset($search_res['status_code'])){

            $this->response['status'] = 'error';
            $this->response['message'] = 'Finding Movie: '.$search_res['status_message'];
        } //Else there was no movie found
        else{

            $this->response['status'] = 'error';
            $this->response['message'] = "Movie not found!";
        }

    }
}