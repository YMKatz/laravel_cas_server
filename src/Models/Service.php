<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/1
 * Time: 15:06
 */

namespace YMKatz\CAS\Models;

use Adldap\Models\Model;

/**
 * Class Service
 * @package YMKatz\CAS\Models
 *
 * @property string  $name
 * @property boolean $allow_proxy
 * @property boolean $enabled
 */
class Service extends Model
{
    protected $fillable = ['name', 'enabled', 'allow_proxy'];
    protected $casts = [
        'enabled'     => 'boolean',
        'allow_proxy' => 'boolean',
    ];

    public function hosts()
    {
        return $this->hasMany(ServiceHost::class);
    }

    /**
     * Returns true / false if the service is enabled
     *
     * @return null|bool
     */
    public function getEnabled()
    {
        return $this->convertStringToBool(
            $this->getFirstAttribute(
                "enabled"
            )
        );
    }
}
