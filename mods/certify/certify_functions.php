<?php

function getCertificateProgress($member_id, $certify_id) {

	global $db;
	
	$certificate = array();
	$progress = 0;

// Fetch associated tests

	$sql =  'SELECT '.TABLE_PREFIX.'certify_tests.*, '.TABLE_PREFIX.'tests.* FROM '.TABLE_PREFIX.'certify_tests ';
	$sql .= 'INNER JOIN '.TABLE_PREFIX.'tests ON '.TABLE_PREFIX.'certify_tests.test_id = '.TABLE_PREFIX.'tests.test_id ';
	$sql .= "WHERE ".TABLE_PREFIX."certify_tests.certify_id=".$certify_id;
	$result = mysql_query($sql, $db) or die(mysql_error() . " - " . $sql);
	
	$certificate['tests'] = array();
	while( $row = mysql_fetch_assoc($result) ) {
		$certificate['tests'][$row['test_id']] = array();
		$certificate['tests'][$row['test_id']]['passscore'] = $row['passscore'];
		$certificate['tests'][$row['test_id']]['passpercent'] = $row['passpercent'];
		$certificate['tests'][$row['test_id']]['out_of'] = $row['out_of'];
		$certificate['tests'][$row['test_id']]['final_score'] = 0;

		// Convert percent scored tests to scores
		if ($certificate['tests'][$row['test_id']]['passpercent'] > 0) {
			$certificate['tests'][$row['test_id']]['passscore'] = $certificate['tests'][$row['test_id']]['out_of'] * $certificate['tests'][$row['test_id']]['passpercent'] / 100;
			$certificate['tests'][$row['test_id']]['passpercent'] = 0;
		}
	}


// Calculate new scores for each test

	$sql =  '
		SELECT '.TABLE_PREFIX.'certify_tests.test_id, '.TABLE_PREFIX.'tests_results.* 
		FROM '.TABLE_PREFIX.'certify_tests
		RIGHT JOIN '.TABLE_PREFIX.'tests_results ON '.TABLE_PREFIX.'tests_results.test_id = '.TABLE_PREFIX.'certify_tests.test_id
		WHERE '.TABLE_PREFIX.'tests_results.member_id = '.$member_id.'
		AND '.TABLE_PREFIX.'certify_tests.certify_id = '.$certify_id.'
	';

	$result = mysql_query($sql, $db) or die(mysql_error() . "<br>" . $sql);
	//echo '<code style="background-color:white;"><pre>'.$sql.'</pre></code>';
	while( $row = mysql_fetch_assoc($result) ) {
		if (!isset($certificate['tests'][$row['test_id']]['final_score']) || $certificate['tests'][$row['test_id']]['final_score'] < $row['final_score'])
			$certificate['tests'][$row['test_id']]['final_score'] = $row['final_score'];

	}

// Calculate new percentages for certificate.


	$certificate['available_score'] = 0;
	$certificate['achieved_score'] = 0;

	if (isset($certificate['tests'])) {
		foreach ($certificate['tests'] as $certify_testid => &$test) {
			if ($test['final_score'] >= $test['passscore']) {
				$certificate['achieved_score'] += $test['passscore'];
			} else {
				$certificate['achieved_score'] += $test['final_score'];
			}
			$certificate['available_score'] += $test['passscore'];
			
			if (!isset($test['final_score'])) {
				
			}

		}
	}
	

	if ($certificate['available_score'] != 0) {
		$certificate['progress'] = $certificate['achieved_score'] * 100 / $certificate['available_score'];
	} else {
		$certificate['progress'] = 0;
	}

	return $certificate['progress'];
}






?>