<?php
    namespace Techy\Lib\Core\Service\Redis;

    use Techy\Lib\Core\System\Application;

    class Queue extends Base implements \Techy\Lib\Core\Service\I\IQueue {

        public function __construct( $name, $spotId = 1 ){
            parent::__construct( $name, $spotId );

            $this->name = $spotId. '_queue_'. $name;
        }

        public function push( $data, $options = array()){
            try {
                $this->client()->rpush( $this->name, $data );
                return true;
            }
            catch( \Exception $E ){
                Application::instance()->getLogger()->logException( $E );
                return false;
            }
        }

        public function pushStack( $item ){
            try {
                $args = func_get_args();
                $items = array( $this->name );
                foreach( $args as $item )
                    $items[] = $item['data'];
                call_user_func_array( array( $this->client(), 'rpush' ), $items );
                return true;
            }
            catch( \Exception $E ){
                Application::instance()->getLogger()->logException( $E );
                return false;
            }
        }

        public function pull( $count = 1 ){
            try {
                $load = array();
                while( $count-- ){
                    $val = $this->client()->lpop( $this->name );
                    if(!$val )
                        break;
                    $load[] = $val;
                }
                return $load;
            }
            catch( \Exception $E ){
                Application::instance()->getLogger()->logException( $E );
                return false;
            }
        }

        public function len(){
            try {
                return $this->client()->llen( $this->name );
            }
            catch( \Exception $E ){
                Application::instance()->getLogger()->logException( $E );
                return false;
            }
        }
    }
