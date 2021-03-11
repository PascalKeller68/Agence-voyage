<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en « wp-config.php » et remplir les
 * valeurs.
 *
 * Ce fichier contient les réglages de configuration suivants :
 *
 * Réglages MySQL
 * Préfixe de table
 * Clés secrètes
 * Langue utilisée
 * ABSPATH
 *
 * @link https://fr.wordpress.org/support/article/editing-wp-config-php/.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'agence' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'root' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', '' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'localhost' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/**
 * Type de collation de la base de données.
 * N’y touchez que si vous savez ce que vous faites.
 */
define( 'DB_COLLATE', '' );

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clés secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'e?b&kwWc7r]WmR.bOWeu?IGfk<NBrU1|%`No=~wq2J..J)YPkD<y`|C^S9]TN&?M' );
define( 'SECURE_AUTH_KEY',  'mqI=Vmb~vp T=HX9i,RXXO|-8).~t1ZN;H2P8,r9!<eq],V?ATBNY/RB}m}sjD3U' );
define( 'LOGGED_IN_KEY',    'sY2uY!AhfmvV&<&Z*YKe(5%w:__:03Xo[+Spvk8q8sr%~N^jEqzTLr6$0MfYvx%h' );
define( 'NONCE_KEY',        '%wcfez`$t_dRMMFm!m#&z4m#?.gmHL8~+-y18FzZl>g(bEBC,9&(+24@=(]zav*d' );
define( 'AUTH_SALT',        '-)_s(M+W>y%t9,tMfo3GbQvxKhr#Wuwn,IcY*!5B!~IT 7fcNw)DRdWQ~zF=1kpy' );
define( 'SECURE_AUTH_SALT', '~b>nRjQMRh}3d}Tj_{q@$` ^VEd(D)m 6h/(kZ*|>=R:OR3a;7^ae_Zo6yL_6F-`' );
define( 'LOGGED_IN_SALT',   'RlJb +{eV[Tv$CiYoavBDbs^lA4(jz}5yw8!:|Tmv<1^}IUEqv_Qwdwj%z~nDsH8' );
define( 'NONCE_SALT',       'Fw7`A{Sn#8oXhX$atsMzC7!~E@MdDJru3;7A=10G49H$cPoPwJ{tM;fs/f/I_,_=' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortement recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://fr.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( ! defined( 'ABSPATH' ) )
  define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once( ABSPATH . 'wp-settings.php' );
