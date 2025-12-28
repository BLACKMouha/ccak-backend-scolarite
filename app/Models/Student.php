<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'students';

    protected $fillable = ['user_id', 'student_number', 'full_name', 'date_of_birth', 'place_of_birth', 'nationality', 'phone', 'emergency_contact_name', 'emergency_contact_phone', 'address', 'photo_url', 'status'];

    protected $casts = [
        'user_id' => 'string',
        'student_number' => 'string',
        'full_name' => 'string',
        'date_of_birth' => 'date',
        'place_of_birth' => 'string',
        'nationality' => 'string',
        'phone' => 'string',
        'emergency_contact_name' => 'string',
        'emergency_contact_phone' => 'string',
        'address' => 'string',
        'photo_url' => 'string',
        'status' => 'string',
    ];

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_SUSPENDED = 'SUSPENDED';
    public const STATUS_GRADUATED = 'GRADUATED';
    public const STATUS_WITHDRAWN = 'WITHDRAWN';
    public const STATUS_EXPELLED = 'EXPELLED';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_SUSPENDED,
            self::STATUS_GRADUATED,
            self::STATUS_WITHDRAWN,
            self::STATUS_EXPELLED,
        ];
    }
}
