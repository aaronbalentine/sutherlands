<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Custom\tmdb;

class TMDBController extends Controller
{
    //Receive incoming request and send to TMDB class for processing, return result to frontend
    function send_request(Request $req){

        $tmdb = new tmdb();

        $tmdb->search($req['query']);

        return $tmdb->response;

    }
}
