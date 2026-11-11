<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Resume extends Model
{
    // MongoDB collection name
    protected $collection = 'resumes';

    protected $fillable = [
        'user_id',
        'title',
        'current_version_id',
        'latest_version_number',
    ];

    /**
     * This replaces (ref: "User") from Mongoose
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function versions()
    {
        return $this->hasMany(ResumeVersion::class, 'resume_id');
    }

    public function currentVersion()
    {
        return $this->belongsTo(ResumeVersion::class, 'current_version_id');
    }
}