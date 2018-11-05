<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/1
 * Time: 14:53
 */

namespace YMKatz\CAS\Models;

use Carbon\Carbon;
use Adldap\Models\Model;
use YMKatz\CAS\Contracts\Models\UserModel;

/**
 * Class Ticket
 * @package YMKatz\CAS\Models
 *
 * @property integer   $id
 * @property string    $ticket
 * @property string    $service_url
 * @property integer   $service_id
 * @property integer   $user_id
 * @property array     $proxies
 * @property Carbon    $created_at
 * @property Carbon    $expire_at
 * @property UserModel $user
 * @property Service   $service
 */
class Ticket extends Model
{
    protected $fillable = ['ticket', 'service_url', 'proxies', 'expireAt', 'createdAt'];
    protected $casts = [
        'expireAt'  => 'timestamp',
        'createdAt' => 'timestamp',
    ];

    public function getProxiesAttribute()
    {
        return json_decode($this->attributes['proxies'], true);
    }

    public function setProxiesAttribute($value)
    {
        //can not modify an existing record
        if ($this->id) {
            return;
        }
        $this->attributes['proxies'] = json_encode($value);
    }

    public function isExpired()
    {
        $t = new Carbon($this->getFirstAttribute('expire_at'));
        return $t->getTimestamp() < time();
    }

    public function getService()
    {
        $service_dn = $this->getFirstAttribute("casservice");
        return $this->query->newInstance()->findByDn($service_dn);
    }

    public function setService($dn)
    {
        if ($dn instanceof Service) {
            $dn = $dn->getDn();
        }
        
        return $this->setFirstAttribute('casservice', $dn);
    }

    public function getServiceUrl()
    {
        return $this->getFirstAttribute("casserviceurl");
    }

    public function getUser()
    {
        $user_dn = $this->getFirstAttribute("uid");
        return $this->query->newInstance()->findByDn($user_dn);
    }

    public function setUser(UserModel $user)
    {
        $user_dn = $user->dn[0];
        $this->uid = $user_dn;
    }

    public function setMeta($a)
    {
        $this->casMetaInfo = json_encode($a);
    }

    public function getMeta()
    {
        if (!empty($this->getFirstAttribute('casMetaInfo')))
        {
            return json_decode($this->getFirstAttribute('casMetaInfo'), true);
        }
        return [];
    }

    public function isProxy()
    {
        return !empty($this->proxies);
    }

    /**
     * Common Name is Ticket Number
     */
    public function getCommonName()
    {
        return $this->getFirstAttribute('casticket');
    }

    protected function getCreatableDn()
    {
        return sprintf("casTicket=%s,cn=cas_tickets,cn=login,cn=csd,%s", $this->getCommonName(), $this->query->getDn());
    }
}
