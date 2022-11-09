<?php

namespace App\Http\Components\Classes;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Permission{
    /**
     * Define All Permission
     */
    protected $all_access = [
            [
                "menu"      => "Admin", 
                "key"       => "admin", 
                "access"    => [
                    "admin_list"    => "Show Admin List", 
                    "admin_create"  => "Create New Admin", 
                    "admin_update"  => "Update Admin", 
                    "admin_delete"  => "Delete Admin", 
                    "admin_restore" => "Restore Admin",
                ],
            ],
            [
                "menu"      => "Group", 
                "key"       => "group", 
                "access"    => [
                    "group_list"        => "Show Group List", 
                    "group_create"      => "Create New Group", 
                    "group_update"      => "Update Group", 
                    "group_delete"      => "Delete Group",

                    "permission_list"    => "View Group Permission", 
                    "permission_create"  => "Add / Update Group Permission"
                ],
            ],
            [
                "menu"      => "Therapist", 
                "key"       => "therapist", 
                "access"    => [
                    "therapist_list"        => "Show Therapist List", 
                    "therapist_create"      => "Add New Therapist", 
                    "therapist_update"      => "Update Therapist Info", 
                    "therapist_delete"      => "Delete Therapist Info",
                ],
            ],
            [
                "menu"      => "Patient", 
                "key"       => "patient", 
                "access"    => [
                    "patient_list"        => "Show Patient List", 
                    "patient_create"      => "Add New Patient", 
                    "patient_update"      => "Update Patient Info", 
                    "patient_delete"      => "Delete Patient Info",
                ],
            ],
            [
                "menu"      => "ticket", 
                "key"       => "ticket", 
                "access"    => [
                    "ticket_list"                         => "Show Ticket List", 
                    "ticket_create"                       => "Add New Ticket", 
                    "ticket_update"                       => "Update Ticket Info", 
                    "ticket_delete"                       => "Delete Ticket Info",
                    "ticket_replyList"                    => "Ticket Replies Info",
                    "ticket_addReply"                     => "Ticket Replies Info Added",
                    "ticket_editReply"                    => "Ticket Show Replies Info",
                    "ticket_updateReply"                  => "Ticket Reply Updated Info",
                    "ticket_deleteReply"                  => "Ticket Reply Deleted Info",
                    "ticket_ticketHistoryActivity"        => "Tickethistory activities List",
                    "ticket_ticketHistoryActivityshow"    => "Tickethistory activities Show List",
                    "assignedticket"                      => "Ticket Assigned Successfully",
                    "cancelticket"                        => "Ticket Cancelled Successfully",
                    "deleteFileTicket"                    => "Ticket File Deleted Successfully",
                ],
            ],
            
    ];

    /**
     * Get All Available Permission List
     * @return Array
     */
    public function getAllPermission(){
        return $this->all_access;
    }

    /**
     * Check Access
     * @param $access_key
     * @return bool
     */
    public function checkAccess($access_key){
        $has_permission = false;
        if( !is_array($access_key) ){
            $access_key = (array) $access_key;
        }
        $user_access = $this->getUserAccess();
        $user_access = $this->getAccessKeys($user_access);
        
        foreach($access_key as $access){
            if( in_array($access, $user_access) ){
                $has_permission = true;
                break;
            }
        }
        return $has_permission;
    }

    /**
     * Set Authentic User Access
     */
    protected function setAuthUserAccess(){
        $admin = Auth::user("admin");
        Session::put("group_access", $admin->group->permission);
    }

    /**
     * Get Authentic User Access
     */
    protected function getUserAccess(){
        if( !Session::has("group_access") ){
            $this->setAuthUserAccess();
        }
        return Session::get("group_access");
    }


    /**
     * Get Access Key Array List
     * @return Array
     */
    public function getAccessKeys($user_access){
        $_access_arr = [];
        foreach($user_access as $key => $data){
            if($key == "access" && is_array($data)){
                foreach($data as $_key => $text)
                $_access_arr[] = $_key;
            }
        }
        return  $_access_arr;
    }
}