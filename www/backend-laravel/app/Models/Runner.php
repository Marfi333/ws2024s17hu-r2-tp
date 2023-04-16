<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Runner extends Model
{
	protected $table = 'runners';
	protected $primaryKey = "id";

    protected $fillable = [
        'firstName',
        'lastName',
        'speed',
		'token',
		'isAdmin',
		'teamId'
    ];

    protected $hidden = [
        'created_at',
		'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
		'updated_at' => 'datetime'
    ];
}
