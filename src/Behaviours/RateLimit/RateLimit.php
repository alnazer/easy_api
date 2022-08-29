<?php
/**
 * @pakages    :easy_api
 * @auther     :https://github.com/alnazer
 * @created_at :28,August 2022 11:19 PM
 * @filename   :RateLimit.php
 **/

/*
|--------------------------------------------------------------------------
| Class title
|--------------------------------------------------------------------------
|
| Description Here
|
*/

namespace Alnazer\Easyapi\Behaviours\RateLimit;

use Alnazer\Easyapi\Behaviours\BehaviourInterface;
use Alnazer\Easyapi\Database\Query;
use Alnazer\Easyapi\Database\Schema;
use Alnazer\Easyapi\System\Application;
use Exception;

class RateLimit implements BehaviourInterface
{
    public string $tableName = "rate_limiting";
    public Query $query;
    public Schema $schema;
    public int $requestCount = 3;
    public int $everySecond = 5;
    public int $blockSecond = 10;
    public bool $allowDisplayInHeader = false;
    private $blockStillWaiting = 0;
    
    /**
     * @throws Exception
     */
    public function __construct()
    {
        // create table is not exist
        $this->schema = new Schema();
        $this->schema->db =Application::$app->db;
        $tableExists = $this->schema->isTableExist($this->tableName);
        if($tableExists === false){
            $query = "CREATE TABLE IF NOT EXISTS $this->tableName(
                    id int( 11 )  PRIMARY KEY auto_increment,
                    user_id int( 11) NOT NULL,
                    path VARCHAR (255) NOT NULL,
                    request_time int(11) NOT NULL,
                    block_time int(11) NULL,
                    attempts int(11) NOT NULL);";
            $this->schema->createTable($this->tableName,$query);
        }
        $this->query = new Query();
        $this->query->tableName = $this->tableName;
    
        $this->blockStillWaiting = ($this->blockStillWaiting > 0) ?  $this->blockStillWaiting : $this->blockSecond;
    }
    
    /**
     * @throws Exception
     */
    public function execute()
    {
        
        if(Application::$app->auth){
            $exist = $this->query->where(["user_id" => Application::$app->auth->user()->id])->first();
            if($exist){
                $this->stopIsRateLimiter($exist);
            }else{
                $this->insertRateTime();
            }
        }
    }
    
    /**
     * @throws Exception
     */
    private function stopIsRateLimiter($exist)
    {
        if (Application::$app->auth instanceof UserRateLimitInterface) {
            $this->requestCount = Application::$app->auth->requestCount();
            $this->everySecond = Application::$app->auth->everySecond();
        }
        
        if($exist['block_time'] && $exist['block_time'] > time()){
            $this->blockStillWaiting = $exist['block_time'] - time();
            $this->setHttpHeader();
            throw new Exception("Too Many Requests",429);
        }else{
            $this->updateRateTime(0);
        }
        $period = time() - $exist['request_time'];
        if($exist['attempts'] > $this->requestCount && $period < $this->everySecond){
            // update block time
            if(!$exist['block_time']){
                $block_time = time()+$this->blockSecond;
                $this->updateRateBlockTime($block_time);
            }
            $this->setHttpHeader();
            throw new Exception("Too Many Requests",429);
        }else{
            //attempt + 1 every time
            $attempts = $exist['attempts']+1;
            $this->updateRateTime($attempts);
            // reset block time
            $this->updateRateBlockTime(0);
        }

        
    }
    
    private function updateRateTime($attempts)
    {
        $this->query->update(
            ["request_time" => time(),'attempts' => $attempts],
            ["user_id" =>Application::$app->auth->user()->id]
        );
    }
    private function updateRateBlockTime($block_time)
    {
        $this->query->update(
            ['block_time'=> $block_time],
            ["user_id" =>Application::$app->auth->user()->id]
        );
    }
    private function insertRateTime()
    {
        $this->query->insert([
            "user_id" => Application::$app->auth->user()->id,
            "path" => Application::$app->action,
            "request_time" => time(),
            'attempts' => 0,
        ]);
    }
    
    private function setHttpHeader()
    {
        if($this->allowDisplayInHeader){
            Application::$app->response->setHeader("X-Rate-Limit-Limit",$this->requestCount);
            Application::$app->response->setHeader("X-Rate-Limit-Remaining",$this->everySecond);
            Application::$app->response->setHeader("X-Rate-Limit-Reset",$this->blockStillWaiting);
        }

    }
}