<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Team extends Model
{
	protected $table = 'teams';
	protected $primaryKey = "id";

    protected $fillable = [
        'name',
        'contactEmail',
        'location',
		'plannedStartingTime',
		'startingTime',
		'teamId'
    ];

    protected $hidden = [
        'created_at',
		'updated_at'
    ];

    protected $casts = [
        'plannedStartingTime' => 'datetime',
		'startingTime' => 'datetime',
		'created_at' => 'datetime',
		'updated_at' => 'datetime'
    ];
}
