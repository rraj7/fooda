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

    // public function 
    public function getRewardsPoint() {
    
        $timeArray = [];
        $rewardPointArray = [];
        foreach ($this->data->events as $val) {
            if ($val->action == "new_order"){
                $timeArray = array(getdate((strtotime(str_replace("T"," ",$val->timestamp)))));
    
                if ($timeArray[0]["hours"]=="12") {
                    $reward = round($val->amount/3);
                    $rewardPointArray[] = ["Name"=> $val->customer,"Rewards" => $reward];
                } 
                elseif($timeArray[0]["hours"]=="11" or $timeArray[0]["hours"] =="13") {
                    $reward = round($val->amount/2);
                    $rewardPointArray[] = ["Name"=> $val->customer,"Rewards" => $reward];
                } 
                elseif ($timeArray[0]["hours"]=="10" or $timeArray[0]["hours"]=="11") {
                    $reward = round($val->amount/1);
                    $rewardPointArray[] = ["Name"=> $val->customer,"Rewards" => $reward];
                } 
                else {
                    $rewardPointArray[] = ["Name"=> $val->customer,"Rewards" => "0"];
                }
            }
        }
        return $rewardPointArray;
    }

    function group_by($key, $data) {
        $result = array();
    
        foreach($data as $val) {
            if(array_key_exists($key, $val)){
                $result[$val[$key]][] = $val;
            }else{
                $result[""][] = $val;
            }
        }
    
        return $result;
    }

    //Helper Function to add and sort
    function addRewards($array){
        $names = array_column($array, 'Name');
        array_multisort($names, SORT_ASC, $array);
    
        $array_sorted = [];
        $seen_names = [];
    
        foreach($array as $entry){
            $seen = false;
            //check if name already processed
            foreach($seen_names as $name){
                if($entry['Name'] == $name){
                    $seen = true;
                    break;
                }
            }
            if($seen == true){
                continue;
            }
            $cur_entry = $entry;
            $cur_entry['Rewards'] = 0;
            $count = 0;
            foreach($array as $entryTwo){
                if($entryTwo['Name'] == $cur_entry['Name']){
                    $cur_entry['Rewards'] += $entryTwo['Rewards'];
                    $count++;
                }
            }
            $cur_entry["Entries"] = $count;
            $array_sorted[] = $cur_entry;
            $seen_names[] = $entry['Name'];
        }
        return $array_sorted;
    }

    public function calculateRewards(){
        $result = [];
        $rewardsArray = $this->getRewardsPoint();
        for ($i=0; $i< count($rewardsArray); $i++){
            if ($rewardsArray[$i]["Rewards"]< 3 or $rewardsArray[$i]["Rewards"] > 20){
                $rewardsArray[$i]["Rewards"] = 0;
            }
        }
        $resultToBeFormatted = $this->addRewards($rewardsArray);
        foreach ($resultToBeFormatted as $key=>&$val){
            $result[] = [$key+1 =>$val["Name"].": ".$val["Rewards"]." points with ".$val["Entries"]." per order."];
            var_dump($result);
        }
        return $result;
    }

    // Create the results file 
    public function printRewardsReport(){
        $result = $this->calculateRewards();
        $fp = fopen('results.json',"w");
        fwrite($fp, json_encode($result));
        fclose($fp);
    }
}

$event = new Events; 
$event->printRewardsReport();
?>