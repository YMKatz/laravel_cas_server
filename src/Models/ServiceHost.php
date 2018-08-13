<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/1
 * Time: 15:17
 */

namespace YMKatz\CAS\Models;

use Adldap\Models\Model;

/**
 * Class ServiceHost
 * @package YMKatz\CAS\Models
 *
 * @property integer $service_id
 * @property Service $service
 */
class ServiceHost extends Model
{
    public function service()
    {
    	$service_dn = $this->getFirstAttribute("casservice");
    	return $this->query->newInstance()->findByDn($service_dn);
    }
}
