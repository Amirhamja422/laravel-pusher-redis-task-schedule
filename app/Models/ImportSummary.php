<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportSummary extends Model
{
    protected $table = 'import_summaries';

    protected $fillable = [
        'upload_history_id',
        'total_rows',
        'total_success',
        'total_duplicate',
        'total_failed',
        'total_exist'
    ];

    public function upload()
    {
        return $this->belongsTo(
            UploadHistory::class,
            'upload_history_id'
        );
    }
}
