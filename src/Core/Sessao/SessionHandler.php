<?php
namespace Pbl\Core\Sessao;

use SessionHandlerInterface;

/**
 * Class SessionHandler
 *
 * @package Kanboard\Core\Session
 */
class SessaoHandler implements SessaoHandlerInterface
{
    public function __construct()
    {
    }

    public function close()
    {
        return true;
    }

    public function destroy($sessionID)
    {
        // return $this->db->table(self::TABLE)->eq('id', $sessionID)->remove();
    }

    public function gc($maxlifetime)
    {
        // return $this->db->table(self::TABLE)->lt('expire_at', time())->remove();
    }

    public function open($savePath, $name)
    {
        return true;
    }

    public function read($sessionID)
    {
        // $result = $this->db->table(self::TABLE)->eq('id', $sessionID)->findOneColumn('data');
        // return $result ?: '';
    }

    public function write($sessionID, $data)
    {
        $lifetime = time() + (ini_get('session.gc_maxlifetime') ?: 1440);

        // $this->db->startTransaction();

        // if ($this->db->table(self::TABLE)->eq('id', $sessionID)->exists()) {
        //     $this->db->table(self::TABLE)->eq('id', $sessionID)->update([
        //         'expire_at' => $lifetime,
        //         'data'      => $data,
        //     ]);
        // } else {
        //     $this->db->table(self::TABLE)->insert([
        //         'id'        => $sessionID,
        //         'expire_at' => $lifetime,
        //         'data'      => $data,
        //     ]);
        // }

        // $this->db->closeTransaction();

        return true;
    }
}
