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
        //echo json_encode($animes);
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
                $this->response->setStatusCode(204,"No animes to show");
                $this->response->setBody(json_encode($animes));
            }
        }
        return $this->response;
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

    private function buildResponse($code, $message, $body, $response){
        $response->setStatusCode($code,$message);
        $response->setBody(json_encode($body));
        return $response;
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
