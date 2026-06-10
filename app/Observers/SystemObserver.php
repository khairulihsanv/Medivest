<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class SystemObserver
{
    protected function logAction($model, string $action)
    {
        AuditLog::create([
            'id_user' => Auth::id(),
            'activity' => $action . ' ' . class_basename($model),
            'target_table' => $model->getTable(),
        ]);
    }

    public function created($model)
    {
        $this->logAction($model, 'Created');
    }

    public function updated($model)
    {
        $this->logAction($model, 'Updated');
    }

    public function deleted($model)
    {
        $this->logAction($model, 'Soft Deleted');
    }

    public function restored($model)
    {
        $this->logAction($model, 'Restored');
    }

    public function forceDeleted($model)
    {
        $this->logAction($model, 'Force Deleted');
    }
}
