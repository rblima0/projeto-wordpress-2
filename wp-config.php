<?php
/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa user o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/pt-br:Editando_wp-config.php
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar estas informações
// com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'wp_dev');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'root');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', '');

/** Nome do host do MySQL */
define('DB_HOST', 'localhost');

/** Charset do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8mb4');

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para desvalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '7Fe+ds@.-Sf=5e8!s:k6,q$J,cvE>jDdG1Ah#b0lNZ7@K|]T&5),2,vo5~72/b_>');
define('SECURE_AUTH_KEY',  'oxw5$RnGbWdc[Ox,NwI_wqokp5aH:T*i$2K`ZQral5~Oz>e/(4z<5B)5uZe`O![U');
define('LOGGED_IN_KEY',    'zbwWy2YRv+$PlaN&`#4-0v;7f<uW#T7~Fd*6Y0TI.GL@v~K1%/1U6cCfggzDZ6gp');
define('NONCE_KEY',        '^E-!nlb43$`^ju`8!^y2mr@tme,%`3tC+Gjw;(/>@IXVm*iLAqbAT)(ol$pF6k8q');
define('AUTH_SALT',        'bZ2*g`ar%Dcko8DlP5j{!e6ee:s})8 #<Toos^)bMO:L8#LV&j[_1m>5KZ+_>tX*');
define('SECURE_AUTH_SALT', 'xj:dI1BpvBw=(TS%Qz!)P-~=6k!NSue@I_442sra.F:z(3<sd<N_t&F[?;1jdSR/');
define('LOGGED_IN_SALT',   'f[h/NET8P@O;Ty9W/9OCZODHH!noj:8T%r>aoxRZ0v}nag:y%=nK/:5t_(nDVxA1');
define('NONCE_SALT',       '-_V:,(*z0HsZJacx9HQV7.rGl ]N.R&BT#*{Rd$4AeC^,klX*7lP~5^=9?y#vp-&');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * para cada um um único prefixo. Somente números, letras e sublinhados!
 */
$table_prefix  = 'wp_';

/**
 * Para desenvolvedores: Modo debugging WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://codex.wordpress.org/pt-br:Depura%C3%A7%C3%A3o_no_WordPress
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Configura as variáveis e arquivos do WordPress. */
require_once(ABSPATH . 'wp-settings.php');
