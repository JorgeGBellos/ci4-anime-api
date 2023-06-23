<?php

namespace App\Models;

use CodeIgniter\Model;

class Studio extends Model
{
    protected $table      = 'studios';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id','title','count','ScoreAvg','content'];

}