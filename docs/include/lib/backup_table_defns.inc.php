<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }


/* content.csv */
	$fields = array();
	$fields[0] = array('content_id',		NUMBER);
	$fields[1] = array('content_parent_id', NUMBER);
	$fields[2] = array('ordering',			NUMBER);
	$fields[3] = array('last_modified',		TEXT);
	$fields[4] = array('revision',			NUMBER);
	$fields[5] = array('formatting',		NUMBER);
	$fields[6] = array('release_date',		TEXT);
	$fields[7] = array('keywords',			TEXT);
	$fields[8] = array('content_path',		TEXT);
	$fields[9] = array('title',				TEXT);
	$fields[10] = array('text',				TEXT);

	$backup_tables['content']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'content WHERE course_id='.$course.' ORDER BY content_parent_id, ordering';
	$backup_tables['content']['fields'] = $fields;

/* forums.csv */
	$fields = array();

	$fields[0] = array('title',			TEXT);
	$fields[1] = array('description',	TEXT);
	// three fields added for v1.4:
	$fields[2] = array('num_topics',	NUMBER);
	$fields[3] = array('num_posts',		NUMBER);
	$fields[4] = array('last_post',		TEXT);

	$backup_tables['forums']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'forums WHERE course_id='.$course.' ORDER BY forum_id';
	$backup_tables['forums']['fields'] = $fields;

/* related_content.csv */
	$fields = array();
	$fields[0] = array('content_id',			NUMBER);
	$fields[1] = array('related_content_id',	NUMBER);

	$backup_tables['related_content']['sql'] = 'SELECT R.content_id, R.related_content_id 
													FROM '.TABLE_PREFIX.'related_content R, '.TABLE_PREFIX.'content C 
													WHERE C.course_id='.$course.' AND R.content_id=C.content_id ORDER BY R.content_id ASC';
	$backup_tables['related_content']['fields'] = $fields;


/* glossary.csv */
	$fields = array();
	$fields[0] = array('word_id',			NUMBER);
	$fields[1] = array('word',				TEXT);
	$fields[2] = array('definition',		TEXT);
	$fields[3] = array('related_word_id',	NUMBER);

	$backup_tables['glossary']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'glossary WHERE course_id='.$course.' ORDER BY word_id ASC';
	$backup_tables['glossary']['fields'] = $fields;

/* resource_categories.csv */
	$fields = array();
	$fields[0] = array('CatID',		NUMBER);
	$fields[1] = array('CatName',	TEXT);
	$fields[2] = array('CatParent', NUMBER);

	$backup_tables['resource_categories']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'resource_categories WHERE course_id='.$course.' ORDER BY CatID ASC';
	$backup_tables['resource_categories']['fields'] = $fields;

/* resource_links.csv */
	$fields = array();
	$fields[0] = array('CatID',			NUMBER);
	$fields[1] = array('Url',			TEXT);
	$fields[2] = array('LinkName',		TEXT);
	$fields[3] = array('Description',	TEXT);
	$fields[4] = array('Approved',		NUMBER);
	$fields[5] = array('SubmitName',	TEXT);
	$fields[6] = array('SubmitEmail',	TEXT);
	$fields[7] = array('SubmitDate',	TEXT);
	$fields[8] = array('hits',			NUMBER);

	$backup_tables['resource_links']['sql'] = 'SELECT L.* FROM '.TABLE_PREFIX.'resource_links L, '.TABLE_PREFIX.'resource_categories C 
													WHERE C.course_id='.$course.' AND L.CatID=C.CatID 
													ORDER BY LinkID ASC';

	$backup_tables['resource_links']['fields'] = $fields;

/* news.csv */
	$fields = array();
	$fields[0] = array('date',		TEXT);
	$fields[1] = array('formatting',NUMBER);
	$fields[2] = array('title',		TEXT);
	$fields[3] = array('body',		TEXT);

	$backup_tables['news']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'news WHERE course_id='.$course.' ORDER BY news_id ASC';
	$backup_tables['news']['fields'] = $fields;
	
