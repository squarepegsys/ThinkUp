<?php
/**
 *
 * ThinkUp/webapp/plugins/googleplus/tests/TestOfFacebookCrawler.php
 *
 * Copyright (c) 2011 Henri Watson
 *
 * LICENSE:
 *
 * This file is part of ThinkUp (http://thinkupapp.com).
 *
 * ThinkUp is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any
 * later version.
 *
 * ThinkUp is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with ThinkUp.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 *
 * Test of GooglePlusCrawler
 *
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2011 Henri Watson
 */
require_once 'tests/init.tests.php';
require_once THINKUP_ROOT_PATH.'webapp/_lib/extlib/simpletest/autorun.php';
require_once THINKUP_ROOT_PATH.'webapp/_lib/extlib/simpletest/web_tester.php';
require_once THINKUP_ROOT_PATH.'webapp/plugins/googleplus/model/class.GooglePlusCrawler.php';
require_once THINKUP_ROOT_PATH.'webapp/plugins/googleplus/tests/classes/mock.GooglePlusAPIAccessor.php';
//require_once THINKUP_ROOT_PATH.'webapp/plugins/googleplus/tests/classes/mock.googleplus.php';

class TestOfGooglePlusCrawler extends ThinkUpUnitTestCase {
    /**
     *
     * @var Instance
     */
    var $instance;
    /**
     *
     * @var Logger
     */
    var $logger;

    public function setUp() {
        parent::setUp();
        $this->logger = Logger::getInstance();
        $r = array('id'=>1, 'network_username'=>'Gina Trapani', 'network_user_id'=>'113612142759476883204',
        'network_viewer_id'=>'113612142759476883204', 'last_post_id'=>'0', 'last_page_fetched_replies'=>0, 
        'last_page_fetched_tweets'=>'0', 'total_posts_in_system'=>'0', 'total_replies_in_system'=>'0', 
        'total_follows_in_system'=>'0', 'is_archive_loaded_replies'=>'0', 
        'is_archive_loaded_follows'=>'0', 'crawler_last_run'=>'', 'earliest_reply_in_system'=>'', 
        'avg_replies_per_day'=>'2', 'is_public'=>'0', 'is_active'=>'0', 'network'=>'google+',
        'last_favorite_id' => '0', 'last_unfav_page_checked' => '0', 'last_page_fetched_favorites' => '0',
        'owner_favs_in_system' => '0', 'total_posts_by_owner'=>0,
        'posts_per_day'=>1, 'posts_per_week'=>1, 'percentage_replies'=>50, 'percentage_links'=>50,
        'earliest_post_in_system'=>'01-01-2009', 'favorites_profile' => '0'
        );
        $this->profile1_instance = new Instance($r);
    }

    public function tearDown() {
        parent::tearDown();
        $this->logger->close();
    }

    public function testConstructor() {
        $gpc = new GooglePlusCrawler($this->profile1_instance, 'fauxaccesstoken', 10);
        $this->assertEqual($gpc->access_token, 'fauxaccesstoken');
    }

    public function testFetchUser() {
        $gpc = new GooglePlusCrawler($this->profile1_instance, 'fauxaccesstoken', 10);
        $gpc->fetchUser($this->profile1_instance->network_user_id, $this->profile1_instance->network, true);
        $user_dao = new UserMySQLDAO();
        $user = $user_dao->getUserByName('Gina Trapani', 'google+');

        $this->assertTrue(isset($user));
        $this->assertEqual($user->username, 'Gina Trapani');
        $this->assertEqual($user->full_name, 'Gina Trapani');
        $this->assertEqual($user->user_id, 1136121427);
        $this->assertEqual($user->location, "San Diego");
        $this->assertEqual($user->description,
        'ThinkUp lead developer, This Week in Google co-host, Todo.txt apps creator, founding editor of Lifehacker');
        $this->assertEqual($user->url, '');
        $this->assertFalse($user->is_protected);
    }

