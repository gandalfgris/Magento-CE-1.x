<?php

require_once 'abstract.php';

class MDN_Shell_AntidotExport extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        
        $start = time();
        
        echo PHP_EOL.PHP_EOL;
        echo PHP_EOL.date('H:i:s')." : Start antidot AFS@Store push";
        echo PHP_EOL.date('H:i:s')." : Start products";
        Mage::getModel('Antidot/Observer')->catalogFullExport();
        echo PHP_EOL.date('H:i:s')." : Start categories";
        Mage::getModel('Antidot/Observer')->categoriesFullExport();
        
        //display last logs from start date
        $logs = Mage::helper('Antidot/LogExport')->getAllLastGeneration(4);
        foreach($logs as $log)
        {
            if (strtotime($log['begin_at']) >= $start)
            {
                echo PHP_EOL." > ".$log['reference']." - ".$log['element'].' - '.$log['status'].' : '.$log['error'];
            }
        }
        
        echo PHP_EOL.date('H:i:s')." : COMPLETE";
        echo PHP_EOL.PHP_EOL;
        
        return true;
    }
}

$shell = new MDN_Shell_AntidotExport();
$shell->run();
