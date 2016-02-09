<?php

$filename = '/etc/apache2/sites-available/000-default.conf';
$contents = file($filename);

$htaccessBlockStartingRegex = '/^<\/VirtualHost>$/';
$htaccessBlockEndingRegex = '/^# vim:/';

$newBlock = [
	'</VirtualHost>',
	'',
	'<Directory /var/www/html>',
	'Options Indexes FollowSymLinks',
	'AllowOverride All',
	'Require all granted',
	'</Directory>',
	'',
	'# vim: syntax=apache ts=4 sw=4 sts=4 sr noet'
];

$newContents = replaceDataBlockInStringArray($contents, $htaccessBlockStartingRegex, $htaccessBlockEndingRegex, $newBlock);

file_put_contents($filename, $newContents);

/**
 * Replaces the first matching data block in the array.
 *
 * @param $array string[]
 * @param $from string regex defining the starting point (inclusive) of the block
 * @param $to string regex defining the ending point (inclusive) of the block
 * @param $insertData string[] data to be inserted
 * @return string[] the new array
 */
function replaceDataBlockInStringArray($array, $from, $to, $insertData)
{
	$result = [];
	$cursorIsInBlock = false;
	$blockReplaced = false;
	$insertDataAdded = false;

	foreach ($array as $line) {

		if (preg_match($from, $line) === 1) {
			$cursorIsInBlock = true;
		}

		if (!$blockReplaced && $cursorIsInBlock) {

			if (!$insertDataAdded) {

				foreach ($insertData as $insertLine) {
					$result[] = $insertLine . PHP_EOL;
				}

				$insertDataAdded = true;
			}

			if (preg_match($to, $line) === 1) {
				$cursorIsInBlock = false;
				$blockReplaced = true;
			}

		} else {

			$result[] = $line;

		}

	}

	return $result;
}