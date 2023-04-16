<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RunnerStage extends Model
{
	protected $table = 'runner_stage';
	protected $primaryKey = "id";

    protected $fillable = [
        'runner_id',
        'stage_id',
        'handoverTime',
    ];

	public function runner(): HasOne
	{
		return $this->hasOne(Runner::class, 'runner_id');
	}

	public function stage(): HasOne
	{
		return $this->hasOne(Stage::class, 'stage_id');
	}
}
