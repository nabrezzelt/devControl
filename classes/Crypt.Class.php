<?php

    /**
     * 
     */
    class Crypt
    {        
        const SALT = "BugtrackerWithProjects";
        const HASH_ALGO = "sha256";

        public static function hashStringWithSalt($source) 
        {
            return hash(Crypt::HASH_ALGO, $source.Crypt::SALT);
        }
    }
     

?>