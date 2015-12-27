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

	/**
	 * Test that we can overwrite assoiation
	 *
	 * @since 1.1.0
	 *
	 * @group group
	 * @group objects
	 * @group posts_object
	 *
	 * @covers \ingot\testing\object\posts::add()
	 */
	public function testOverwrite(){
		$groups = [5,3,7];
		$groups_2 = [9,4,7,12];
		$obj = new \ingot\testing\object\posts( $this->the_post );
		$obj->add( $groups );
		$obj->add( $groups_2, true );
		$this->assertEquals( array_values( $groups_2 ), array_values( $obj->get_groups() ) );

		$this->assertEquals( array_values( $groups_2 ), array_values( get_post_meta( $this->the_post->ID, 'ingot_groups', true ) ) );
	}

	/**
	 * Test that we can find the IDs from the shortcodes in post content
	 *
	 * @since 1.1.0
	 *
	 * @group group
	 * @group objects
	 * @group posts_object
	 *
	 * @covers ingot\testing\utility\posts::find_ids()
	 */
	public function testFindShortcodes() {
		$str = 'fdsjklsdfajkl [ingot id="7"] asdfghj  sdfghjj xfsd [ingot id="3"] sdf ';

		$found = \ingot\testing\utility\posts::find_ids( $str  );

		$this->assertSame( [7,3], $found );
	}

	/**
	 * Test that we can find the IDs from the shortcodes in post content
	 *
	 * @since 1.1.0
	 *
	 * @group group
	 * @group objects
	 * @group posts_object
	 *
	 * @covers ingot\testing\utility\posts::update_groups_in_post()
	 */
	public function testUpdatePost(){
		$id = $this->factory->post->create( [
			'post_title' => 'good',
			'post_content' => 'sfdjlakjsdf [ingot id="9"] sfdj fsdkdfs SDF657R542 ingot id="7" fsd [ingot id="42"][ingot id="11"]'
		]);
		$post = get_post( $id );
		\ingot\testing\utility\posts::update_groups_in_post( $post );
		$obj = new \ingot\testing\object\posts( get_post( $id ) );
		$expected = [9,42,11];
		$this->assertSame( $expected, $obj->get_groups() );
		$post->post_content = 'sfdjlakjsdf [ingot id="9"]';
		\ingot\testing\utility\posts::update_groups_in_post( $post );
		$obj = new \ingot\testing\object\posts( get_post( $id ) );
		$this->assertSame( [9], $obj->get_groups() );

	}

}
