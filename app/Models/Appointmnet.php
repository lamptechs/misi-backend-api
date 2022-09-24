<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointmnet extends Model
{
    use HasFactory;
    public function createdBy(){
        return $this->belongsTo(Admin::class, "created_by")->withTrashed();
    }
    public function updatedBy(){
        return $this->belongsTo(Admin::class, "updated_by")->withTrashed();
    }
    public function patient(){
       
        return $this->belongsTo(User::class, 'patient_id');
        
    }
    public function therapist(){
       
        return $this->belongsTo(Therapist::class, 'therapist_id');
        
    }
    public function schedule(){
       
        return $this->belongsTo(TherapistSchedule::class, 'therapist_schedule_id');
        
    }
    // protected $casts = [
    //     'time' => 'time:H:i:m',
    //     'date' => 'date:d/m/Y',
    // ];
}
