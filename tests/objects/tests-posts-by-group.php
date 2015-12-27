<?php
/**
 * Test saving/getting groups associated with posts
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_posts_by_group extends \WP_UnitTestCase {
	/**
	 * @var \WP_Post
	 */
	protected $the_post;

	/**
	 * @var \WP_Post
	 */
	protected $not_the_post;
	public function setUp(){
		parent::setUp();
		$id = $this->factory->post->create( array( 'post_title' => 'good' ) );
		$this->the_post = get_post( $id );
		$id = $this->factory->post->create( array( 'post_title' => 'not_good' ) );
		$this->not_the_post = get_post( $id );

	}

	/**
	 * Test that we can add one group to the association
	 *
	 * @since 1.1.0
	 *
	 * @group group
	 * @group objects
	 * @group posts_object
	 *
	 * @covers \ingot\testing\object\posts::add()
	 */
	public function testAddOneGroup(){

		$obj = new \ingot\testing\object\posts( $this->the_post );
		$obj->add( 7 );

		$this->assertEquals( $obj->get_groups(), get_post_meta( $this->the_post->ID, 'ingot_groups', true) );
		$this->assertNotEquals( $obj->get_groups(), get_post_meta( $this->not_the_post->ID, 'ingot_groups', true ) );

	}

	/**
	 * Test that we can add an array of group to the association
	 *
	 * @since 1.1.0
	 *
	 * @group group
	 * @group objects
	 * @group posts_object
	 *
	 * @covers \ingot\testing\object\posts::add()
	 */
	public function testAddGroups(){
		$groups = [1,3,7];
		$obj = new \ingot\testing\object\posts( $this->the_post );
		$obj->add( $groups );
		$this->assertEquals( $groups, $obj->get_groups() );
		$this->assertEquals( $obj->get_groups(), get_post_meta( $this->the_post->ID, 'ingot_groups', true ) );
		$this->assertNotEquals( $obj->get_groups(), get_post_meta( $this->not_the_post->ID, 'ingot_groups', true ) );


	}

	/**
	 * Test that we can add remove a group from the association
	 *
	 * @since 1.1.0
	 *
	 * @group group
	 * @group objects
	 * @group posts_object
	 *
	 * @covers \ingot\testing\object\posts::remove()
	 */
	public function testRemove(){

		$groups = [5,3,7];
		$obj = new \ingot\testing\object\posts( $this->the_post );
		$obj->add( $groups );
		$obj->remove( 5 );
		$expected = [3,7];
		$this->assertEquals( array_values( $expected ), array_values( $obj->get_groups() ) );
		$this->assertEquals( array_values( $obj->get_groups() ), array_values( get_post_meta( $this->the_post->ID, 'ingot_groups', true ) ) );

		$obj = new \ingot\testing\object\posts( $this->the_post );
		$this->assertEquals( array_values( $expected ), array_values( $obj->get_groups() ) );
	}

}
