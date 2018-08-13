<?php

namespace YMKatz\CAS\Traits;

use Adldap\Models\RootDse;
use Adldap\Schemas\ActiveDirectory;
use Adldap\Schemas\SchemaInterface;
use Adldap\Connections\ConnectionInterface;
/**
 * Adldap2 Search Factory.
 *
 * Constructs new LDAP queries.
 *
 * @package Adldap\Search
 *
 * @mixin Builder
 */
trait FactoryTraits
{
    /**
     * Returns a query builder limited to cas_services.
     *
     * @return Builder
     */
    public function cas_services()
    {
        return $this->where([
            $this->schema->objectClass() => "csdCasService",
        ]);
    }

    /**
     * Creates a new cas ticket instance.
     *
     * @param array $attributes
     *
     * @return User
     */
    public function cas_ticket(array $attributes = [])
    {
        $model = $this->schema->casTicketModel();

        return (new $model($attributes, $this->query))
            ->setAttribute($this->schema->objectClass(), [
                $this->schema->top(),
                "csdCasTicket",
            ]);
    }

}
