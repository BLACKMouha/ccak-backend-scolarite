<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy extends BasePolicy
{
    protected string $resource = 'documents';

    public function review(User $user, Document $document): bool
    {
        return $this->allow($user, 'documents.update');
    }
}
