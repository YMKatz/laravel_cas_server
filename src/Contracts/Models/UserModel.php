<?php
/**
 * Created by PhpStorm.
 * User: leo108
 * Date: 2016/9/27
 * Time: 07:26
 */

namespace YMKatz\CAS\Contracts\Models;

use Adldap\Models\Model;

interface UserModel
{
    /**
     * Get user's name (should be unique in whole cas system)
     *
     * @return string
     */
    public function getName();

    /**
     * Get user's attributes
     *
     * @return array
     */
    public function getCASAttributes($requested_attributes);

    /**
     * @return Model
     */
    public function getLdapModel();
}
