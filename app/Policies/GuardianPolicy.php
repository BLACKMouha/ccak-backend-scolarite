<?php

namespace App\Policies;

use App\Models\Guardian;

use App\Models\User;

class GuardianPolicy extends BasePolicy

{

    protected string $resource = 'guardians';
}
