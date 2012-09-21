<?php
/**
 * @author alari
 * @since 8/23/12 4:52 PM
 */
class ProcessMailQueueCommand extends CConsoleCommand
{
    public function run(array $args) {
        echo join("\n", Yii::app()->getModule("emailSender")->processQueue($this));
        echo "\n";
    }
}
