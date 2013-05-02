<?php

// Make sure application context and database context are defined
// that way this part can only be included in context
if (!$app || !$em) die();

class Users {
    protected static $_app;
    protected static $_em;

    public static function setApp($app) { self::$_app = $app; }
    public static function setEm($em) { self::$_em = $em; }

    /**
     * Will retrieve login data from a post request (username and password fields)
     * and check if the user exists and has the correct password. If that is the case
     * he/she will be logged in and the correct session variables created. Else well not
     * logged in. In case a user is already logged in this method will instantly stop.
     *
     * @return bool Wether the login was successful (true) or not (false)
     **/
    public static function login() {
        // if we are already logged in... fail here
        if (self::isLoggedIn()) return false;

        $req      = self::$_app->request();
        $username = $req->post('username');
        $password = $req->post('password');
        // If not defined, well here we leave...
        if (!$username || !$password) return false;

        // Now find that user
        $user = self::$_em->getRepository('Entities\User')->findOneBy(array('name' => $username));
        if (!$user) return false;

        // Check if the stored password and the actually provided password are identical
        // We just use a simple md5 hashing here... this could actually be attacked with a rainbow
        // table. So salting would be appropriate for real world usage
        if ($user->getPassword() != md5($password)) return false;

        // Well that seemed fine, let's log that one in...
        $_SESSION['username'] = $user->getName();
        $_SESSION['userid']   = $user->getId();

        return true;
    }

    /**
     * Logout a logged in user
     *
     * @return Wether the logout was successful (true) or not (false) 
     **/
    public static function logout() {
        if (!self::isLoggedIn()) return false;
        unset($_SESSION['username']);
        unset($_SESSION['userid']);

        return true;
    }

    /**
     * Checks if a user is currently logged in and the credentials stored in the session
     * match with the actual database entry
     *
     * @return bool Wether the user is logged in or not
     **/
    public static function isLoggedIn() {
        // First check if the session entries even exist
        if (!isset($_SESSION['username']) || !isset($_SESSION['userid'])) return false;

        // Now we will also add a bit of validation (Username and id should at least match)
        $user = self::$_em->find('Entities\User', (int) $_SESSION['userid']);
        // If we did not even find the entry with the given id, stop here
        if (!$user) return false;
        // And as the final result check if the username matches the stored session name
        return $user->getName() == $_SESSION['username'];
    }

    /**
     * Function can be called from every request/route which would require
     * a logged in user. In case no user is logged in a 401 (Unauthorized)
     * status-code will be returned and further execution stopped
     *
     * @return void
     **/
    public static function requireLoggedin() {
        if (!self::isLoggedIn()) {
            // Not logged in, so we define this as unauthorized
            self::$_app->halt(403, 'You shall not pass!');
        }
    }
}

// And assign those parts to the static internal holders
Users::setApp($app);
Users::setEm($em);
