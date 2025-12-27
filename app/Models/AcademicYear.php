<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\UsesUuidV7;

class AcademicYear extends Model
{
    use HasFactory;
    use UsesUuidV7;

    protected $table = 'academic_years';

    protected $fillable = ['name'];

    protected $casts = [
        'name' => 'string',
    ];
}
