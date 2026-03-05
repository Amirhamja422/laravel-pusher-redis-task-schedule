<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedImport extends Model
{
    protected $table = 'failed_imports';

    protected $fillable = [
        'upload_history_id',
        'name',
        'email',
        'reason'
    ];
}
