<?php

namespace App\Controllers;
use CodeIgniter\HTTP\IncomingRequest;

class Anime extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }
    
    // .../anime/$id
    public function getAnime($id){
        $total = $this->animeModel->findAll();
        $animes['content'] = $this->animeModel->where('Rank', $id)->findAll();

        if($id > 0  && $id <= count($total)){

            if($id < count($total)){
                $animes['next-anime'] = base_url('anime/'.$id+1);
            }else{
                $animes['next-anime'] = base_url('anime/1');
            }
            
            $this->response->setStatusCode(200, "Here you have your anime");
            $this->response->setBody(json_encode($animes));
        }else{
            
            $this->response->setStatusCode(404, "Ups, nothing was found");
            $this->response->setBody([]);
        }
        return $this->response;
    }

    /*
    Endpoints needed comsuption only

        - GET ALL ANIMES (ready)
        - GET ANIME BY ID (ready)
        - GET LIST OF GENRES 
        - GET ANIMES BY GENRES
        - GET ANIMES BY TYPE
        - GET ANIMES WITH RANGE OF SCORE
    */
    //Limit will be <=10
    //limit and page will be always needed both

    //    offset = (page - 1) * page_size
    //    limit = page_size
       
    public function getAllAnimes(){
        $animes = $this->animeModel->findAll();
        $limit = $this->request->getGet('limit');
        $page = $this->request->getGet('page');

        if(!isset($limit) && !isset($page)){
            $this->response->setStatusCode(200, "Here you have all the animes");
            $this->response->setBody(json_encode($animes));
        }else{
            
            if($this->validateParams($limit,$page)){
                $offset = ($page - 1) * $limit;
                $query   = $this->builder->get($limit,$offset);
                $animesFiltered = $query->getResult();
                $this->response->setStatusCode(200,"The animes were filtered");
                $this->response->setBody(json_encode($animesFiltered));
            }else{
                $this->response->setStatusCode(404,"No animes to shows");
                $this->response->setBody([]);
            }
        }
        return $this->response;
    }
    
    public function getAllGenres(){

        $this->builder->select('Genre');
        $query = $this->builder->get();
        echo '<pre>'; print_r($query->getResultArray()); echo '</pre>';

    }

    //validate only limit and page params
    private function validateParams($limit,$page){
        //limit should be <=10
        //page should be possible
        try {
            //code...    
            if(is_numeric($limit) && $limit <=10){
                
                $max_pages = 250/$limit;

                if(is_numeric($page) && $page <= $max_pages){
                    return true;
                }
            }
            
            return false;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }
}
