<?php

namespace App\Http\Components\Traits;

use App\Models\UserActivity;
use Exception;


trait ActivityLog{
    /**
     * Add Admin Activity Log
     * @param $mode Must Be model Instance OF Data
     * @param $user Must Be model Instance OF User 
     */
    protected function saveActivity($request, $model, $user, $activity){
        try{
            $activity = new UserActivity();
            $activity->ip               = $request->ip();
            $activity->tableable_type   = $model->getMorphClass();
            $activity->tableable_id     = $model->id;
            $activity->userable_type    = $user->getMorphClass();
            $activity->userable_id      = $user->getMorphClass();
            $activity = $activity;
            $activity->save();
        }catch(Exception $e){
            //
        }
    }
}
