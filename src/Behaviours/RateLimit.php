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

namespace Alnazer\Easyapi\Behaviours;

use Alnazer\Easyapi\Database\Query;
use Alnazer\Easyapi\Database\Schema;
use Alnazer\Easyapi\System\Application;

class RateLimit implements BehaviourInterface
{
    public $tableName = "rate_limiting";
    public $query;
    public Schema $schema;
    public $user;
    public $requestCount = 1;
    public $perSecound = 60;

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
                    start_update int(11) NOT NULL,
                    attempts int(11) NOT NULL);";
            $this->schema->createTable($this->tableName,$query);
        }
        $this->query = new Query();
        $this->query->tableName = $this->tableName;
    }

    public function execute()
    {

        if(Application::$app->user){
            $exist = $this->query->where(["user_id" => Application::$app->user->id,"path" => Application::$app->action,])->first();
            if($exist){

                $this->query->update(
                    ["start_update" => time(),'attempts' => $exist['attempts']+1],
                    ["user_id" => Application::$app->user->id, "path" => Application::$app->action ]
                );
                $this->stopIsRateLimiter($exist);
            }else{
                $this->query->insert([
                    "user_id" => Application::$app->user->id,
                    "path" => Application::$app->action,
                    "start_update" => time(),
                    'attempts' => 1,
                ]);
            }
        }
    }

    private function stopIsRateLimiter($exist)
    {
        pd($exist);
    }
}