/* tests.csv */
	$fields = array();
	$fields[] = array('test_id',			NUMBER);
	$fields[] = array('title',				TEXT);
	$fields[] = array('format',				NUMBER);
	$fields[] = array('start_date',			TEXT);
	$fields[] = array('end_date',			TEXT);
	$fields[] = array('randomize_order',	NUMBER);
	$fields[] = array('num_questions',		NUMBER);
	$fields[] = array('instructions',		TEXT);

	/* four fields added for v1.4 */
	$fields[] = array('content_id',		NUMBER);
	$fields[] = array('automark',		NUMBER);
	$fields[] = array('random',			NUMBER);
	$fields[] = array('difficulty',		NUMBER);

	/* field added for v1.4.2 */
	$fields[] = array('num_takes',		NUMBER);
	$fields[] = array('anonymous',		NUMBER);

	$backup_tables['tests']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'tests WHERE course_id='.$course.' ORDER BY test_id ASC';
	$backup_tables['tests']['fields'] = $fields;

/* tests_questions.csv */
	$fields = array();
	$fields[] = array('test_id',			NUMBER);
	$fields[] = array('ordering',			NUMBER);
	$fields[] = array('type',				NUMBER);
	$fields[] = array('weight',				NUMBER);
	$fields[] = array('required',			NUMBER);
	$fields[] = array('feedback',			TEXT);
	$fields[] = array('question',			TEXT);
	$fields[] = array('choice_0',			TEXT);
	$fields[] = array('choice_1',			TEXT);
	$fields[] = array('choice_2',			TEXT);
	$fields[] = array('choice_3',			TEXT);
	$fields[] = array('choice_4',			TEXT);
	$fields[] = array('choice_5',			TEXT);
	$fields[] = array('choice_6',			TEXT);
	$fields[] = array('choice_7',			TEXT);
	$fields[] = array('choice_8',			TEXT);
	$fields[] = array('choice_9',			TEXT);
	$fields[] = array('answer_0',			NUMBER);
	$fields[] = array('answer_1',			NUMBER);
	$fields[] = array('answer_2',			NUMBER);
	$fields[] = array('answer_3',			NUMBER);
	$fields[] = array('answer_4',			NUMBER);
	$fields[] = array('answer_5',			NUMBER);
	$fields[] = array('answer_6',			NUMBER);
	$fields[] = array('answer_7',			NUMBER);
	$fields[] = array('answer_8',			NUMBER);
	$fields[] = array('answer_9',			NUMBER);
	$fields[] = array('answer_size',		NUMBER);
	$fields[] = array('content_id',			NUMBER);	/* one field added for v1.4 */

	$backup_tables['tests_questions']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'tests_questions WHERE course_id='.$course.' ORDER BY test_id ASC';
	$backup_tables['tests_questions']['fields'] = $fields;

/* polls.csv */
	$fields = array();
	$fields[0] = array('question',		TEXT);
	$fields[1] = array('created_date',	TEXT);
	$fields[2] = array('choice1',		TEXT);
	$fields[3] = array('choice2',		TEXT);
	$fields[4] = array('choice3',		TEXT);
	$fields[5] = array('choice4',		TEXT);
	$fields[6] = array('choice5',		TEXT);
	$fields[7] = array('choice6',		TEXT);
	$fields[8] = array('choice7',		TEXT);

	$backup_tables['polls']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'polls WHERE course_id='.$course;
	$backup_tables['polls']['fields'] = $fields;

/* course_stats.csv */
	$fields = array();
	$fields[0] = array('login_date',	TEXT);
	$fields[1] = array('guests',		NUMBER);
	$fields[2] = array('members',		NUMBER);

	$backup_tables['course_stats']['sql']    = 'SELECT * FROM '.TABLE_PREFIX.'course_stats WHERE course_id='.$course;
	$backup_tables['course_stats']['fields'] = $fields;

	unset($fields);

?>