<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'date_of_birth',
        'age',
        'door_number',
        'aadhar_number',
        'phone_number',
        'is_student',
        'is_employed',
        'father_id',
        'mother_id',
        'marital_status',
        'blood_group',
        'disability_status',
        'voter_id',
        'ration_card_number',
        'is_monthly_saving_scheme_member',
        'monthly_saving_amount',
        'last_payment_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'is_student' => 'boolean',
            'is_employed' => 'boolean',
            'is_monthly_saving_scheme_member' => 'boolean',
            'monthly_saving_amount' => 'decimal:2',
            'last_payment_date' => 'date',
        ];
    }

    // Relationships
    public function father()
    {
        return $this->belongsTo(User::class, 'father_id');
    }

    public function mother()
    {
        return $this->belongsTo(User::class, 'mother_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'father_id')->orWhere('mother_id', $this->id);
    }

    public function education()
    {
        return $this->hasOne(Education::class);
    }

    public function occupation()
    {
        return $this->hasOne(Occupation::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }
        return $this->roles->contains($role);
    }

    public function hasPermission($permission): bool
    {
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permission)) {
                return true;
            }
        }
        return false;
    }

    public function assignRole($role): void
    {
        if (is_string($role)) {
            $role = Role::whereName($role)->firstOrFail();
        }
        $this->roles()->syncWithoutDetaching($role);
    }

    public function removeRole($role): void
    {
        if (is_string($role)) {
            $role = Role::whereName($role)->firstOrFail();
        }
        $this->roles()->detach($role);
    }

    public function monthlySchemeTransactions()
    {
        return $this->hasMany(FinanceTransaction::class, 'member_id')
            ->whereHas('category', function ($query) {
                $query->where('name', 'Monthly Saving Scheme');
            })
            ->where('type', 'income');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }

    public function studentDetails()
    {
        return $this->hasOne(StudentDetail::class);
    }
}
