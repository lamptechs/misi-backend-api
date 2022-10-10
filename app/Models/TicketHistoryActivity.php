<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketHistoryActivity extends Model
{
    use HasFactory;
    public function createdBy(){
        return $this->belongsTo(Admin::class, "created_by")->withTrashed();
    }
    public function updatedBy(){
        return $this->belongsTo(Admin::class, "updated_by")->withTrashed();
    }
    public function Ticket(){
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
    public function group(){
        return $this->belongsTo(Group::class, 'appointment_group ');
    }

}
