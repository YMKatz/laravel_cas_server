<?php
/**
 * Created by PhpStorm.
 * User: leo108
 * Date: 16/9/17
 * Time: 20:13
 */

namespace YMKatz\CAS\Repositories;

use Adldap\AdldapInterface;
use YMKatz\CAS\Models\Service;
use YMKatz\CAS\Models\ServiceHost;

class ServiceRepository
{

    /**
     * @var Adldap
     */
    protected $ldap;
    protected $connection;
    
    /**
     * Constructor.
     *
     * @param AdldapInterface $adldap
     */
    public function __construct(AdldapInterface $ldap)
    {
        $this->ldap = $ldap;
        $this->connection = config('cas.ldap.connection');
    }

    /**
     * @param $url
     * @return Service|null
     */
    public function getServiceByUrl($url)
    {
        $host = parse_url($url, PHP_URL_HOST);

        $l = $this->getLdapConnection();

        $service_hosts = $l->search()->where(["objectclass" => "csdCasServiceHost",])->where('host', $host)->get();

        if (count($service_hosts) > 1){
            throw new \Exception("Multiple services on one host are not implmented yet.");
        }

        $record = $service_hosts->first();

        if (!$record) {
            return null;
        }

        return $record->service();
    }

    /**
     * @param $url
     * @return bool
     */
    public function isUrlValid($url)
    {
        $service = $this->getServiceByUrl($url);
        return $service !== null && $service->enabled;
    }

    private function getLdapConnection()
    {
        return $this->ldap->connect($this->connection);
    }
}
