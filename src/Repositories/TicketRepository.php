<?php
/**
 * Created by PhpStorm.
 * User: leo108
 * Date: 16/9/17
 * Time: 20:19
 */

namespace YMKatz\CAS\Repositories;

use Adldap\AdldapInterface;
use Carbon\Carbon;
use YMKatz\CAS\Contracts\Models\UserModel;
use YMKatz\CAS\Exceptions\CAS\CasException;
use YMKatz\CAS\Models\Ticket;
use YMKatz\CAS\Services\TicketGenerator;

class TicketRepository
{
    /**
     * @var Adldap
     */
    protected $ldap;
    protected $connection;
    
    /**
     * @var ServiceRepository
     */
    protected $serviceRepository;

    /**
     * @var TicketGenerator
     */
    protected $ticketGenerator;

    /**
     * TicketRepository constructor.
     * @param Ticket            $ticket
     * @param ServiceRepository $serviceRepository
     * @param TicketGenerator   $ticketGenerator
     */
    public function __construct(AdldapInterface $ldap, ServiceRepository $serviceRepository, TicketGenerator $ticketGenerator)
    {
        $this->ldap = $ldap;
        $this->connection = config('cas.ldap.connection');

        $this->serviceRepository = $serviceRepository;
        $this->ticketGenerator   = $ticketGenerator;
    }

    /**
     * @param UserModel $user
     * @param string    $serviceUrl
     * @param array     $proxies
     * @throws CasException
     * @return Ticket
     */
    public function applyTicket(UserModel $user, $serviceUrl, $proxies = [])
    {
        $l = $this->getLdapConnection();

        $service = $this->serviceRepository->getServiceByUrl($serviceUrl);
        if (!$service) {
            throw new CasException(CasException::INVALID_SERVICE);
        }
        $ticket = $this->getAvailableTicket(config('cas.ticket_len', 32), empty($proxies) ? 'ST-' : 'PT-');
        if ($ticket === false) {
            throw new CasException(CasException::INTERNAL_ERROR, 'apply ticket failed');
        }
        $record = $l->make()->cas_ticket(
            [
                'casTicket'     => $ticket,
                'expireAt'      => (new Carbon(sprintf('+%dsec', config('cas.ticket_expire', 300))))->format('YmdHisO'),
                //'created_at'    => new Carbon(),
                'casServiceUrl' => $serviceUrl,
                //'proxies'       => $proxies,
            ]
        );
        $record->setUser($user->getLdapModel());
        $record->setService($service);

        $record->setMeta(session('meta'));

        $record->save();

        return $record;
    }

    /**
     * @param string $ticket
     * @param bool   $checkExpired
     * @return null|Ticket
     */
    public function getByTicket($ticket, $checkExpired = true)
    {
        $l = $this->getLdapConnection();

        $records = $l->search()->where(["objectclass" => "csdCasTicket",])->where('casTicket', $ticket)->get();

        if (count($records) > 1){
            throw new CasException(CasException::INTERNAL_ERROR, 'ticket unique error');
        }
        if (count($records) === 0) {
            return null;
        }
        $record = $records->first();

        return ($checkExpired && $record->isExpired()) ? null : $record;
    }

    /**
     * @param Ticket $ticket
     * @return bool|null
     */
    public function invalidTicket(Ticket $ticket)
    {
        return $ticket->delete();
    }

    /**
     * @param integer $totalLength
     * @param string  $prefix
     * @return string|false
     */
    protected function getAvailableTicket($totalLength, $prefix)
    {
        return $this->ticketGenerator->generate(
            $totalLength,
            $prefix,
            function ($ticket) {
                return is_null($this->getByTicket($ticket, false));
            },
            10
        );
    }

    private function getLdapConnection()
    {
        return $this->ldap->connect($this->connection);
    }

}
