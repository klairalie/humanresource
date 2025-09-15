<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume_format extends Model
{
     protected $table = 'resume_formats';
    protected $primaryKey = 'resume_format_id';
    public $timestamps = true;

    protected $fillable = [

        'file_name',
        'file_data',

    ];
    /** @use HasFactory<\Database\Factories\ResumeFormatFactory> */
    use HasFactory;
}
