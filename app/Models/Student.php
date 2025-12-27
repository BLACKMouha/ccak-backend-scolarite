<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\UsesUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    use UsesUuidV7;

    protected $table = 'students';

    protected $fillable = ['student_number', 'full_name'];

    protected $casts = [
        'student_number' => 'string',
        'full_name' => 'string',
    ];
}
