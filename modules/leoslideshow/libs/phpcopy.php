<?php
/**
 *  Leo Theme for Prestashop 1.6.x
 *
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
 */

if (!class_exists('phpcopy')) {

    class PhpCopy
    {

        /**
         * @reference http://snipplr.com/view/74297/copy-files-and-folder-recursive/
         * copy and override
         */
        public static function cpy($source, $dest)
        {
            if (is_dir($source)) {
                $dir_handle = opendir($source);
                while ($file = readdir($dir_handle)) {
                    if ($file != "." && $file != "..") {
                        if (is_dir($source."/".$file)) {
                            if (!is_dir($dest."/".$file)) {
                                mkdir($dest."/".$file);
                            }
                            self::cpy($source."/".$file, $dest."/".$file);
                        }
                        else {
                            // if this is file then copy
                            copy($source."/".$file, $dest."/".$file);
                        }
                    }
                }
                closedir($dir_handle);
            }
            else {
                // if this is file then copy
                copy($source, $dest);
            }
        }

        /**
         * Copy but not override
         */
        public static function safeCopy($source, $dest)
        {
            if (is_dir($source)) {
                $dir_handle = opendir($source);
                while ($file = readdir($dir_handle)) {
                    if ($file != "." && $file != "..") {
                        if (is_dir($source."/".$file)) {
                            if (!is_dir($dest."/".$file)) {
                                mkdir($dest."/".$file);
                            }
                            self::safeCopy($source."/".$file, $dest."/".$file);
                        }
                        else {
                            if (is_file($dest."/".$file) === false) {
                                // if this is file then copy
                                copy($source."/".$file, $dest."/".$file);
                            }
                        }
                    }
                }
                closedir($dir_handle);
            }
            else {
                if (is_file($dest) === false) {
                    // if this is file then copy
                    // if the destination file already exists, it will NOT be overwritten.        
                    copy($source, $dest);
                }
            }
        }

        /**
         * create folder foler path : a/b/c/d
         * recursively create a long directory path
         */
        public static function createPath($path)
        {
            if (is_dir($path)) {
                # module validation
                return true;
            }
            $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1);
            $return = self::createPath($prev_path);
            return ($return && is_writable($prev_path)) ? mkdir($path) : false;
        }
    }
}
