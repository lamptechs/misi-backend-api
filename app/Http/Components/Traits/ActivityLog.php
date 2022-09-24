<?php

namespace App\Http\Components\Traits;

use App\ActivityLog as AppActivityLog;
use Exception;

trait ActivityLog{
    /**
     * Add Admin Activity Log
     */
    protected function saveActivity($request, $activity, $model = null ){
        try{
            $admin = $request->user("admin");
            $advisor = $request->user();
            $activity_log = new AppActivityLog();
            $name = "";
            if(isset($advisor) && $advisor->getTable() == "advisors"){
                $name = $advisor->first_name;
                $activity_log->advisor_id = $advisor->id;
            }
            if(isset($admin) && $admin->getTable() == "admins"){
                $name = $admin->name;
                $activity_log->admin_id = $admin->id;
            }
            $activity_log->ip = $request->ip();
            //$activity = trim($activity, "open");
            //$activity = trim($activity, "Open");
            $activity = $activity;
            $activity = ucfirst(strtolower($activity));
            $activity_log->activity = $activity . ' by ' . $name;
            if( isset($model->id) ){
                $activity_log->effect_table = $model->getTable();
                $activity_log->effect_data_id = $model->id ?? "";
            }
            $activity_log->save();
        }catch(Exception $e){
            //
        }
    }
}