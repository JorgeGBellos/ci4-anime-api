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
        $animes['content'] = $this->animeModel->where('Rank', $id)->findAll();
        $animes['next-anime'] = base_url('anime/'.$id+1);
        echo json_encode($animes);
        //var_dump($animes);
    }
    /*
    Endpoints needed consuption only

        - GET ALL ANIMES
        - GET ANIME BY ID 
        - GET LIST OF GENRES
        - GET ANIMES BY GENRES
        - GET ANIMES BY TYPE
        - GET ANIMES WITH RANGE OF SCORE
    */
    //Limit will be <=10

    //    offset = (page - 1) * page_size
    //    limit = page_size
       
    public function getAllAnimes(){
        $animes = $this->animeModel->findAll();
        //echo json_encode($animes);
        $limit = $this->request->getGet('limit');
        $page = $this->request->getGet('page');

        if($this->validateParams($limit,$page)){
            $offset = ($page - 1) * $limit;
            $query   = $this->builder->get($limit,$offset);
            $animes = $query->getResult();
            return $this->response->setJSON(json_encode($animes));
        }else{
            return $this->response->setJSON(json_encode($animes));
        }
        //$query   = $this->builder->get();
        //echo json_encode($animes);
        //var_dump($query->getResult());
    }
    
    //validate only limit and page params
    private function validateParams($limit,$page){
        //limit should be <=10
        //page should be possible

        if(is_numeric($limit) && $limit <=10){
            
            $max_pages = 250/$limit;

            if(is_numeric($page) && $page <= $max_pages){
                return true;
            }
        }

        return false;
    }
    private function applyOffset($array, $page){

    }
    
    private function applyLimit($array, $limit){
        if(count($array) <= $limit){
            return $array;
        }else{
            return array_slice($array,0,$limit);
    
        }
    }
}
