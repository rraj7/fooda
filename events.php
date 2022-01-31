<?php

class Events{
    
    private $data;

    public function __construct(){
        $this->data = json_decode(file_get_contents("data.json"));
    }

    public function getNewCustomer() {
        $customer = [];
        foreach ($this->data->events as $val){
            if ($val->action == "new_customer"){
                $customer[] = $val->name;
            }
        }
        return $customer;
    }

    public function getNewOrder(){
        $orders = [];
        foreach ($this->data->events as $val){
            if ($val->action == "new_order"){
                $orders[] = ["name" =>$val->customer, "amount" => $val->amount];
            }
        }
        return $orders;
    }

    public function getRewardsPoint() {
    
        $timestampArray = [];
        $time = [];
        foreach ($this->data->events as $val) {
            if ($val->action == "new_order"){
                $timestampArray[] = $val->timestamp;
            }
        }
        for($i=0;$i<count($timestampArray);$i++){
            $temp= explode(" ",str_replace("T"," ",$timestampArray[$i]));
            $time[]= $temp[1];
            
        }
        return $time;
    }
}

$event = new Events; 
//var_export($event->getNewCustomer());
//var_export($event->getNewOrder());
var_export($event->getRewardsPoint());
?>