<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Analysis extends Model
{
    protected $collection = 'analyses';

    protected $fillable = [
        'user_id',
        'resume_id',
        'version_id',
        'ats_score',
        'score_breakdown',
        'issues',
        'strengths',
        'bullet_rewrites',
        'keywords_present',
        'keywords_missing',
        'summary',
        'model',
        'prompt_tokens',
        'response_tokens',
    ];

    /**
     * Cast attributes to handle nested structures.
     */
    protected function casts(): array
    {
        return [
            'score_breakdown' => 'array',
            'issues' => 'array',
            'strengths' => 'array',
            'bullet_rewrites' => 'array',
            'keywords_present' => 'array',
            'keywords_missing' => 'array',
            'ats_score' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
    
    public function version()
    {
        return $this->belongsTo(ResumeVersion::class);
    }
}