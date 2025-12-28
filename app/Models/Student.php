<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory, HasUuids;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'student_number',
        'full_name',
        'gender',
        'date_of_birth',
        'place_of_birth',
        'nationality',
        'phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'address',
        'photo_url',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    /**
     * Get the user that owns the student.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the guardians for the student.
     */
    public function guardians()
    {
        return $this->hasMany(Guardian::class);
    }

    /**
     * Get the documents for the student.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Scope a query to only include active students.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    /**
     * Scope a query to only include graduated students.
     */
    public function scopeGraduated($query)
    {
        return $query->where('status', 'GRADUATED');
    }

    /**
     * Get the student's age.
     */
    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }

    /**
     * Generate a unique student number.
     * Format: UCAK{YEAR}{NUMBER}
     * Example: UCAK2024001
     */
    public static function generateStudentNumber(): string
    {
        $year = date('Y');
        $lastStudent = self::where('student_number', 'like', "UCAK{$year}%")
            ->orderBy('student_number', 'desc')
            ->first();

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->student_number, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "UCAK{$year}{$newNumber}";
=======
    protected $fillable = [
        'student_number',
        'full_name',
        'email',
        'phone',
        'birth_date',
        'address',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class)->whereIn('status', ['ACTIVE', 'REGISTERED']);

    }
}