    public function testInitializeInstanceUserFreshToken() {
        $gpc = new GooglePlusCrawler($this->profile1_instance, 'faux-access-token', 10);
        $gpc->initializeInstanceUser('test_client_id', 'test_client_secret', 'valid_token', 'test_refresh_token', 1);
        $user_dao = new UserMySQLDAO();
        $user = $user_dao->getUserByName('Gina Trapani', 'google+');

        $this->assertTrue(isset($user));
        $this->assertEqual($user->username, 'Gina Trapani');
        $this->assertEqual($user->full_name, 'Gina Trapani');
        $this->assertEqual($user->user_id, '113612142759476883204');
        $this->assertEqual($user->location, "San Diego");
        $this->assertEqual($user->description,
        'ThinkUp lead developer, This Week in Google co-host, Todo.txt apps creator, founding editor of Lifehacker');
        $this->assertEqual($user->url, '');
        $this->assertFalse($user->is_protected);
    }

    public function testInitializeInstanceUserExpiredToken() {
        $gpc = new GooglePlusCrawler($this->profile1_instance, 'faux-expired-access-token', 10);
        $gpc->initializeInstanceUser('test_client_id', 'test_client_secret', 'valid_token', 'test_refresh_token', 1);

        $user_dao = new UserMySQLDAO();
        $user = $user_dao->getUserByName('Gina Trapani', 'google+');

        $this->assertTrue(isset($user));
        $this->assertEqual($user->username, 'Gina Trapani');
        $this->assertEqual($user->full_name, 'Gina Trapani');
        $this->assertEqual($user->user_id, '113612142759476883204');
        $this->assertEqual($user->location, "San Diego");
        $this->assertEqual($user->description,
        'ThinkUp lead developer, This Week in Google co-host, Todo.txt apps creator, founding editor of Lifehacker');
        $this->assertEqual($user->url, '');
        $this->assertFalse($user->is_protected);
    }

    public function testGetOAuthTokens() {
        $gpc = new GooglePlusCrawler($this->profile1_instance, 'fauxaccesstoken', 10);

        //test getting initial token
        $tokens = $gpc->getOAuthTokens('test-client-id', 'test-client-secret', 'test-code1', 'authorization_code');
        $this->assertEqual($tokens->access_token, 'faux-access-token');
        $this->assertEqual($tokens->refresh_token, 'faux-refresh-token');

        //test refreshing token
        $tokens = $gpc->getOAuthTokens('test-client-id', 'test-client-secret', 'test-refresh_token1',
        'refresh_token');
        $this->assertEqual($tokens->access_token, 'faux-access-token');
        $this->assertEqual($tokens->refresh_token, 'faux-refresh-token');
    }

    public function testFetchInstanceUserPosts() {
        $gpc = new GooglePlusCrawler($this->profile1_instance, 'fauxaccesstoken', 10);
        $gpc->fetchInstanceUserPosts();

        $post_dao = new PostMySQLDAO();
        $post = $post_dao->getPost('z12is5v4snurihgdl22iiz3pjrnws3lle', 'google+', true);
        $this->assertIsA($post, 'Post');
        $this->assertEqual($post->post_text,
        'I&#39;ve got a date with the G+ API this weekend to make a ThinkUp plugin!');
        $this->assertEqual($post->reply_count_cache, 24);
        $this->assertEqual($post->favlike_count_cache, 159);
        $this->assertEqual($post->retweet_count_cache, 29);
        $this->assertIsA($post->link, 'Link');
        $this->assertEqual($post->link->url,
        'http://googleplusplatform.blogspot.com/2011/09/getting-started-on-google-api.html');
        $this->assertEqual($post->link->title,
        'Getting Started on the Google+ API - Google+ Platform Blog');
        $this->assertEqual($post->link->description,
        'Official source of information about the Google+ platform');
        $this->assertEqual($post->link->image_src, '');

        //now crawl on updated data and assert counts get updated in database
        $gpc->api_accessor->setDataLocation('new_counts/');
        $gpc->fetchInstanceUserPosts();
        $post = $post_dao->getPost('z12is5v4snurihgdl22iiz3pjrnws3lle', 'google+', true);
        $this->assertEqual($post->reply_count_cache, 64);
        $this->assertEqual($post->favlike_count_cache, 199);
        $this->assertEqual($post->retweet_count_cache, 69);
    }
}
