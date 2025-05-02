<?php

class cupCalculation
{

    function calculate($winnerPoint, $loserPoint)
    {
        $pointDiff = abs($winnerPoint - $loserPoint); // abs :  mutlak değerde
        $baseCup = 10;

        if ($winnerPoint < $loserPoint) 
        {
            $cupGain = $baseCup + intval($pointDiff / 10);
            $cupLose = $baseCup - intval($pointDiff / 20);
        } 
        else 
        {
            $cupGain = $baseCup - intval($pointDiff / 20);
            $cupLose = $baseCup + intval($pointDiff / 10);
        }
        // kupa kazanımı ve kaybının minimum 5, maksimum 30 arasında sınırlandırılması
        $cupGain = max(5, min(30, $cupGain));
        $cupLose = max(5, min(30, $cupLose));

        return ["cupGain" => $cupGain, "cupLose" => $cupLose];

    }




}
?>