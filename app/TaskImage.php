<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskImage extends Model
{
    protected $table = "task_images";
    use SoftDeletes;
}
