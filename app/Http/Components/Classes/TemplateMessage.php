<?php

namespace App\Http\Components\Classes;

use Exception;

class TemplateMessage{
    /**
     * @var $model Model Instance
     * @var $message
     */
    protected $model;
    protected $message;

    public function model($model){
        $this->model = $model;
    }

    /**
     * Converting The message
     * @param String $message
     * @return String
     */
    public function parse(String $message, $model = null) : String
    {
        if(!empty($model)){
            $this->model($model);
        }
        $this->message = $message;
        $this->prepareMessage();

        return $this->message;
    }

    /**
     * Prepare the Message with Message Template
     */
    protected function prepareMessage()
    {
        try{
            if( isset($this->model->first_name) ){
                $this->message = str_replace("{first_name}", $this->model->first_name, $this->message);
            }
            if( isset($this->model->last_name) ){
                $this->message = str_replace("{last_name}", $this->model->last_name, $this->message);
            }
            if( isset($this->model->name) ){
                $this->message = str_replace("{name}", $this->model->name, $this->message);
            }
            if( isset($this->model->id) ){
                $this->message = str_replace("{ticket_no}", $this->model->id, $this->message);
            }
            if( isset($this->model->id) ){
                $this->message = str_replace("{appointment_no}", $this->model->id, $this->message);
            }
            if( isset($this->model->date) ){
                $this->message = str_replace("{appointment_date}", $this->model->date, $this->message);
            }
            if( isset($this->model->fee) ){
                $this->message = str_replace("{appointment_fee}", $this->model->fee, $this->message);
            }
            if( isset($this->model->address) ){
                $this->message = str_replace("{patient_address}", $this->model->address, $this->message);
            }
            if( isset($this->model->address) ){
                $this->message = str_replace("{therapist_address}", $this->model->address, $this->message);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(). ' On File'.$e->getFile().":". $e->getLine(), 500);
        }
    }
}