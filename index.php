<?php
/**
 * Created by PhpStorm.
 * User: Bilal ATLI
 * Company: GARIVALDI - Digital Solutions
 * E-mail: bilal@garivaldi.com / ytbilalatli@gmail.com
 * Date: 06.08.2018
 * Phone: 0542-433-09-19
 * Time: 16:20
 */

require('GeneratePrime.php');

$GP = new GeneratePrime();

$info = $GP->generateBlock();

echo "<pre>";
var_dump($info);