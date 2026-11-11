<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ResumeVersion extends Model
{
    protected $collection = 'resume_versions';

    protected $fillable = [
        'resume_id',
        'version_number',
        'label',
        'raw_text',
        'parsed_sections',
        'source_type',
        'parent_version_id',
        'latest_analysis_id',
        'cloudinary_public_id',
        'original_public_id',
        'file_url',
    ];

    /**
     * Casting allows us to treat the JSON/Document data 
     * as structured objects/arrays automatically.
     */
    protected function casts(): array
    {
        return [
            'parsed_sections' => 'array', // Will automatically handle the nested structure
            'version_number' => 'integer',
        ];
    }

    public function resume()
    {
        return $this->belongsTo(Resume::class, 'resume_id');
    }

    public function latestAnalysis()
    {
        return $this->belongsTo(Analysis::class, 'latest_analysis_id', '_id');
    }
}