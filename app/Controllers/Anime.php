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
        - GET LIST OF STUDIOS
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
    
    public function getAllStudios(){

        $animes = $this->animeModel->findAll();
        $studios = $this->generateStudios($animes);

        $this->response->setStatusCode(200,"Aquí tienes tus estudios");
        $this->response->setBody(json_encode($studios));
        
        return $this->response;
    }

    private function insertStudios(){
        $animes = $this->animeModel->findAll();
        $studios = [];
        $final = [];
        foreach ($animes as $key => $value) {
            $studios[] = $value['Studio'];
        }
        //echo json_encode($genres);
        foreach ($studios as $key => $value) {
            //$palabras = str_word_count($value, 1);
            $palabras = array_map('trim', explode(',', $value));
            //echo json_encode($frasesSeparadasPorComas);
            foreach ($palabras as $palabra) {
                $clean = ltrim($palabra);
                $cleanString = str_replace("\u{00A0}", "", $clean);
                //echo $finalStr."<br>";
                if(!in_array($cleanString,$final)){
                    $final[] = $cleanString;
                }
            }
        }
        foreach ($final as $key => $value) {
            $animes = $this->animeModel->like('Studio', $value)->findAll();
            $score = 0;
            foreach ($animes as $keyAnime => $valueAnime) {
                $score += $valueAnime['Score'];
            }
            $score = $score / count($animes);
            $data = [
                'title' => $value,
                'count' => count($animes),
                'ScoreAvg' => round($score,2),
                'content' => json_encode($animes)
            ];
            $this->studioModel->insert($data);
        }
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
    
    private function generateStudios($array){
        
        $studios = [];
        foreach ($array as $key => $element) {
            if(!array_key_exists($element['Studio'], $studios)){
                $studios[$element['Studio']]['count'] = 1;
                $studios[$element['Studio']]['scoreAvg'] = $element['Score'];
            }else{
                $studios[$element['Studio']]['count']++;
                $studios[$element['Studio']]['scoreAvg'] = $studios[$element['Studio']]['scoreAvg'] + $element['Score'];
            }
            $studios[$element['Studio']]['animes'][] = $element['Title'];

        }
            
        foreach ($studios as &$element) {
            $element['scoreAvg'] = round($element['scoreAvg']/$element['count'],2);
        }

        return $studios;
    }

}
