<?php

namespace Zork\Iterator;

use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * SortIteratorTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SortIteratorTest extends TestCase
{

    public $array = array(
        'img1.jpg'  => 'img1.jpg',
        'img10.jpg' => 'img10.jpg',
        'Img2.jpg'  => 'Img2.jpg',
        'Img11.jpg' => 'Img11.jpg',
    );

    /**
     * Test sort array
     */
    public function testSortArray()
    {
        $this->assertSame(
            array(
                'Img11.jpg' => 'Img11.jpg',
                'Img2.jpg'  => 'Img2.jpg',
                'img1.jpg'  => 'img1.jpg',
                'img10.jpg' => 'img10.jpg',
            ),
            iterator_to_array( new SortIterator( $this->array ) )
        );
    }

    /**
     * Test sort iterator
     */
    public function testSortIterator()
    {
        $this->assertSame(
            array(
                'Img11.jpg' => 'Img11.jpg',
                'Img2.jpg'  => 'Img2.jpg',
                'img1.jpg'  => 'img1.jpg',
                'img10.jpg' => 'img10.jpg',
            ),
            iterator_to_array( new SortIterator(
                new ArrayIterator( $this->array )
            ) )
        );
    }

    /**
     * Test sort compare: natural
     */
    public function testSortCmpNatural()
    {
        $this->assertSame(
            array(
                'Img2.jpg'  => 'Img2.jpg',
                'Img11.jpg' => 'Img11.jpg',
                'img1.jpg'  => 'img1.jpg',
                'img10.jpg' => 'img10.jpg',
            ),
            iterator_to_array( new SortIterator(
                $this->array,
                SortIterator::SORT_CMP_NATURAL
            ) )
        );
    }

    /**
     * Test sort compare: natural (case-insensitive)
     */
    public function testSortCmpNaturalCase()
    {
        $this->assertSame(
            array(
                'img1.jpg'  => 'img1.jpg',
                'Img2.jpg'  => 'Img2.jpg',
                'img10.jpg' => 'img10.jpg',
                'Img11.jpg' => 'Img11.jpg',
            ),
            iterator_to_array( new SortIterator(
                $this->array,
                SortIterator::SORT_CMP_NATURALCASE
            ) )
        );
    }

    /**
     * Test sort compare: custom
     */
    public function testSortCmpCustom()
    {
        $this->assertSame(
            array(
                'img1.jpg'  => 'img1.jpg',
                'Img2.jpg'  => 'Img2.jpg',
                'img10.jpg' => 'img10.jpg',
                'Img11.jpg' => 'Img11.jpg',
            ),
            iterator_to_array( new SortIterator(
                $this->array,
                'version_compare'
            ) )
        );
    }

    /**
     * Test sort by key
     */
    public function testSortByKey()
    {
        $this->assertSame(
            array(
                'Img11.jpg' => 'Img11.jpg',
                'Img2.jpg'  => 'Img2.jpg',
                'img1.jpg'  => 'img1.jpg',
                'img10.jpg' => 'img10.jpg',
            ),
            iterator_to_array( new SortIterator(
                $this->array,
                SortIterator::SORT_CMP_OPERATOR,
                SortIterator::SORT_BY_KEY
            ) )
        );
    }

    /**
     * Test sort compare: natural by key
     */
    public function testSortCmpNaturalByKey()
    {
        $this->assertSame(
            array(
                'Img2.jpg'  => 'Img2.jpg',
                'Img11.jpg' => 'Img11.jpg',
                'img1.jpg'  => 'img1.jpg',
                'img10.jpg' => 'img10.jpg',
            ),
            iterator_to_array( new SortIterator(
                $this->array,
                SortIterator::SORT_CMP_NATURAL,
                SortIterator::SORT_BY_KEY
            ) )
        );
    }

    /**
     * Test sort compare: natural (case-insensitive) by key
     */
    public function testSortCmpNaturalCaseByKey()
    {
        $this->assertSame(
            array(
                'img1.jpg'  => 'img1.jpg',
                'Img2.jpg'  => 'Img2.jpg',
                'img10.jpg' => 'img10.jpg',
                'Img11.jpg' => 'Img11.jpg',
            ),
            iterator_to_array( new SortIterator(
                $this->array,
                SortIterator::SORT_CMP_NATURALCASE,
                SortIterator::SORT_BY_KEY
            ) )
        );
    }

    /**
     * Test sort compare: custom by key
     */
    public function testSortCmpCustomByKey()
    {
        $this->assertSame(
            array(
                'img1.jpg'  => 'img1.jpg',
                'Img2.jpg'  => 'Img2.jpg',
                'img10.jpg' => 'img10.jpg',
                'Img11.jpg' => 'Img11.jpg',
            ),
            iterator_to_array( new SortIterator(
                $this->array,
                'version_compare',
                SortIterator::SORT_BY_KEY
            ) )
        );
    }

}
