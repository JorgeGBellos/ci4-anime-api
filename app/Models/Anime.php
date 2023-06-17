<?php

namespace App\Models;

use CodeIgniter\Model;

class Anime extends Model
{
    protected $table      = 'anime';
    protected $primaryKey = 'Rank';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['Rank', 'Title'];

}