<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioMessage extends Model
{
    protected $fillable = [
        'message_id',
        'name',
        'message',
        'rating',
        'posted_at_ms',
        'session_token',
        'hidden',
        'flagged',
        'pinned',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'posted_at_ms' => 'integer',
            'hidden' => 'boolean',
            'flagged' => 'boolean',
            'pinned' => 'boolean',
        ];
    }

    public function toFrontendArray(): array
    {
        return [
            'id' => $this->message_id,
            'name' => $this->name,
            'message' => $this->message,
            'rating' => $this->rating,
            'timestamp' => $this->posted_at_ms,
            'sessionToken' => $this->session_token,
            'hidden' => $this->hidden,
            'flagged' => $this->flagged,
            'pinned' => $this->pinned,
        ];
    }
}
