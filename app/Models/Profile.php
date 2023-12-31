<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;


class Profile extends Model
{
    use HasFactory;

    protected $with = ['payment'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id','reference_by','reference_phone','first_name','last_name','phone','marital_status','gender',
        'spouse_first_name','spouse_last_name','spouse_email','spouse_phone','family_members',
        'address_line_1','address_line_2','city','state','metro_area','zip_code','country',
        'membership_category', 'payment_mode',
        'status',"india_state", "india_city"
    ];

    protected $casts = [
        'family_members' => 'array',
    ];

    public function membershipCategory()
    {
        return $this->hasOne(MembershipCategory::class,'id','membership_category');
    }

    public function payment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'for')->orderBy("created_at","DESC");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
