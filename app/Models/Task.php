<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Task extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'description', 'due_date', 'status'];
    protected $guarded = [];

    public function __construct(array $fillable = [])
    {
        parent::__construct($fillable);
    }

    public function scopeDueSoon($task)
    {
        return $task->where('due_date', '<=', Carbon::now()->addDays(2));
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDueDate(): string
    {
        return $this->due_date;
    }

    public function setDueDate(string $due_date): void
    {
        $this->due_date = $due_date;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function files(): HasOne
    {
        return $this->hasOne(File::class);
    }

    public function notifications()
    {
    return $this->hasMany('App\Notification');
    }
}
