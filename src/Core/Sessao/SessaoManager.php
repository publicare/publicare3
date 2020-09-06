<?php
namespace Pbl\Core\Sessao;

use Pbl\Core\Base;

/**
 * Sessao Manager
 *
 * @package  Pbl\Core\Sessao
 */
class SessaoManager extends Base
{
    /**
     * Event names
     *
     * @var string
     */
    const EVENT_DESTROY = 'session.destroy';

    /**
     * Return true if the session is open
     *
     * @static
     * @access public
     * @return boolean
     */
    public static function isOpen()
    {
        return session_id() !== '';
    }

    public function iniciar()
    {
        // session_set_save_handler(new SessionHandler($this->db), true);
        if (ini_get('session.auto_start') == 1) {
            session_destroy();
        }

        // nome da sessao
        session_name('PBL_SID');
        // utilizando cookies
        ini_set("session.use_cookies", true);
        // apenas cookies
        ini_set("session.use_only_cookies", true);
        // nao transmitir sid pela url
        ini_set('session.use_trans_sid', false);
        // permite apenas sessoes inicializadas por aqui
        ini_set("session.use_strict_mode", true);
        // bloqueia acesso ao cookie de sessao por scripts
        ini_set("session.cookie_httponly", true);

        ini_set('session.hash_function', '1'); // 'sha512' is not compatible with FreeBSD, only MD5 '0' and SHA-1 '1' seems to work
        ini_set('session.hash_bits_per_character', 6);
        
        session_start();
    }

    /**
     * Destroy the session
     *
     * @access public
     */
    public function close()
    {
        // Destroy the session cookie
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );

        session_unset();
        session_destroy();
    }

}
