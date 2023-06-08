<?php

namespace ofilin\GeoIP;

use Yii;
use yii\base\Component;
use MaxMind\Db\Reader;


/**
 * Class GeoIP
 */
class GeoIP extends Component
{
    /**
     * @var string
     */
    public $dbPath;

    /**
     * @var string
     */
    public $lang = 'en';

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var array
     */
    private $result = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        $db = $this->dbPath ?: Yii::getAlias('@vendor/ofilin/maxmind-geolite2-database/city.mmdb');

        $this->reader = new Reader($db);

        parent::init();
    }

    /**
     * @param string|null $ip
     * @return Result
     */
    public function ip($ip = null)
    {
        if ($ip === null) {
            $ip = Yii::$app->request->getUserIP();
        }

        $key = self::class . ':' . $ip;
        $cache = Yii::$app->cache;
        $this->result[$ip] = $cache->getOrSet($key, function () use ($ip) {
            $result = $this->reader->get($ip);
            return new Result($result, $this->lang);
        }, 3600);

        return $this->result[$ip];
    }
}
