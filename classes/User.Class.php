<?php
    require_once("includes/connect.inc.php");

    class User 
    {
        const NOBODY = 0;

        private $id;
        private $username;
        private $email;
        private $password;
        private $rankName;
        private $lastLogin;
        private $lastIP;
        private $registerDate;
        private $online;
        private $profilePicture;
        private $banned;

        function __construct($id, $username, $email, $password, $rankName, $lastLogin, $lastIP, $registerDate, $online, $profilePicture, $banned)
        {
            $this->id = (int) $id;
            $this->username = $username;
            $this->email = $email;
            $this->password = $password;
            $this->rankName = $rankName;
            $this->lastLogin = $lastLogin;
            $this->lastIP = $lastIP;
            $this->registerDate = $registerDate;
            $this->online = (bool) $online;
            $this->profilePicture = $profilePicture;
            $this->banned = (bool) $banned;
        }

		public function setId($id)
		{
			$this->id = $id;
		}

        /**
         * Get the UserID
         *
         * Gets the unique ID of the User
         **/
        public function getID()
        {
            return $this->id;
        }

		public function setUsername($username)
		{
			$this->username = $username;
		}

        /**
         * Gets the Username of this User        
         **/
        public function getUsername()
        {
            return $this->username;
        }

        public function setEmail($email)
        {
            $this->email = $email;
        }

        public function getEmail()
        {
            return $this->email;
        }

        public function setPassword($password)
        {
            $this->password = $password;
        }

        /**
         * Gets the Hashed Password of this User
         *
         * Get the Hashed Password of this User-Object
         * The Password is hashed with SHA sha256 and the SALT of the Crypt-Enum
         **/
        public function getPassword()
        {
            return $this->password;
        }   

        public function setRankName($rankName)
        {
            $this->rankName = $rankName;
        }

        public function getRankName()
        {
            return $this->rankName;
        }

        public function setLastLogin($lastLogin)
        {
            $this->lastLogin = $lastLogin;
        }

        public function getLastLogin()
        {
            return $this->lastLogin;
        }

        public function setLastIP($lastIP)
        {
            $this->lastIP = $lastIP;
        }

        public function getLastIP()
        {
            return $this->lastIP;
        }

        public function setRegisterDate($registerDate)
        {
            $this->registerDate = $registerDate;
        }

        public function getRegisterDate()
        {
            return $this->registerDate;
        }

        public function isOnline()
        {
            return $this->online;
        }

        public function setProfilePicture($profilePicture)
        {
            $this->profilePicture = $profilePicture;
        }

        public function getProfilePicture()
        {
            return $this->profilePicture;
        }

        public function isBanned()
        {
            return $this->banned;
        }


        /**
         * Login the user with this username and password. 
         * If password is correct update LastLogin, LastIP and onlineState.     
         *
         * @param string username Username of the User
         * @param string password Password of the User
         *
         * @return User if login was succsessful else return null
         **/
        public static function Login($username, $password)
        {
            $user = User::getUser(strtolower($username));

            if($user == null) 
            {
                return null;
            }

            if (Crypt::hashStringWithSalt($password) == $user->getPassword()) 
            {
                $query = "UPDATE users SET lastLogin = NOW(), LastIP = '" . User::getIP() . "', online = '1' WHERE id = '" . $user->getID() . "'";
                $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

                return User::getUser(strtolower($username));
            }
        }

        public static function Logout($userSession)
        {
            $user = $userSession;

            $query = "UPDATE users SET online = '0' WHERE id = '" . $user->getID() . "'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
        }

        /**
         * Find the user with the given Username or UserID in the Database
         *
         * @param string/int $IDorUsername Username or userID         
         *
         * @return User returns a User if it exitsts else return null
         **/
        public static function getUser($ID_Username)
        {

            if (is_int($ID_Username)) 
            {
                 $query = "SELECT * FROM users WHERE id = '$ID_Username'";
            } 
            else 
            {
                $query = "SELECT * FROM users WHERE username = '$ID_Username'";
            }            
                    
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            if (!$res) 
            {
                return null;
            }

            $row = mysql_fetch_assoc($res);

            $id = $row['id'];
            $username = $row['username'];
            $email = $row['email'];
            $password = $row['password'];
            $rankName = $row['rankName'];
            $lastLogin = $row['lastLogin'];
            $lastIP = $row['lastIP'];
            $registerDate = $row['registerDate'];
            $online = $row['online'];
            $profilePicture = $row['profilePicture'];
            $banned = $row['banned'];

            return new User($id, $username, $email, $password, $rankName, $lastLogin, $lastIP, $registerDate, $online, $profilePicture, $banned);
        }

        public static function getMemberCount()
        {
            $query = "SELECT count(*) AS memberCount FROM users";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            $row = mysql_fetch_assoc($res);
            
            return $row['memberCount'];
        }

        public static function getAllUsers()
        {
            $query = "SELECT * FROM users";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            $users = new SplDoublyLinkedList();

            while($row = mysql_fetch_assoc($res))
            {
                $id = $row['id'];
                $username = $row['username'];
                $email = $row['email'];
                $password = $row['password'];
                $rankName = $row['rankName'];
                $lastLogin = $row['lastLogin'];
                $lastIP = $row['lastIP'];
                $registerDate = $row['registerDate'];
                $online = $row['online'];
                $profilePicture = $row['profilePicture'];
                $banned = $row['banned'];

                $users->push(new User($id, $username, $email, $password, $rankName, $lastLogin, $lastIP, $registerDate, $online, $profilePicture, $banned));        
            }

            return $users;
        }

        /**
         * Gets the IP of the current User who visit the Site
         *
         * @return string IP-Adress of current Client
         **/
        public static function getIP()
        {            
            if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) 
            {
                return $_SERVER['REMOTE_ADDR'];
            }
            else
            {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }  

        public static function ban($userID)
        {
            $query = "UPDATE users SET banned = '1' WHERE id = $userID";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
        }   

        public static function unban($userID)
        {
            $query = "UPDATE users SET banned = '0' WHERE id = $userID";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
        } 
    }   
?>