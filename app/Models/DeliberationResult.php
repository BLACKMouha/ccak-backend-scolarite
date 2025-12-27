<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliberationResult extends Model
{
    use HasFactory;
    use UsesUuidV7;

    public const DECISIONS = ['ADMITTED', 'ADMITTED_COMPENSATION', 'RESIT', 'FAILED', 'EXCLUDED'];
    public const HONOR_LEVELS = ['PASSABLE', 'ASSEZ_BIEN', 'BIEN', 'TRES_BIEN'];

    protected $fillable = [
        'deliberation_session_id',
        'student_id',
        'decision',
        'jury_remarks',
        'is_with_honors',
        'honor_level',
    ];

    protected $casts = [
        'is_with_honors' => 'boolean',
    ];
}
