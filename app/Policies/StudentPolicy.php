<?php

namespace App\Policies;

use App\Models\Student;

use App\Models\User;

class StudentPolicy extends BasePolicy

{

    protected string $resource = 'students';
}
