<?php

/**
 * Class LP_Settings_Cache
 *
 * @author tungnx
 * @since 4.2.2
 * @version 1.0.1
 */
defined( 'ABSPATH' ) || exit();

class LP_Course_Review_Cache extends LP_Cache {
	/**
	 * @var string Key group child(external)
	 */
	protected $key_group_child = 'course-rating';

	public function __construct( $has_thim_cache = false ) {
		parent::__construct( $has_thim_cache );
	}

	/**
	 * Set rating.
	 *
	 * @param $course_id
	 * @param $rating
	 *
	 * @return void
	 */
	public function set_rating( $course_id, $rating ) {
		$this->set_cache( $course_id, $rating );

		$key_cache_first = "{$this->key_group}/{$course_id}";
		LP_Cache::cache_load_first( 'set', $key_cache_first, $rating );
	}

	/**
	 * Get rating.
	 *
	 * @param $course_id
	 *
	 * @return array|false|mixed|string
	 */
	public function get_rating( $course_id ) {
		$key_cache_first = "{$this->key_group}/{$course_id}";
		$total           = LP_Cache::cache_load_first( 'get', $key_cache_first );
		if ( false !== $total ) {
			return $total;
		}

		$total = $this->get_cache( $course_id );
		LP_Cache::cache_load_first( 'set', $key_cache_first, $total );

		return $total;
	}

	/**
	 * Clean cache rating
	 * And calculate average rating for course
	 *
	 * @param int $course_id
	 * @param int $user_id
	 *
	 * @return void
	 */
	public function clean_rating( int $course_id, int $user_id = 0 ) {
		$this->clear( $course_id );
		$key_cache_first = "{$this->key_group}/{$course_id}";
		LP_Cache::cache_load_first( 'clean', $key_cache_first );

		// Set average rating for course
		$rating = LP_Addon_Course_Review_Preload::$addon->get_rating_of_course( $course_id );
		LP_Addon_Course_Review::set_course_rating_average( $course_id, $rating['rated'] );
		$key_cache_review = "user/{$user_id}/course/{$course_id}/review";
		$this->clear( $key_cache_review );
	}
}
