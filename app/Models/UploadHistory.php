<?php

namespace App\Models;

use App\Models\FailedImport;
use App\Models\ImportSummary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadHistory extends Model
{
    use HasFactory;

    // Mass assignable fields
    protected $fillable = [
        'file',
        'total_rows',
        'status',
    ];

    public function summary()
    {
        return $this->hasOne(ImportSummary::class);
    }


    public function failedImports()
{
    return $this->hasMany(FailedImport::class);
}

}
