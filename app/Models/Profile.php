<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

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
        'status',
    ];

    protected $casts = [
        'family_members' => 'array',
    ];

    public function membership_category_details()
    {
        return $this->hasOne(MembershipCategory::class,'id','membership_category');
    }

}
