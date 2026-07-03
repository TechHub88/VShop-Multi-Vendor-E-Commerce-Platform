<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class InstitutionDetail extends Model
{
     use HasFactory;

  protected $fillable = [
    'user_id', 'type', 'institution_name',
    'board', 'medium', 'student_strength', 'uniform_pattern',
    'doctor_count', 'nurse_count', 'staff_uniform_type',
    'gst_number', 'distribution_area',
    'company', 'staff_count',
    'city', 'area', 'clothing_requirement','organization_name','role','file','address','pincode',

    // Added contact fields
    'name', 'email', 'phone', 'password',
];


    
    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

}

