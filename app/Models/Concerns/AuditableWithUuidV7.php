<?php

namespace App\Models\Concerns;

use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Ramsey\Uuid\Uuid;

trait AuditableWithUuidV7
{
    use AuditableTrait, HasUuids;

    /**
     * Boot the trait
     */
    public static function bootAuditableWithUuidV7(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Uuid::uuid7()->toString();
            }
        });
    }

    /**
     * Initialize the trait
     */
    public function initializeAuditableWithUuidV7(): void
    {
        $this->incrementing = false;
        $this->keyType = 'string';

        // Set default audit configuration
        if (property_exists($this, 'fillable') && !property_exists($this, 'auditInclude')) {
            $this->auditInclude = $this->fillable;
        }

        if (!property_exists($this, 'auditEvents')) {
            $this->auditEvents = ['created', 'updated', 'deleted', 'restored'];
        }
    }

    /**
     * Generate new UUID v7
     */
    public function newUniqueId(): string
    {
        return Uuid::uuid7()->toString();
    }

    /**
     * Transform audit data
     */
    public function transformAudit(array $data): array
    {
        // Add UUID information
        $data['auditable_uuid'] = $this->getKey();

        // Add user information if available
        if (auth()->check()) {
            $user = auth()->user();
            $data['user_uuid'] = $user->getKey();
            $data['user_type'] = get_class($user);
            $data['user_email'] = $user->email;
        }

        return $data;
    }

    /**
     * Attributes to exclude from audit
     */
    public function getAuditExclude(): array
    {
        $defaultExcludes = [
            'created_at',
            'updated_at',
            'deleted_at',
            'remember_token',
            'password',
        ];

        return array_merge($defaultExcludes, $this->auditExclude ?? []);
    }

    /**
     * Get display name for audit logs
     */
    public function getAuditDisplayName(): string
    {
        if (method_exists($this, 'getDisplayNameAttribute')) {
            return $this->display_name;
        }

        if (isset($this->name)) {
            return $this->name;
        }

        if (isset($this->email)) {
            return $this->email;
        }

        if (isset($this->code) && isset($this->name)) {
            return "{$this->code} - {$this->name}";
        }

        return class_basename($this) . ' #' . substr($this->getKey(), 0, 8);
    }
}
